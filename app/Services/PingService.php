<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use JJG\Ping as GeerlingPing;

class PingHelperService
{
    /**
     * Ping timeout in seconds
     */
    protected $timeout = 2;
    
    /**
     * Number of ping attempts
     */
    protected $attempts = 2;
    
    /**
     * Ping a single host and return result
     *
     * @param string $host Host to ping (IP or hostname)
     * @param int|null $timeout Timeout in seconds
     * @return array Ping result
     */
    public function ping($host, $timeout = null)
    {
        $timeout = $timeout ?? $this->timeout;
        
        try {
            // Create ping instance
            $ping = new GeerlingPing($host);
            $ping->setTimeout($timeout);
            
            // Perform ping
            $latency = $ping->ping();
            
            // Log method used by geerlingguy/ping for debugging
            Log::debug("Ping method used for {$host}: " . $ping->getPingMethodUsed());
            
            if ($latency !== false) {
                return [
                    'host' => $host,
                    'status' => $latency < 300 ? 'online' : 'slow',
                    'time' => $latency,
                    'error' => null,
                    'method' => $ping->getPingMethodUsed()
                ];
            } else {
                return [
                    'host' => $host,
                    'status' => 'offline',
                    'time' => null,
                    'error' => 'Host unreachable',
                    'method' => $ping->getPingMethodUsed()
                ];
            }
        } catch (\Exception $e) {
            Log::warning("Ping error for {$host}: " . $e->getMessage());
            
            // Fall back to socket ping if geerlingguy/ping fails
            try {
                $result = $this->socketPing($host);
                $result['fallback'] = true;
                return $result;
            } catch (\Exception $e2) {
                // If both methods fail, return error
                return [
                    'host' => $host,
                    'status' => 'error',
                    'time' => null,
                    'error' => $e->getMessage(),
                    'method' => 'failed'
                ];
            }
        }
    }
    
    /**
     * Ping multiple hosts and return results
     *
     * @param array $hosts Array of hosts to ping
     * @param int|null $timeout Timeout in seconds
     * @return array Results indexed by host
     */
    public function pingMultiple(array $hosts, $timeout = null)
    {
        $results = [];
        
        foreach ($hosts as $host) {
            // Extract IP from server object if needed
            $ip = is_array($host) ? $host['ip'] : $host;
            
            // Parse out port if present
            if (strpos($ip, ':') !== false) {
                list($ip, $port) = explode(':', $ip, 2);
            }
            
            // Skip if we already pinged this IP
            if (isset($results[$ip])) {
                continue;
            }
            
            $results[$ip] = $this->ping($ip, $timeout);
        }
        
        return $results;
    }
    
    /**
     * Ping hosts multiple times and average results
     * More reliable but takes longer
     *
     * @param array $hosts Array of hosts to ping
     * @param int|null $attempts Number of ping attempts per host
     * @param int|null $timeout Timeout in seconds
     * @return array Averaged results indexed by host
     */
    public function pingMultipleWithAverage(array $hosts, $attempts = null, $timeout = null)
    {
        $attempts = $attempts ?? $this->attempts;
        $results = [];
        
        foreach ($hosts as $host) {
            // Extract IP from server object if needed
            $ip = is_array($host) ? $host['ip'] : $host;
            
            // Parse out port if present
            if (strpos($ip, ':') !== false) {
                list($ip, $port) = explode(':', $ip, 2);
            }
            
            // Skip if we already pinged this IP
            if (isset($results[$ip])) {
                continue;
            }
            
            $pingResults = [];
            $successCount = 0;
            $totalTime = 0;
            
            // Perform multiple pings
            for ($i = 0; $i < $attempts; $i++) {
                $result = $this->ping($ip, $timeout);
                $pingResults[] = $result;
                
                if ($result['status'] === 'online' || $result['status'] === 'slow') {
                    $successCount++;
                    $totalTime += $result['time'];
                }
                
                // Small delay between pings
                if ($i < $attempts - 1) {
                    usleep(100000); // 100ms
                }
            }
            
            // Calculate average time
            $avgTime = $successCount > 0 ? round($totalTime / $successCount) : null;
            $reliability = $successCount > 0 ? round(($successCount / $attempts) * 100) : 0;
            
            // Determine overall status
            $status = 'offline';
            if ($reliability >= 50) {
                $status = $avgTime < 300 ? 'online' : 'slow';
            }
            
            $results[$ip] = [
                'host' => $ip,
                'status' => $status,
                'time' => $avgTime,
                'reliability' => $reliability,
                'success_count' => $successCount,
                'attempt_count' => $attempts,
                'details' => $pingResults
            ];
        }
        
        return $results;
    }
    
    /**
     * Run a pre-test on servers to find responsive ones
     *
     * @param array $servers Array of server objects with 'ip' key
     * @param int $minCount Minimum number of servers to return
     * @return array List of responsive servers
     */
    public function findResponsiveServers(array $servers, $minCount = 5)
    {
        // Get ping results
        $pingResults = $this->pingMultiple($servers);
        
        // Filter online servers
        $responsiveServers = [];
        foreach ($servers as $index => $server) {
            $ip = $server['ip'];
            
            // Parse out port if present
            if (strpos($ip, ':') !== false) {
                list($ip, $port) = explode(':', $ip, 2);
            }
            
            if (isset($pingResults[$ip]) && 
                ($pingResults[$ip]['status'] === 'online' || $pingResults[$ip]['status'] === 'slow')) {
                $server['ping_result'] = $pingResults[$ip];
                $responsiveServers[$index] = $server;
            }
        }
        
        // Sort by ping time
        uasort($responsiveServers, function($a, $b) {
            $timeA = $a['ping_result']['time'] ?? PHP_INT_MAX;
            $timeB = $b['ping_result']['time'] ?? PHP_INT_MAX;
            return $timeA <=> $timeB;
        });
        
        // If we don't have enough responsive servers, add some from original list
        if (count($responsiveServers) < $minCount) {
            // Get servers that haven't been tested yet
            $remainingServers = array_diff_key($servers, $responsiveServers);
            
            // Limit how many additional servers we'll try
            $additionalCount = min($minCount - count($responsiveServers), count($remainingServers));
            
            if ($additionalCount > 0) {
                // Slice the first N remaining servers
                $additionalServers = array_slice($remainingServers, 0, $additionalCount, true);
                
                // Add them to our responsive servers
                foreach ($additionalServers as $index => $server) {
                    $responsiveServers[$index] = $server;
                }
            }
        }
        
        return $responsiveServers;
    }
    
    /**
     * Perform TCP socket ping when geerlingguy/ping is unavailable
     *
     * @param string $host Host to ping
     * @param int $port Port to connect to (default: 80)
     * @param int|null $timeout Timeout in seconds
     * @return array Ping result
     */
    public function socketPing($host, $port = 80, $timeout = null)
    {
        $timeout = $timeout ?? $this->timeout;
        
        $startTime = microtime(true);
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        $endTime = microtime(true);
        
        if (!$socket) {
            // Try alternative port if default fails
            if ($port === 80) {
                // Try HTTPS port
                return $this->socketPing($host, 443, $timeout);
            }
            
            return [
                'host' => $host,
                'status' => 'offline',
                'time' => null,
                'error' => "$errstr ($errno)",
                'method' => 'socket_' . $port
            ];
        }
        
        fclose($socket);
        $responseTime = round(($endTime - $startTime) * 1000); // Convert to ms
        
        return [
            'host' => $host,
            'status' => $responseTime < 300 ? 'online' : 'slow',
            'time' => $responseTime,
            'error' => null,
            'method' => 'socket_' . $port
        ];
    }
    
    /**
     * Try to ping using the best available method
     * 
     * @param string $host Host to ping
     * @param int|null $timeout Timeout in seconds
     * @return array Ping result
     */
    public function bestEffortPing($host, $timeout = null)
    {
        // Try geerlingguy/ping first
        try {
            return $this->ping($host, $timeout);
        } catch (\Exception $e) {
            Log::info("Falling back to socket ping for {$host}: " . $e->getMessage());
            
            // Fall back to socket ping
            try {
                // Try HTTPS port first (443)
                $result = $this->socketPing($host, 443, $timeout);
                
                // If that fails, try HTTP port (80)
                if ($result['status'] === 'offline') {
                    $result = $this->socketPing($host, 80, $timeout);
                }
                
                return $result;
            } catch (\Exception $e2) {
                Log::warning("All ping methods failed for {$host}: " . $e2->getMessage());
                
                return [
                    'host' => $host,
                    'status' => 'error',
                    'time' => null,
                    'error' => "All ping methods failed: " . $e2->getMessage(),
                    'method' => 'all_failed'
                ];
            }
        }
    }
    
    /**
     * Fallback ping implementation using DNS lookup time
     * Used when other methods fail
     * 
     * @param string $host Host to ping
     * @return array Ping result
     */
    public function dnsPing($host)
    {
        $startTime = microtime(true);
        $ip = gethostbyname($host);
        $endTime = microtime(true);
        
        $resolved = ($ip !== $host); // If lookup fails, gethostbyname returns the input
        $responseTime = round(($endTime - $startTime) * 1000); // Convert to ms
        
        return [
            'host' => $host,
            'status' => $resolved ? 'online' : 'offline',
            'time' => $responseTime,
            'ip' => $resolved ? $ip : null,
            'error' => $resolved ? null : 'DNS resolution failed',
            'method' => 'dns_lookup'
        ];
    }
    
    /**
     * Get reliability score for a list of servers based on ping results
     * Useful for ranking servers by overall connectivity
     * 
     * @param array $pingResults Results from pingMultiple
     * @return array Server scores (higher is better)
     */
    public function calculateServerScores(array $pingResults)
    {
        $scores = [];
        
        foreach ($pingResults as $ip => $result) {
            // Base score calculation
            $score = 0;
            
            if ($result['status'] === 'online') {
                // Start with 100 points for online
                $score = 100;
                
                // Subtract points for high latency (0-50 points deduction)
                if ($result['time'] !== null) {
                    // Lower is better, so deduct points for higher times
                    // 0ms = 0 deduction, 500ms = 50 deduction
                    $latencyDeduction = min(50, $result['time'] / 10);
                    $score -= $latencyDeduction;
                }
            } elseif ($result['status'] === 'slow') {
                // Start with 50 points for slow
                $score = 50;
                
                // Adjust based on actual time
                if ($result['time'] !== null) {
                    // For slow servers, deduct more for higher latency
                    // 300ms = 0 deduction, 1000ms = 30 deduction
                    $latencyDeduction = min(30, max(0, $result['time'] - 300) / 23.3);
                    $score -= $latencyDeduction;
                }
            } else {
                // Offline or error gets 0 points
                $score = 0;
            }
            
            // Add small bonus for socket_create method (usually more reliable)
            if (isset($result['method']) && $result['method'] === 'socket_create') {
                $score += 5;
            }
            
            // Cap score at 100
            $scores[$ip] = min(100, $score);
        }
        
        // Sort by score (highest first)
        arsort($scores);
        
        return $scores;
    }
}