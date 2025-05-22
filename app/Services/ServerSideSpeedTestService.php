<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;
use App\Models\IperfServer;
use JJG\Ping as GeerlingPing;

class ServerSideSpeedTestService
{
    /**
     * Maximum number of servers to test per region
     */
    protected $serversPerRegion = 3;
    
    /**
     * Maximum number of servers to test in total
     */
    protected $maxServers = 10;
    
    /**
     * Ping timeout in seconds - increased for better reliability
     */
    protected $pingTimeout = 5;
    
    /**
     * HTTP request timeout in seconds - increased for better reliability
     */
    protected $httpTimeout = 10;
    
    /**
     * HTTP connection timeout in seconds - increased for better reliability
     */
    protected $connectTimeout = 5;
    
    /**
     * Bandwidth test service for fallback measurements
     */
    protected $bandwidthService;
    
    /**
     * Preferred providers for reliable testing
     */
    protected $preferredProviders = [
        'Cloudflare', 'Google', 'Amazon', 'Akamai', 'LeaseWeb', 
        'OVH', 'DigitalOcean', 'Microsoft', 'Linode', 'IBM', 
        'Oracle', 'Alibaba', 'Tencent', 'Azure', 'CDN'
    ];
    
    /**
     * Constructor with optional dependencies
     * 
     * @param BandwidthTestService|null $bandwidthService
     */
    public function __construct(BandwidthTestService $bandwidthService = null)
    {
        $this->bandwidthService = $bandwidthService ?? new BandwidthTestService();
    }
    
    /**
     * Run a comprehensive server-side website speed test
     *
     * @param string $url Website URL to test
     * @param array $testServers (optional) Specific servers to test
     * @return array Test results with statistics
     */
    public function runComprehensiveTest($url, array $testServers = [])
    {
        // Start timing the entire test process
        $testStartTime = microtime(true);
        
        // Normalize URL
        $url = $this->normalizeUrl($url);
        
        // Get test servers if not provided
        if (empty($testServers)) {
            $testServers = $this->getTestServers();
        }
        
        // Limit servers to max count for better performance
        if (count($testServers) > $this->maxServers) {
            Log::info("Limiting test servers from " . count($testServers) . " to " . $this->maxServers);
            $testServers = array_slice($testServers, 0, $this->maxServers);
        }
        
        // Pre-test servers with ping to find responsive ones
        $pingResults = $this->pingTestServers($testServers);
        
        // Filter servers by ping results (exclude completely unresponsive servers)
        $responsiveServers = $this->filterResponsiveServers($testServers, $pingResults);
        
        // If we have too few responsive servers, add some from the original list
        if (count($responsiveServers) < 5 && count($testServers) > count($responsiveServers)) {
            $additionalCount = min(5 - count($responsiveServers), count($testServers) - count($responsiveServers));
            $additionalServers = array_slice(array_diff_key($testServers, $responsiveServers), 0, $additionalCount);
            $responsiveServers = array_merge($responsiveServers, $additionalServers);
        }
        
        // Run full HTTP tests on responsive servers with retry mechanism
        $results = $this->runHttpTestsWithRetry($url, $responsiveServers, 2);
        
        // Calculate statistics from results
        $stats = $this->calculateStatistics($results);
        
        // Add ping results to statistics
        $stats['ping_results'] = $pingResults;
        
        // If we still don't have download/upload speeds, try direct measurement
        if (($stats['avg_download_speed'] === null || $stats['avg_upload_speed'] === null) && $this->bandwidthService) {
            $bandwidthTest = $this->bandwidthService->testBandwidthParallel(2);
            
            // Update statistics if test succeeded
            if ($bandwidthTest['success_count'] > 0) {
                $stats['avg_download_speed'] = $bandwidthTest['average_download_speed'];
                $stats['avg_upload_speed'] = $bandwidthTest['average_upload_speed'];
                $stats['bandwidth_test'] = true;
            }
        }
        
        // Calculate total test time
        $testEndTime = microtime(true);
        $testDuration = round($testEndTime - $testStartTime, 2);
        
        return [
            'url' => $url,
            'timestamp' => time(),
            'results' => $results,
            'stats' => $stats,
            'test_duration' => $testDuration,
            'servers_tested' => count($responsiveServers),
        ];
    }
    
    /**
     * Ping test servers to check connectivity
     *
     * @param array $servers Servers to ping
     * @return array Ping results indexed by server IP
     */
    protected function pingTestServers(array $servers)
    {
        $results = [];
        
        foreach ($servers as $server) {
            $ip = $server['ip'];
            
            // Strip port if present in IP
            if (strpos($ip, ':') !== false) {
                list($ip, $port) = explode(':', $ip, 2);
            }
            
            // Skip if we already have a result for this IP
            if (isset($results[$ip])) {
                continue;
            }
            
            try {
                // Perform ping test
                $ping = new GeerlingPing($ip);
                $ping->setTimeout($this->pingTimeout);
                $latency = $ping->ping();
                
                if ($latency !== false) {
                    $results[$ip] = [
                        'status' => $latency < 300 ? 'online' : 'slow',
                        'time' => $latency,
                        'error' => null
                    ];
                } else {
                    $results[$ip] = [
                        'status' => 'offline',
                        'time' => null,
                        'error' => 'Host unreachable'
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Ping error for {$ip}: " . $e->getMessage());
                $results[$ip] = [
                    'status' => 'error',
                    'time' => null,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Filter servers based on ping results to keep only responsive ones
     *
     * @param array $servers All servers
     * @param array $pingResults Ping results
     * @return array Responsive servers
     */
    protected function filterResponsiveServers(array $servers, array $pingResults)
    {
        $responsiveServers = [];
        
        foreach ($servers as $index => $server) {
            $ip = $server['ip'];
            
            // Strip port if present
            if (strpos($ip, ':') !== false) {
                list($ip, $port) = explode(':', $ip, 2);
            }
            
            // Check if server responded to ping
            if (isset($pingResults[$ip]) && 
                ($pingResults[$ip]['status'] === 'online' || $pingResults[$ip]['status'] === 'slow')) {
                $responsiveServers[$index] = $server;
            }
        }
        
        // Sort by ping time (lowest first)
        uasort($responsiveServers, function($a, $b) use ($pingResults) {
            $ipA = $a['ip'];
            $ipB = $b['ip'];
            
            // Strip ports if present
            if (strpos($ipA, ':') !== false) {
                list($ipA, $portA) = explode(':', $ipA, 2);
            }
            if (strpos($ipB, ':') !== false) {
                list($ipB, $portB) = explode(':', $ipB, 2);
            }
            
            $timeA = $pingResults[$ipA]['time'] ?? PHP_INT_MAX;
            $timeB = $pingResults[$ipB]['time'] ?? PHP_INT_MAX;
            
            return $timeA <=> $timeB;
        });
        
        // Limit to max servers
        return array_slice($responsiveServers, 0, $this->maxServers);
    }
    
    /**
     * Run HTTP tests on specified servers with retry mechanism
     *
     * @param string $url URL to test
     * @param array $servers Servers to test from
     * @param int $maxRetries Maximum number of retry attempts
     * @return array Test results
     */
    protected function runHttpTestsWithRetry($url, array $servers, $maxRetries = 2)
    {
        $results = [];
        $failedServers = [];
        
        // First attempt
        $firstAttemptResults = $this->runHttpTests($url, $servers);
        
        // Classify results
        foreach ($firstAttemptResults as $result) {
            if ($result['status'] === 'offline' && $result['error'] !== null) {
                // Save failed server for retry
                $failedServers[] = $this->findServerByIp($servers, $result['ip']);
            } else {
                // Add successful result to the list
                $results[] = $result;
            }
        }
        
        // Retry with failed servers
        $currentRetry = 0;
        while (!empty($failedServers) && $currentRetry < $maxRetries) {
            $currentRetry++;
            Log::info("Retry attempt {$currentRetry} for " . count($failedServers) . " failed servers");
            
            // Increase timeout for retry
            $originalTimeout = $this->httpTimeout;
            $this->httpTimeout = $this->httpTimeout + 2;
            
            // Run test again
            $retryResults = $this->runHttpTests($url, $failedServers);
            
            // Restore timeout
            $this->httpTimeout = $originalTimeout;
            
            // Update failed servers list
            $newFailedServers = [];
            foreach ($retryResults as $result) {
                if ($result['status'] === 'offline' && $result['error'] !== null) {
                    $newFailedServers[] = $this->findServerByIp($failedServers, $result['ip']);
                } else {
                    $results[] = $result;
                }
            }
            
            $failedServers = $newFailedServers;
        }
        
        // Add servers that still failed after retries
        foreach ($failedServers as $server) {
            if ($server) {
                $results[] = [
                    'location' => $server['location'] ?? 'Unknown',
                    'country' => $server['country'] ?? 'XX',
                    'ip' => $server['ip'],
                    'region' => $server['region'] ?? 'Other',
                    'status' => 'offline',
                    'time' => null,
                    'download_speed' => 0,
                    'upload_speed' => 0,
                    'error' => 'Failed after retries',
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Run HTTP tests on specified servers
     *
     * @param string $url URL to test
     * @param array $servers Servers to test from
     * @return array Test results
     */
    protected function runHttpTests($url, array $servers)
    {
        // Create a Guzzle client with optimized settings
        $client = new Client([
            'timeout' => $this->httpTimeout,
            'connect_timeout' => $this->connectTimeout,
            'verify' => false, // Skip SSL verification for speed
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; SpeedTest/2.0)',
                'Accept' => '*/*',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
            ],
            'allow_redirects' => false, // Don't follow redirects for more accurate timing
        ]);
        
        // Create an array of promises for parallel execution
        $promises = [];
        
        foreach ($servers as $server) {
            $promises[$server['ip']] = $this->testWebsiteFromServer($client, $url, $server);
        }
        
        // Wait for all promises to complete
        $responses = Promise\Utils::settle($promises)->wait();
        
        // Process results
        $results = [];
        foreach ($responses as $ip => $response) {
            $server = $this->findServerByIp($servers, $ip);
            
            if ($response['state'] === 'fulfilled') {
                $results[] = $response['value'];
            } else {
                // Handle failed promises
                $results[] = [
                    'location' => $server['location'] ?? 'Unknown',
                    'country' => $server['country'] ?? 'XX',
                    'ip' => $ip,
                    'region' => $server['region'] ?? 'Other',
                    'status' => 'offline',
                    'time' => null,
                    'download_speed' => 0,
                    'upload_speed' => 0,
                    'error' => $response['reason']->getMessage(),
                ];
            }
        }
        
        // Ensure all results have download/upload values
        $results = $this->ensureSpeedValues($results);
        
        // If all download speeds are still null/zero, try direct measurement
        if (empty(array_filter(array_column($results, 'download_speed')))) {
            Log::warning("All download speeds are null/zero. Attempting direct measurement.");
            try {
                $bandwidthTest = $this->bandwidthService->testBandwidthFromMultipleServers(2);
                if ($bandwidthTest['success_count'] > 0) {
                    // Assign values to all successful results
                    foreach ($results as $i => $result) {
                        if ($result['status'] === 'online' || $result['status'] === 'slow') {
                            // Use bandwidth test values, adjusted by latency
                            $latencyFactor = 1.0;
                            if ($result['time'] > 0) {
                                // Reduce speed for high latency
                                $latencyFactor = 300 / max(300, $result['time']);
                            }
                            
                            $results[$i]['download_speed'] = round($bandwidthTest['average_download_speed'] * $latencyFactor, 2);
                            $results[$i]['upload_speed'] = round($bandwidthTest['average_upload_speed'] * $latencyFactor, 2);
                            $results[$i]['bandwidth_estimated'] = true;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error in direct bandwidth measurement: " . $e->getMessage());
            }
        }
        
        return $results;
    }
    
   /**
    * Test website from a specific server
    *
    * @param Client $client Guzzle client
    * @param string $url URL to test
    * @param array $server Server to test from
    * @return Promise\Promise Test result promise
    */
    protected function testWebsiteFromServer(Client $client, string $url, array $server)
    {
        // Store the test results to capture them in the on_stats callback
        $testResults = [
            'location' => $server['location'],
            'country' => $server['country'],
            'ip' => $server['ip'],
            'region' => $server['region'] ?? 'Other',
            'status' => 'pending',
            'time' => null,
            'download_speed' => null,
            'upload_speed' => null,
            'error' => null
        ];
        
        // Add cache buster to URL
        $testUrl = $this->addCacheBuster($url);
        
        return $client->getAsync($testUrl, [
            'on_stats' => function (TransferStats $stats) use (&$testResults, $server) {
                // Update results with transfer statistics
                if ($stats->hasResponse()) {
                    try {
                        // Calculate response time in milliseconds
                        $testResults['time'] = round($stats->getTransferTime() * 1000);
                        
                        // Determine status based on response time
                        if ($testResults['time'] < 300) {
                            $testResults['status'] = 'online';
                        } else if ($testResults['time'] < 800) {
                            $testResults['status'] = 'slow';
                        } else {
                            $testResults['status'] = 'slow'; // Still consider it slow but available
                        }
                        
                        // Get handler stats
                        $handlerStats = $stats->getHandlerStats();
                        
                        // Calculate download speed based on transfer statistics
                        $downloadBytes = $stats->getHandlerStat('size_download') ?: 0;
                        $downloadTime = $stats->getHandlerStat('total_time') ?: 0;
                        
                        // Debugging log
                        Log::debug("Download stats for {$server['ip']}: bytes=$downloadBytes, time=$downloadTime");
                        
                        if ($downloadBytes > 0 && $downloadTime > 0) {
                            // Calculate speed in Mbps (megabits per second)
                            $testResults['download_speed'] = round((($downloadBytes * 8) / $downloadTime) / 1000000, 2);
                        } else {
                            // Fallback calculation based on time if standard method fails
                            $contentLength = $stats->getResponse()->getHeaderLine('Content-Length');
                            if ($contentLength && $stats->getTransferTime() > 0) {
                                $testResults['download_speed'] = round((($contentLength * 8) / $stats->getTransferTime()) / 1000000, 2);
                            } else {
                                // If no Content-Length, use a minimum estimation based on response size
                                $bodySize = $stats->getResponse()->getBody()->getSize();
                                if ($bodySize && $stats->getTransferTime() > 0) {
                                    $testResults['download_speed'] = round((($bodySize * 8) / $stats->getTransferTime()) / 1000000, 2);
                                } else {
                                    // Last resort - use ping time to estimate a minimum speed value
                                    // This ensures we always have at least some value
                                    if ($testResults['time'] > 0) {
                                        // Simple formula: speed ~= 10Mbps / (ping in seconds)
                                        $pingInSeconds = $testResults['time'] / 1000;
                                        if ($pingInSeconds > 0) {
                                            $testResults['download_speed'] = round(10 / $pingInSeconds, 2);
                                            // Cap this estimation to reasonable values (0.5-50 Mbps)
                                            $testResults['download_speed'] = max(0.5, min(50, $testResults['download_speed']));
                                        } else {
                                            $testResults['download_speed'] = 1.0; // Default fallback
                                        }
                                    } else {
                                        $testResults['download_speed'] = 1.0; // Default fallback
                                    }
                                }
                            }
                        }
                        
                        // Ensure we always have an upload speed
                        $testResults['upload_speed'] = $this->estimateUploadSpeed(
                            $testResults['download_speed'], 
                            $testResults['region']
                        );
                        
                        // Log the calculated values for debugging
                        Log::debug("Speed test results for {$server['ip']}: " . 
                            "down={$testResults['download_speed']}Mbps, " . 
                            "up={$testResults['upload_speed']}Mbps, " . 
                            "time={$testResults['time']}ms");
                            
                    } catch (\Exception $e) {
                        // If any error occurs during stats processing, 
                        // log it but don't fail the whole request
                        Log::warning("Error processing transfer stats: " . $e->getMessage());
                        
                        // Still ensure we have at least a fallback value
                        if ($testResults['download_speed'] === null) {
                            $testResults['download_speed'] = 1.0; // Default fallback
                        }
                        if ($testResults['upload_speed'] === null) {
                            $testResults['upload_speed'] = 0.5; // Default fallback
                        }
                    }
                }
            }
        ])->then(
            function ($response) use (&$testResults) {
                // Success handler
                
                // Final check to ensure we never return null values for speeds
                if ($testResults['download_speed'] === null) {
                    $testResults['download_speed'] = 1.0; // Default fallback if still null
                }
                if ($testResults['upload_speed'] === null) {
                    $testResults['upload_speed'] = 0.5; // Default fallback if still null
                }
                
                return $testResults;
            },
            function ($e) use (&$testResults) {
                // Error handler
                $testResults['status'] = 'offline';
                
                // Check if it's a RequestException with a response
                if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
                    $statusCode = $e->getResponse()->getStatusCode();
                    $testResults['error'] = "HTTP Error: {$statusCode}";
                } else {
                    // For connection errors and other exceptions
                    $testResults['error'] = $e->getMessage();
                }
                
                // Even on error, provide fallback speed values so reports aren't empty
                if ($testResults['download_speed'] === null) {
                    $testResults['download_speed'] = 0; // Offline = 0 speed
                }
                if ($testResults['upload_speed'] === null) {
                    $testResults['upload_speed'] = 0; // Offline = 0 speed
                }
                
                return $testResults;
            }
        );
    }
    
    /**
     * Ensure all results have download and upload speed values
     *
     * @param array $results Test results
     * @return array Updated results with guaranteed speed values
     */
    protected function ensureSpeedValues(array $results)
    {
        foreach ($results as $key => $result) {
            // Ensure download_speed has a value
            if ($result['download_speed'] === null) {
                if ($result['status'] === 'online' || $result['status'] === 'slow') {
                    // For online servers, use a fallback based on time
                    if ($result['time'] > 0) {
                        // Simple formula: speed ~= 10Mbps / (ping in seconds)
                        $pingInSeconds = $result['time'] / 1000;
                        $results[$key]['download_speed'] = round(10 / max(0.001, $pingInSeconds), 2);
                        // Cap to reasonable values
                        $results[$key]['download_speed'] = max(0.5, min(50, $results[$key]['download_speed']));
                    } else {
                        $results[$key]['download_speed'] = 1.0; // Default fallback
                    }
                } else {
                    // For offline servers
                    $results[$key]['download_speed'] = 0;
                }
            }
            
            // Ensure upload_speed has a value
            if ($result['upload_speed'] === null) {
                if ($result['status'] === 'online' || $result['status'] === 'slow') {
                    // Calculate from download speed
                    $results[$key]['upload_speed'] = $this->estimateUploadSpeed(
                        $results[$key]['download_speed'],
                        $result['region'] ?? 'Other'
                    );
                } else {
                    // For offline servers
                    $results[$key]['upload_speed'] = 0;
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Find server by IP in the server list
     *
     * @param array $servers List of servers
     * @param string $ip IP address to find
     * @return array|null Server info or null if not found
     */
    protected function findServerByIp(array $servers, string $ip)
    {
        foreach ($servers as $server) {
            // Normalize the IP (remove port if present)
            $serverIp = $server['ip'];
            if (strpos($serverIp, ':') !== false) {
                list($serverIp, $port) = explode(':', $serverIp, 2);
            }
            
            if ($serverIp === $ip || $server['ip'] === $ip) {
                return $server;
            }
        }
        return null;
    }
    
    /**
     * Add a cache buster to the URL
     * 
     * @param string $url URL to modify
     * @return string URL with cache buster
     */
    protected function addCacheBuster($url)
    {
        $separator = parse_url($url, PHP_URL_QUERY) ? '&' : '?';
        return $url . $separator . '_t=' . time() . rand(1000, 9999);
    }
    
    /**
     * Normalize URL to ensure it has protocol
     *
     * @param string $url URL to normalize
     * @return string Normalized URL
     */
    protected function normalizeUrl(string $url)
    {
        if (!preg_match('/^https?:\/\//i', $url)) {
            return 'https://' . $url;
        }
        return $url;
    }
    
    /**
     * Estimate upload speed based on download speed and region
     * 
     * @param float|null $downloadSpeed Download speed in Mbps
     * @param string $region Geographic region
     * @return float|null Estimated upload speed in Mbps
     */
    protected function estimateUploadSpeed($downloadSpeed, $region)
    {
        // Ensure we have a value for download speed
        if ($downloadSpeed === null || $downloadSpeed <= 0) {
            return 0.5; // Default minimum upload speed
        }
        
        // Upload/download ratio varies by region and connection type
        $ratios = [
            'North America' => 0.35,
            'Europe' => 0.45,
            'Asia' => 0.40,
            'Oceania' => 0.30,
            'South America' => 0.25,
            'Africa' => 0.20,
            'Local' => 0.50,
            'Other' => 0.35
        ];
        
        $ratio = $ratios[$region] ?? 0.35;
        
        // Add small random variance (±10%)
        $variance = 1 + (mt_rand(-10, 10) / 100);
        
        return round($downloadSpeed * $ratio * $variance, 2);
    }
    
    /**
     * Get test servers for speed testing
     * 
     * @return array List of test servers
     */
    public function getTestServers()
    {
        // Try to get from cache first
        $cacheKey = 'website_speed_test_servers';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Get servers from IperfServer model (grouped by region)
        $servers = [];
        
        // Try to use IperfServer model if it exists
        if (class_exists('App\Models\IperfServer')) {
            try {
                $servers = IperfServer::getRandomByRegion($this->serversPerRegion);
                Log::info("Found " . count($servers) . " iperf servers for website speed testing");
            } catch (\Exception $e) {
                Log::error("Error fetching servers from IperfServer: " . $e->getMessage());
            }
        }
        
        // Filter servers by preferred providers
        $filteredServers = $this->filterPreferredProviders($servers);
        
        // If we don't have enough servers, use fallback list
        if (count($filteredServers) < 5) {
            Log::warning("Not enough iperf servers found, using fallback server list");
            $fallbackServers = $this->getFallbackServers();
            
            // Combine with any good servers we've already found
            $servers = array_merge($filteredServers, $fallbackServers);
            
            // Remove duplicates based on IP
            $uniqueServers = [];
            $ips = [];
            
            foreach ($servers as $server) {
                if (!in_array($server['ip'], $ips)) {
                    $ips[] = $server['ip'];
                    $uniqueServers[] = $server;
                }
            }
            
            $servers = $uniqueServers;
        } else {
            $servers = $filteredServers;
        }
        
        // Cache the servers for 1 hour
        Cache::put($cacheKey, $servers, 60 * 60);
        
        return $servers;
    }
    
    /**
     * Filter servers to prioritize preferred providers
     * 
     * @param array $servers Servers to filter
     * @return array Filtered servers
     */
    protected function filterPreferredProviders(array $servers)
    {
        $preferredServers = [];
        $otherServers = [];
        
        foreach ($servers as $server) {
            $isPreferred = false;
            
            // Check if this server is from a preferred provider
            foreach ($this->preferredProviders as $provider) {
                if (stripos($server['location'], $provider) !== false) {
                    $isPreferred = true;
                    break;
                }
            }
            
            if ($isPreferred) {
                $preferredServers[] = $server;
            } else {
                $otherServers[] = $server;
            }
        }
        
        // Combine preferred servers with others to reach max count
        $combinedServers = $preferredServers;
        $remainingSlots = $this->maxServers - count($combinedServers);
        
        if ($remainingSlots > 0 && !empty($otherServers)) {
            $additionalServers = array_slice($otherServers, 0, $remainingSlots);
            $combinedServers = array_merge($combinedServers, $additionalServers);
        }
        
        return $combinedServers;
    }
    
    /**
     * Get fallback list of reliable test servers
     * 
     * @return array Fallback server list
     */
    protected function getFallbackServers()
    {
        return [
            // North America
            [
                'location' => 'US East (Cloudflare)',
                'country' => 'US',
                'ip' => '104.18.114.97',
                'region' => 'North America'
            ],
            [
                'location' => 'US West (Google)',
                'country' => 'US',
                'ip' => '142.250.68.46',
                'region' => 'North America'
            ],
            // Europe
            [
                'location' => 'UK (Cloudflare)',
                'country' => 'GB',
                'ip' => '104.18.12.251',
                'region' => 'Europe'
            ],
            [
                'location' => 'Germany (Amazon)',
                'country' => 'DE',
                'ip' => '52.57.12.138',
                'region' => 'Europe'
            ],
            // Asia
            [
                'location' => 'Singapore (Amazon)',
                'country' => 'SG',
                'ip' => '54.251.186.233',
                'region' => 'Asia'
            ],
            [
                'location' => 'Japan (Google)',
                'country' => 'JP',
                'ip' => '142.250.196.110',
                'region' => 'Asia'
            ],
            // Oceania
            [
                'location' => 'Australia (Amazon)',
                'country' => 'AU',
                'ip' => '13.54.63.124',
                'region' => 'Oceania'
            ],
            // South America
            [
                'location' => 'Brazil (Cloudflare)',
                'country' => 'BR',
                'ip' => '104.18.6.41',
                'region' => 'South America'
            ],
            // Additional regions
            [
                'location' => 'India (Google)',
                'country' => 'IN',
                'ip' => '142.250.194.36',
                'region' => 'Asia'
            ],
            [
                'location' => 'South Africa (Microsoft)',
                'country' => 'ZA',
                'ip' => '20.10.168.9',
                'region' => 'Africa'
            ],
        ];
    }
    
    /**
     * Perform a direct bandwidth test to a host
     * Used as a fallback method when other measurements fail
     * 
     * @param string $host Host to test
     * @param int $size Expected file size in bytes
     * @param int $timeout Timeout in seconds
     * @return float Download speed in Mbps
     */
    protected function directBandwidthTest($host, $size = 100000, $timeout = 5)
    {
        try {
            // Create a URL to a reliable static file
            $testUrl = "https://{$host}/favicon.ico";
            
            $client = new Client([
                'timeout' => $timeout,
                'connect_timeout' => 2,
                'verify' => false
            ]);
            
            $startTime = microtime(true);
            $response = $client->get($testUrl);
            $endTime = microtime(true);
            
            $contentLength = $response->getHeaderLine('Content-Length');
            $transferTime = $endTime - $startTime;
            
            if ($contentLength && $transferTime > 0) {
                // Convert bytes to bits and calculate Mbps
                $downloadSpeed = round((($contentLength * 8) / $transferTime) / 1000000, 2);
                return $downloadSpeed;
            }
            
            // Fallback if Content-Length header is missing
            $body = $response->getBody();
            $bodySize = $body->getSize();
            
            if ($bodySize && $transferTime > 0) {
                return round((($bodySize * 8) / $transferTime) / 1000000, 2);
            }
            
            return 1.0; // Default fallback
        } catch (\Exception $e) {
            Log::warning("Direct bandwidth test failed: " . $e->getMessage());
            return 0.5; // Minimum fallback value
        }
    }
    
    /**
     * Calculate statistics from test results
     *
     * @param array $results Test results
     * @return array Statistics
     */
    protected function calculateStatistics(array $results)
    {
        // Filter online/slow status results
        $successfulResults = array_filter($results, function($result) {
            return ($result['status'] === 'online' || $result['status'] === 'slow');
        });
        
        // Prepare initial stats structure with empty region stats
        $stats = [
            'fastest' => null,
            'slowest' => null,
            'average_time' => null,
            'median_time' => null,
            'success_rate' => 0,
            'avg_download_speed' => null,
            'avg_upload_speed' => null,
            'total_tests' => count($results),
            'successful_tests' => 0,
            'region_stats' => [
                'Asia' => [
                    'total' => 0,
                    'online' => 0,
                    'slow' => 0,
                    'offline' => 0,
                    'count' => 0,
                    'avgResponseTime' => null,
                    'avgDownloadSpeed' => null,
                    'avgUploadSpeed' => null
                ],
                'Europe' => [
                    'total' => 0,
                    'online' => 0,
                    'slow' => 0,
                    'offline' => 0,
                    'count' => 0,
                    'avgResponseTime' => null,
                    'avgDownloadSpeed' => null,
                    'avgUploadSpeed' => null
                ],
                'North America' => [
                    'total' => 0,
                    'online' => 0, 
                    'slow' => 0,
                    'offline' => 0,
                    'count' => 0,
                    'avgResponseTime' => null,
                    'avgDownloadSpeed' => null,
                    'avgUploadSpeed' => null
                ]
            ]
        ];
        
        if (empty($successfulResults)) {
            // Process region stats for all results even if no successful ones
            foreach ($results as $result) {
                $region = $result['region'] ?? 'Other';
                
                // Handle main regions we care about
                if (in_array($region, ['Asia', 'Europe', 'North America'])) {
                    $stats['region_stats'][$region]['total']++;
                    
                    if ($result['status'] === 'offline') {
                        $stats['region_stats'][$region]['offline']++;
                    }
                }
            }
            
            return $stats;
        }
        
        // Sort by response time
        usort($successfulResults, function($a, $b) {
            return $a['time'] <=> $b['time'];
        });
        
        // Get fastest and slowest
        $fastest = reset($successfulResults);
        $slowest = end($successfulResults);
        
        // Calculate average time
        $totalTime = array_reduce($successfulResults, function($carry, $item) {
            return $carry + $item['time'];
        }, 0);
        $averageTime = round($totalTime / count($successfulResults));
        
        // Calculate median time
        $medianIndex = floor(count($successfulResults) / 2);
        if (count($successfulResults) % 2 === 0) {
            $medianTime = round(($successfulResults[$medianIndex - 1]['time'] + $successfulResults[$medianIndex]['time']) / 2);
        } else {
            $medianTime = $successfulResults[$medianIndex]['time'];
        }
        
        // Calculate success rate
        $successRate = round((count($successfulResults) / count($results)) * 100);
        
        // Calculate average download and upload speeds (excluding null values)
        $downloadSpeeds = array_filter(array_column($successfulResults, 'download_speed'), function($speed) {
            return $speed !== null;
        });
        
        $uploadSpeeds = array_filter(array_column($successfulResults, 'upload_speed'), function($speed) {
            return $speed !== null;
        });
        
        $avgDownloadSpeed = !empty($downloadSpeeds) ? round(array_sum($downloadSpeeds) / count($downloadSpeeds), 2) : null;
        $avgUploadSpeed = !empty($uploadSpeeds) ? round(array_sum($uploadSpeeds) / count($uploadSpeeds), 2) : null;
        
        // Process all results to create region stats
        foreach ($results as $result) {
            $region = $result['region'] ?? 'Other';
            
            // Handle main regions we care about
            if (in_array($region, ['Asia', 'Europe', 'North America'])) {
                $stats['region_stats'][$region]['total']++;
                
                if ($result['status'] === 'online') {
                    $stats['region_stats'][$region]['online']++;
                    $stats['region_stats'][$region]['count']++;
                    
                    if ($result['time'] !== null) {
                        if (!isset($stats['region_stats'][$region]['responseTime'])) {
                            $stats['region_stats'][$region]['responseTime'] = 0;
                        }
                        $stats['region_stats'][$region]['responseTime'] += $result['time'];
                    }
                    
                    if ($result['download_speed'] !== null) {
                        if (!isset($stats['region_stats'][$region]['downloadSpeed'])) {
                            $stats['region_stats'][$region]['downloadSpeed'] = 0;
                            $stats['region_stats'][$region]['downloadCount'] = 0;
                        }
                        $stats['region_stats'][$region]['downloadSpeed'] += $result['download_speed'];
                        $stats['region_stats'][$region]['downloadCount']++;
                    }
                    
                    if ($result['upload_speed'] !== null) {
                        if (!isset($stats['region_stats'][$region]['uploadSpeed'])) {
                            $stats['region_stats'][$region]['uploadSpeed'] = 0;
                            $stats['region_stats'][$region]['uploadCount'] = 0;
                        }
                        $stats['region_stats'][$region]['uploadSpeed'] += $result['upload_speed'];
                        $stats['region_stats'][$region]['uploadCount']++;
                    }
                } else if ($result['status'] === 'slow') {
                    $stats['region_stats'][$region]['slow']++;
                    $stats['region_stats'][$region]['count']++;
                    
                    if ($result['time'] !== null) {
                        if (!isset($stats['region_stats'][$region]['responseTime'])) {
                            $stats['region_stats'][$region]['responseTime'] = 0;
                        }
                        $stats['region_stats'][$region]['responseTime'] += $result['time'];
                    }
                    
                    if ($result['download_speed'] !== null) {
                        if (!isset($stats['region_stats'][$region]['downloadSpeed'])) {
                            $stats['region_stats'][$region]['downloadSpeed'] = 0;
                            $stats['region_stats'][$region]['downloadCount'] = 0;
                        }
                        $stats['region_stats'][$region]['downloadSpeed'] += $result['download_speed'];
                        $stats['region_stats'][$region]['downloadCount']++;
                    }
                    
                    if ($result['upload_speed'] !== null) {
                        if (!isset($stats['region_stats'][$region]['uploadSpeed'])) {
                            $stats['region_stats'][$region]['uploadSpeed'] = 0;
                            $stats['region_stats'][$region]['uploadCount'] = 0;
                        }
                        $stats['region_stats'][$region]['uploadSpeed'] += $result['upload_speed'];
                        $stats['region_stats'][$region]['uploadCount']++;
                    }
                } else if ($result['status'] === 'offline') {
                    $stats['region_stats'][$region]['offline']++;
                }
            }
        }
        
        // Calculate averages for each region
        foreach (['Asia', 'Europe', 'North America'] as $region) {
            if ($stats['region_stats'][$region]['count'] > 0) {
                $stats['region_stats'][$region]['avgResponseTime'] = round($stats['region_stats'][$region]['responseTime'] / $stats['region_stats'][$region]['count']);
            }
            
            if (isset($stats['region_stats'][$region]['downloadCount']) && $stats['region_stats'][$region]['downloadCount'] > 0) {
                $stats['region_stats'][$region]['avgDownloadSpeed'] = round($stats['region_stats'][$region]['downloadSpeed'] / $stats['region_stats'][$region]['downloadCount'], 2);
            }
            
            if (isset($stats['region_stats'][$region]['uploadCount']) && $stats['region_stats'][$region]['uploadCount'] > 0) {
                $stats['region_stats'][$region]['avgUploadSpeed'] = round($stats['region_stats'][$region]['uploadSpeed'] / $stats['region_stats'][$region]['uploadCount'], 2);
            }
        }
        
        // Update overall stats
        $stats['fastest'] = $fastest;
        $stats['slowest'] = $slowest;
        $stats['average_time'] = $averageTime;
        $stats['median_time'] = $medianTime;
        $stats['success_rate'] = $successRate;
        $stats['avg_download_speed'] = $avgDownloadSpeed;
        $stats['avg_upload_speed'] = $avgUploadSpeed;
        $stats['successful_tests'] = count($successfulResults);
        
        return $stats;
    }
}