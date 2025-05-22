<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Log;

class BandwidthTestService
{
    /**
     * Sample file size (bytes) for download speed testing
     */
    protected $testFileSize = 100000; // ~100KB
    
    /**
     * List of reliable static file servers for testing
     */
    protected $testFileServers = [
        'cdn.jsdelivr.net' => '/gh/geerlingguy/Ping@master/README.md',
        'cdnjs.cloudflare.com' => '/ajax/libs/jquery/3.6.0/jquery.min.js',
        'code.jquery.com' => '/jquery-3.6.0.min.js',
        'ajax.googleapis.com' => '/ajax/libs/jquery/3.6.0/jquery.min.js',
        'stackpath.bootstrapcdn.com' => '/bootstrap/4.5.2/css/bootstrap.min.css'
    ];
    
    /**
     * Timeout for testing in seconds
     */
    protected $timeout = 5;
    
    /**
     * Connection timeout in seconds
     */
    protected $connectTimeout = 2;
    
    /**
     * Test bandwidth from a specific server
     *
     * @param string $host Host name or IP
     * @param string|null $path Path to file
     * @param int|null $timeout Timeout in seconds
     * @return array Bandwidth test results
     */
    public function testBandwidth($host, $path = null, $timeout = null)
    {
        $timeout = $timeout ?? $this->timeout;
        
        // If no path provided, use default path for that host
        if ($path === null) {
            if (isset($this->testFileServers[$host])) {
                $path = $this->testFileServers[$host];
            } else {
                // Try with favicon.ico if not found
                $path = '/favicon.ico';
            }
        }
        
        // Determine schema (http/https)
        $schema = 'https';
        $testUrl = "{$schema}://{$host}{$path}";
        
        try {
            $client = new Client([
                'timeout' => $timeout,
                'connect_timeout' => $this->connectTimeout,
                'verify' => false, // Skip SSL verification for speed
                'allow_redirects' => true, // Allow redirects
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (compatible; SpeedTest/2.0)',
                    'Accept' => '*/*',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                ]
            ]);
            
            $downloadSpeed = null;
            $uploadSpeed = null;
            $responseTime = null;
            $downloadSize = null;
            
            $response = $client->get($testUrl, [
                'on_stats' => function (TransferStats $stats) use (&$downloadSpeed, &$responseTime, &$downloadSize) {
                    if ($stats->hasResponse()) {
                        // Response time (ms)
                        $responseTime = round($stats->getTransferTime() * 1000);
                        
                        // Calculate download speed
                        $downloadBytes = $stats->getHandlerStat('size_download') ?: 0;
                        $downloadTime = $stats->getHandlerStat('total_time') ?: 0;
                        $downloadSize = $downloadBytes;
                        
                        if ($downloadBytes > 0 && $downloadTime > 0) {
                            // Calculate speed in Mbps
                            $downloadSpeed = round((($downloadBytes * 8) / $downloadTime) / 1000000, 2);
                        }
                    }
                }
            ]);
            
            // Get size from header if not yet available
            if ($downloadSize === null || $downloadSize === 0) {
                $contentLength = $response->getHeaderLine('Content-Length');
                if ($contentLength) {
                    $downloadSize = (int)$contentLength;
                } else {
                    // If no Content-Length header, get actual size
                    $body = $response->getBody();
                    $downloadSize = $body->getSize();
                }
            }
            
            // Estimate upload speed based on download speed
            if ($downloadSpeed !== null) {
                // Upload speed typically ~35% of download speed
                $uploadSpeed = round($downloadSpeed * 0.35, 2);
            }
            
            // Ensure we have a value for download_speed
            if ($downloadSpeed === null) {
                if ($responseTime > 0) {
                    // Simple formula: speed ~= 10Mbps / (ping in seconds)
                    $pingInSeconds = $responseTime / 1000;
                    $downloadSpeed = round(10 / max(0.001, $pingInSeconds), 2);
                    // Cap to reasonable values
                    $downloadSpeed = max(0.5, min(50, $downloadSpeed));
                } else {
                    $downloadSpeed = 1.0; // Default fallback
                }
            }
            
            // Ensure we have a value for upload_speed
            if ($uploadSpeed === null) {
                $uploadSpeed = round($downloadSpeed * 0.35, 2);
            }
            
            return [
                'status' => 'success',
                'download_speed' => $downloadSpeed,
                'upload_speed' => $uploadSpeed,
                'response_time' => $responseTime,
                'download_size' => $downloadSize,
                'url' => $testUrl
            ];
        } catch (\Exception $e) {
            Log::warning("Bandwidth test error ({$testUrl}): " . $e->getMessage());
            
            return [
                'status' => 'error',
                'download_speed' => 0,
                'upload_speed' => 0,
                'response_time' => null,
                'error' => $e->getMessage(),
                'url' => $testUrl
            ];
        }
    }
    
    /**
     * Test bandwidth from multiple servers and get average
     *
     * @param int $count Number of servers to test
     * @return array Bandwidth test results from multiple servers
     */
    public function testBandwidthFromMultipleServers($count = 3)
    {
        // Randomly select servers from the list
        $servers = array_keys($this->testFileServers);
        shuffle($servers);
        $testServers = array_slice($servers, 0, min($count, count($servers)));
        
        $results = [];
        $totalDownloadSpeed = 0;
        $totalUploadSpeed = 0;
        $successCount = 0;
        
        foreach ($testServers as $server) {
            $path = $this->testFileServers[$server];
            $result = $this->testBandwidth($server, $path);
            $results[$server] = $result;
            
            if ($result['status'] === 'success') {
                $totalDownloadSpeed += $result['download_speed'];
                $totalUploadSpeed += $result['upload_speed'];
                $successCount++;
            }
        }
        
        // Calculate average values
        $avgDownloadSpeed = $successCount > 0 ? round($totalDownloadSpeed / $successCount, 2) : 0;
        $avgUploadSpeed = $successCount > 0 ? round($totalUploadSpeed / $successCount, 2) : 0;
        
        return [
            'average_download_speed' => $avgDownloadSpeed,
            'average_upload_speed' => $avgUploadSpeed,
            'success_count' => $successCount,
            'total_servers' => count($testServers),
            'detailed_results' => $results
        ];
    }
    
    /**
     * Perform parallel bandwidth tests from multiple servers
     *
     * @param int $count Number of servers to test
     * @return array Bandwidth test results
     */
    public function testBandwidthParallel($count = 3)
    {
        // Randomly select servers from the list
        $servers = array_keys($this->testFileServers);
        shuffle($servers);
        $testServers = array_slice($servers, 0, min($count, count($servers)));
        
        // Create Guzzle client
        $client = new Client([
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
            'verify' => false,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; SpeedTest/2.0)',
                'Accept' => '*/*',
                'Cache-Control' => 'no-cache'
            ]
        ]);
        
        // Create promises for each server
        $promises = [];
        $statsData = [];
        
        foreach ($testServers as $server) {
            $path = $this->testFileServers[$server];
            $url = "https://{$server}{$path}";
            
            $statsData[$server] = [
                'download_speed' => null,
                'response_time' => null,
                'download_size' => null
            ];
            
            $promises[$server] = $client->getAsync($url, [
                'on_stats' => function (TransferStats $stats) use (&$statsData, $server) {
                    if ($stats->hasResponse()) {
                        // Response time (ms)
                        $statsData[$server]['response_time'] = round($stats->getTransferTime() * 1000);
                        
                        // Calculate download speed
                        $downloadBytes = $stats->getHandlerStat('size_download') ?: 0;
                        $downloadTime = $stats->getHandlerStat('total_time') ?: 0;
                        $statsData[$server]['download_size'] = $downloadBytes;
                        
                        if ($downloadBytes > 0 && $downloadTime > 0) {
                            // Calculate speed in Mbps
                            $statsData[$server]['download_speed'] = round((($downloadBytes * 8) / $downloadTime) / 1000000, 2);
                        }
                    }
                }
            ]);
        }
        
        // Wait for all promises to complete
        $responses = Promise\Utils::settle($promises)->wait();
        
        // Process results
        $results = [];
        $totalDownloadSpeed = 0;
        $totalUploadSpeed = 0;
        $successCount = 0;
        
        foreach ($responses as $server => $response) {
            $downloadSpeed = $statsData[$server]['download_speed'];
            $responseTime = $statsData[$server]['response_time'];
            
            // Ensure we have a value for download_speed
            if ($downloadSpeed === null) {
                if ($responseTime > 0) {
                    $pingInSeconds = $responseTime / 1000;
                    $downloadSpeed = round(10 / max(0.001, $pingInSeconds), 2);
                    $downloadSpeed = max(0.5, min(50, $downloadSpeed));
                } else {
                    $downloadSpeed = 1.0;
                }
            }
            
            // Estimate upload speed
            $uploadSpeed = round($downloadSpeed * 0.35, 2);
            
            if ($response['state'] === 'fulfilled') {
                $results[$server] = [
                    'status' => 'success',
                    'download_speed' => $downloadSpeed,
                    'upload_speed' => $uploadSpeed,
                    'response_time' => $responseTime,
                    'download_size' => $statsData[$server]['download_size'],
                    'url' => "https://{$server}{$this->testFileServers[$server]}"
                ];
                
                $totalDownloadSpeed += $downloadSpeed;
                $totalUploadSpeed += $uploadSpeed;
                $successCount++;
            } else {
                $results[$server] = [
                    'status' => 'error',
                    'download_speed' => 0,
                    'upload_speed' => 0,
                    'response_time' => null,
                    'error' => $response['reason']->getMessage(),
                    'url' => "https://{$server}{$this->testFileServers[$server]}"
                ];
            }
        }
        
        // Calculate average values
        $avgDownloadSpeed = $successCount > 0 ? round($totalDownloadSpeed / $successCount, 2) : 0;
        $avgUploadSpeed = $successCount > 0 ? round($totalUploadSpeed / $successCount, 2) : 0;
        
        return [
            'average_download_speed' => $avgDownloadSpeed,
            'average_upload_speed' => $avgUploadSpeed,
            'success_count' => $successCount,
            'total_servers' => count($testServers),
            'detailed_results' => $results
        ];
    }
    
    /**
     * Measure client bandwidth
     * Can be performed before running website speed test to use base values from the user
     * 
     * @return array Bandwidth measurement results
     */
    public function measureBandwidthForClient()
    {
        // Perform test from 2 servers
        $results = $this->testBandwidthParallel(2);
        
        return [
            'client_download_speed' => $results['average_download_speed'],
            'client_upload_speed' => $results['average_upload_speed'],
            'test_success' => $results['success_count'] > 0,
            'timestamp' => time()
        ];
    }
    
    /**
     * Add new test server to the list
     * 
     * @param string $host Host name
     * @param string $path Path to test file
     * @return void
     */
    public function addTestServer($host, $path)
    {
        $this->testFileServers[$host] = $path;
    }
    
    /**
     * Test specific URL for bandwidth measurement
     * 
     * @param string $url Full URL to test
     * @param int|null $timeout Timeout in seconds
     * @return array Bandwidth test results
     */
    public function testSpecificUrl($url, $timeout = null)
    {
        $timeout = $timeout ?? $this->timeout;
        $parsedUrl = parse_url($url);
        
        if (!$parsedUrl || !isset($parsedUrl['host'])) {
            return [
                'status' => 'error',
                'error' => 'Invalid URL',
                'url' => $url
            ];
        }
        
        try {
            $client = new Client([
                'timeout' => $timeout,
                'connect_timeout' => $this->connectTimeout,
                'verify' => false,
                'allow_redirects' => true,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (compatible; SpeedTest/2.0)',
                    'Accept' => '*/*',
                    'Cache-Control' => 'no-cache'
                ]
            ]);
            
            $downloadSpeed = null;
            $responseTime = null;
            $downloadSize = null;
            
            $startTime = microtime(true);
            $response = $client->get($url, [
                'on_stats' => function (TransferStats $stats) use (&$downloadSpeed, &$responseTime, &$downloadSize) {
                    if ($stats->hasResponse()) {
                        // Response time (ms)
                        $responseTime = round($stats->getTransferTime() * 1000);
                        
                        // Calculate download speed
                        $downloadBytes = $stats->getHandlerStat('size_download') ?: 0;
                        $downloadTime = $stats->getHandlerStat('total_time') ?: 0;
                        $downloadSize = $downloadBytes;
                        
                        if ($downloadBytes > 0 && $downloadTime > 0) {
                            // Calculate speed in Mbps
                            $downloadSpeed = round((($downloadBytes * 8) / $downloadTime) / 1000000, 2);
                        }
                    }
                }
            ]);
            $endTime = microtime(true);
            
            // Get size from body if not yet available
            if ($downloadSize === null || $downloadSize === 0) {
                $body = $response->getBody();
                $content = $body->getContents();
                $downloadSize = strlen($content);
            }
            
            // If we still don't have a download speed, calculate from total time
            if ($downloadSpeed === null) {
                $totalTime = $endTime - $startTime;
                if ($totalTime > 0 && $downloadSize > 0) {
                    $downloadSpeed = round((($downloadSize * 8) / $totalTime) / 1000000, 2);
                } else {
                    // Fallback based on response time
                    if ($responseTime > 0) {
                        $pingInSeconds = $responseTime / 1000;
                        $downloadSpeed = round(10 / max(0.001, $pingInSeconds), 2);
                        $downloadSpeed = max(0.5, min(50, $downloadSpeed));
                    } else {
                        $downloadSpeed = 1.0; // Default fallback
                    }
                }
            }
            
            // Estimate upload speed
            $uploadSpeed = round($downloadSpeed * 0.35, 2);
            
            return [
                'status' => 'success',
                'download_speed' => $downloadSpeed,
                'upload_speed' => $uploadSpeed,
                'response_time' => $responseTime,
                'download_size' => $downloadSize,
                'total_time' => round(($endTime - $startTime) * 1000),
                'url' => $url
            ];
        } catch (\Exception $e) {
            Log::warning("Specific URL bandwidth test error ({$url}): " . $e->getMessage());
            
            return [
                'status' => 'error',
                'download_speed' => 0,
                'upload_speed' => 0,
                'response_time' => null,
                'error' => $e->getMessage(),
                'url' => $url
            ];
        }
    }
}