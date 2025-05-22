<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Log;

/**
 * CheckHost API Integration
 * 
 * A Laravel wrapper for the check-host.net API services
 * that allows testing website availability from multiple global nodes.
 * Supports ping, HTTP, TCP, and DNS checks.
 */
class CheckHostApi
{
    /**
     * Base URL for the CheckHost API
     * @var string
     */
    protected $baseUrl = 'https://check-host.net';
    
    /**
     * Domain to test
     * @var string
     */
    protected $domain;
    
    /**
     * Selected nodes for testing
     * @var array
     */
    protected $nodes = [];
    
    /**
     * Request timeout in seconds
     * @var int
     */
    protected $timeout = 30;
    
    /**
     * Wait time between initial request and result fetch (seconds)
     * @var int
     */
    protected $waitTime = 15;
    
    /**
     * Maximum number of result retries
     * @var int
     */
    protected $maxRetries = 5;

    /**
     * Default list of node IDs to use as fallback
     * @var array
     */
    protected $defaultNodes = [
        'us1', 'us2', 'us3', 'uk1', 'de1', 'fr1', 'nl1', 
        'jp1', 'sg1', 'au1', 'br1', 'ca1', 'in1'
    ];
    
    /**
     * Constructor
     * 
     * @param string|null $domain Domain to test
     */
    public function __construct($domain = null)
    {
        if ($domain) {
            $this->domain = $domain;
        }
        
        Log::debug("CheckHostApi initialized", ['domain' => $domain]);
    }
    
    /**
     * Set domain to test
     * 
     * @param string $domain
     * @return $this
     */
    public function domain($domain)
    {
        $this->domain = $domain;
        return $this;
    }
    
    /**
     * Add a node to test from
     * 
     * @param string $node Node identifier
     * @return $this
     */
    public function node($node)
    {
        $this->nodes[] = $node;
        Log::debug("Added node to CheckHost test", ['node' => $node]);
        return $this;
    }
    
    /**
     * Set timeout for requests
     * 
     * @param int $seconds
     * @return $this
     */
    public function setTimeout($seconds)
    {
        $this->timeout = max(5, intval($seconds));
        return $this;
    }
    
    /**
     * Set wait time between initial request and fetching results
     * 
     * @param int $seconds
     * @return $this 
     */
    public function setWaitTime($seconds)
    {
        $this->waitTime = max(5, intval($seconds));
        return $this;
    }
    
    /**
     * Reset selected nodes
     * 
     * @return $this
     */
    public function resetNodes()
    {
        $this->nodes = [];
        return $this;
    }
    
    /**
     * Get available nodes from CheckHost
     * 
     * @return array Array of node identifiers
     */
    public function getAvailableNodes()
    {
        try {
            // Use cURL for more reliable communication
            $ch = curl_init($this->baseUrl . '/nodes/hosts');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
                CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; CheckHostAPIClient/1.0)'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                Log::warning("cURL error fetching nodes list", ['error' => $error]);
                return $this->defaultNodes;
            }
            
            if ($httpCode !== 200) {
                Log::warning("HTTP error fetching nodes list", ['status' => $httpCode]);
                return $this->defaultNodes;
            }
            
            $data = json_decode($response, true);
            if (isset($data['nodes']) && is_array($data['nodes'])) {
                $nodeIds = array_keys($data['nodes']);
                Log::info("Retrieved node list from CheckHost API", ['count' => count($nodeIds)]);
                return $nodeIds;
            }
            
            Log::warning("Unexpected response format from nodes API", [
                'response_type' => gettype($data),
                'has_nodes' => isset($data['nodes']),
                'nodes_is_array' => isset($data['nodes']) && is_array($data['nodes'])
            ]);
        } catch (\Exception $e) {
            Log::warning("Exception getting nodes from API", ['error' => $e->getMessage()]);
        }
        
        // Fallback to the default list of reliable nodes
        Log::info("Using fallback node list", ['nodes' => $this->defaultNodes]);
        return $this->defaultNodes;
    }
    
    /**
     * Perform HTTP check
     * 
     * @return array|null Results of the HTTP check
     */
    public function http()
    {
        return $this->performCheck('check-http');
    }
    
    /**
     * Perform Ping check
     * 
     * @return array|null Results of the Ping check
     */
    public function ping()
    {
        return $this->performCheck('check-ping');
    }
    
    /**
     * Perform TCP check
     * 
     * @return array|null Results of the TCP check
     */
    public function tcp()
    {
        return $this->performCheck('check-tcp');
    }
    
    /**
     * Perform DNS check
     * 
     * @return array|null Results of the DNS check
     */
    public function dns()
    {
        return $this->performCheck('check-dns');
    }
    
    /**
     * Perform a check and wait for results with improved error handling
     * 
     * @param string $checkType Type of check to perform (check-ping, check-http, etc)
     * @return array|null Results of the check
     * @throws \InvalidArgumentException If domain is not set
     */
    protected function performCheck($checkType)
    {
        if (empty($this->domain)) {
            throw new \InvalidArgumentException('Domain is required');
        }
        
        try {
            // Prepare parameters
            $params = ['host' => $this->domain];
            
            // Add nodes if specified
            if (!empty($this->nodes)) {
                foreach ($this->nodes as $node) {
                    $params['node'][] = $node;
                }
            }
            
            // Log start of check
            Log::info("Starting CheckHost {$checkType} for domain {$this->domain}", [
                'node_count' => count($this->nodes),
                'timeout' => $this->timeout
            ]);
            
            // Use cURL for more reliable POST request
            $ch = curl_init($this->baseUrl . '/' . $checkType);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
                CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; CheckHostAPIClient/1.0)'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                Log::error("cURL error initiating CheckHost check", ['error' => $error]);
                return null;
            }
            
            if ($httpCode !== 200) {
                Log::error("HTTP error initiating CheckHost check", [
                    'status' => $httpCode,
                    'response' => $response
                ]);
                return null;
            }
            
            $initData = json_decode($response, true);
            
            if (!isset($initData['request_id'])) {
                Log::error("CheckHost API missing request_id", ['response' => $response]);
                return null;
            }
            
            $requestId = $initData['request_id'];
            Log::info("Received CheckHost request_id: {$requestId} for {$checkType}");
            
            // Wait for results with improved logging
            Log::info("Waiting {$this->waitTime} seconds for CheckHost results...");
            sleep($this->waitTime);
            
            // Try to get results with exponential backoff
            $results = null;
            $retries = 0;
            $delay = 2;
            $backoffFactor = 1.5;
            
            while ($retries < $this->maxRetries) {
                // Try to get results with cURL
                $retryCount = $retries + 1;
                Log::debug("Attempt {$retryCount} to fetch CheckHost results");
                
                $ch = curl_init($this->baseUrl . '/check-result/' . $requestId);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => $this->timeout,
                    CURLOPT_HTTPHEADER => ['Accept: application/json'],
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; CheckHostAPIClient/1.0)'
                ]);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                if ($error) {
                    Log::warning("cURL error fetching results", ['error' => $error]);
                    $retries++;
                    continue;
                }
                
                if ($httpCode !== 200) {
                    Log::warning("HTTP error fetching results", ['status' => $httpCode]);
                    $retries++;
                    continue;
                }
                
                if (empty($response)) {
                    Log::warning("Empty response fetching results");
                    $retries++;
                    continue;
                }
                
                $results = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning("JSON decode error", ['error' => json_last_error_msg()]);
                    $retries++;
                    continue;
                }
                
                // Check if results are complete
                $stillProcessing = false;
                
                if (!empty($this->nodes)) {
                    foreach ($this->nodes as $node) {
                        if (!isset($results[$node]) || $results[$node] === null) {
                            $stillProcessing = true;
                            break;
                        }
                    }
                }
                
                if (!$stillProcessing) {
                    Log::info("Successfully fetched complete CheckHost results");
                    break;
                }
                
                Log::debug("Results still processing, waiting before retry...");
                
                $retries++;
                if ($retries < $this->maxRetries) {
                    $delay = ceil($delay * $backoffFactor);
                    $nextRetry = $retries + 1;
                    Log::debug("Waiting {$delay} seconds before retry {$nextRetry}");
                    sleep($delay);
                }
            }
            
            // Try to get extended results for more details
            Log::debug("Fetching extended results for request {$requestId}");
            
            $ch = curl_init($this->baseUrl . '/check-result-extended/' . $requestId);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_HTTPHEADER => ['Accept: application/json'],
                CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; CheckHostAPIClient/1.0)'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if (!$error && $httpCode === 200 && !empty($response)) {
                $extendedResults = json_decode($response, true);
                if (isset($extendedResults['results']) && is_array($extendedResults['results'])) {
                    Log::info("Successfully fetched extended results");
                    return $extendedResults['results'];
                }
            }
            
            // Return the basic results if extended results failed
            return $results;
        } catch (\Exception $e) {
            Log::error("CheckHost API exception: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Run a comprehensive test using multiple methods
     * 
     * @param string $url URL to test
     * @return array Combined test results
     */
    public function runComprehensiveTest($url)
    {
        // Extract domain from URL
        $domain = parse_url($url, PHP_URL_HOST);
        if (empty($domain)) {
            $domain = $url;
        }
        $this->domain($domain);
        
        // If no nodes are specified, get some available ones
        if (empty($this->nodes)) {
            $availableNodes = $this->getAvailableNodes();
            
            // Take a subset of nodes for faster testing
            if (count($availableNodes) > 10) {
                $this->nodes = array_slice($availableNodes, 0, 10);
            } else {
                $this->nodes = $availableNodes;
            }
            
            Log::info("Using " . count($this->nodes) . " nodes for testing");
        }
        
        $testResults = [
            'domain' => $domain,
            'timestamp' => time(),
            'nodes_used' => $this->nodes,
        ];
        
        // Run HTTP test
        try {
            Log::info("Starting CheckHost HTTP test for domain: " . $domain);
            $httpResults = $this->http();
            $testResults['http'] = $httpResults;
            Log::info("HTTP test completed", [
                'success' => !empty($httpResults), 
                'results_count' => is_array($httpResults) ? count($httpResults) : 0
            ]);
        } catch (\Exception $e) {
            Log::error("Error running HTTP test: " . $e->getMessage());
            $testResults['http'] = null;
            $testResults['http_error'] = $e->getMessage();
        }
        
        // Run Ping test
        try {
            Log::info("Starting CheckHost Ping test for domain: " . $domain);
            $pingResults = $this->ping();
            $testResults['ping'] = $pingResults;
            Log::info("Ping test completed", [
                'success' => !empty($pingResults), 
                'results_count' => is_array($pingResults) ? count($pingResults) : 0
            ]);
        } catch (\Exception $e) {
            Log::error("Error running Ping test: " . $e->getMessage());
            $testResults['ping'] = null;
            $testResults['ping_error'] = $e->getMessage();
        }
        
        // Optionally add TCP test
        try {
            // Only run TCP test if domain has a port specified or is a web domain
            $runTcpTest = false;
            
            if (strpos($domain, ':') !== false) {
                $runTcpTest = true;
            } else if (preg_match('/\.(com|net|org|io|co|app|dev)$/i', $domain)) {
                $runTcpTest = true;
            }
            
            if ($runTcpTest) {
                Log::info("Starting CheckHost TCP test for domain: " . $domain);
                $tcpResults = $this->tcp();
                $testResults['tcp'] = $tcpResults;
                Log::info("TCP test completed", [
                    'success' => !empty($tcpResults), 
                    'results_count' => is_array($tcpResults) ? count($tcpResults) : 0
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error running TCP test: " . $e->getMessage());
            $testResults['tcp'] = null;
            $testResults['tcp_error'] = $e->getMessage();
        }
        
        return $testResults;
    }
    
    /**
     * Process and enhance raw CheckHost results
     * 
     * @param array $results Raw CheckHost results
     * @return array Enhanced results with calculated metrics
     */
    public function processResults($results) 
    {
        if (empty($results) || !is_array($results)) {
            return [
                'success' => false,
                'message' => 'No valid results to process'
            ];
        }
        
        // Initialize processed results
        $processed = [
            'success' => true,
            'timestamp' => time(),
            'metrics' => [
                'online_nodes' => 0,
                'offline_nodes' => 0,
                'total_nodes' => 0,
                'availability_percentage' => 0,
                'average_response_time' => null
            ],
            'regions' => [],
            'nodes' => []
        ];
        
        // Extract HTTP results (most useful for general availability)
        $httpResults = $results['http'] ?? [];
        if (empty($httpResults) || !is_array($httpResults)) {
            $processed['success'] = false;
            $processed['message'] = 'No HTTP test results available';
            return $processed;
        }
        
        // Process each node
        $responseTimes = [];
        
        foreach ($httpResults as $nodeId => $nodeData) {
            // Initialize node metrics
            $nodeInfo = [
                'id' => $nodeId,
                'region' => $this->getNodeRegion($nodeId),
                'country' => substr($nodeId, 0, 2),
                'status' => 'offline',
                'response_time' => null,
                'details' => null
            ];
            
            // Process HTTP data
            if (is_array($nodeData) && !empty($nodeData)) {
                // Handle different response formats
                if (isset($nodeData[0]) && is_array($nodeData[0])) {
                    // Standard format: [[1, 0.13, "OK", "200", "94.242.206.94"]]
                    $status = $nodeData[0][0] ?? 0;
                    $time = isset($nodeData[0][1]) ? round($nodeData[0][1] * 1000) : null;
                    $message = $nodeData[0][2] ?? null;
                    $statusCode = $nodeData[0][3] ?? null;
                    $ip = $nodeData[0][4] ?? null;
                    
                    $nodeInfo['status'] = $status ? 'online' : 'offline';
                    $nodeInfo['response_time'] = $time;
                    $nodeInfo['details'] = [
                        'message' => $message,
                        'status_code' => $statusCode,
                        'ip' => $ip
                    ];
                    
                    // Add to response times for average calculation
                    if ($status && $time) {
                        $responseTimes[] = $time;
                    }
                } else if (is_array($nodeData) && isset($nodeData['status'])) {
                    // Alternative format with status property
                    $nodeInfo['status'] = $nodeData['status'];
                    $nodeInfo['response_time'] = $nodeData['response_time_ms'] ?? null;
                    $nodeInfo['details'] = $nodeData;
                    
                    if ($nodeData['status'] === 'online' && isset($nodeData['response_time_ms'])) {
                        $responseTimes[] = $nodeData['response_time_ms'];
                    }
                } else if (is_array($nodeData) && isset($nodeData['time'])) {
                    // TCP check format: [{"time": 0.03, "address": "104.28.31.42"}]
                    $nodeInfo['status'] = 'online';
                    $nodeInfo['response_time'] = round(($nodeData['time'] ?? 0) * 1000);
                    $nodeInfo['details'] = $nodeData;
                    
                    if (isset($nodeData['time'])) {
                        $responseTimes[] = round($nodeData['time'] * 1000);
                    }
                } else if (is_array($nodeData) && isset($nodeData['error'])) {
                    // Error format: [{"error": "Connection timed out"}]
                    $nodeInfo['status'] = 'offline';
                    $nodeInfo['details'] = $nodeData;
                }
            }
            
            // Update counters
            $processed['metrics']['total_nodes']++;
            if ($nodeInfo['status'] === 'online') {
                $processed['metrics']['online_nodes']++;
            } else {
                $processed['metrics']['offline_nodes']++;
            }
            
            // Add to regions
            if (!isset($processed['regions'][$nodeInfo['region']])) {
                $processed['regions'][$nodeInfo['region']] = [
                    'name' => $nodeInfo['region'],
                    'total_nodes' => 0,
                    'online_nodes' => 0,
                    'offline_nodes' => 0,
                    'availability_percentage' => 0,
                    'response_times' => []
                ];
            }
            
            $processed['regions'][$nodeInfo['region']]['total_nodes']++;
            if ($nodeInfo['status'] === 'online') {
                $processed['regions'][$nodeInfo['region']]['online_nodes']++;
                if ($nodeInfo['response_time']) {
                    $processed['regions'][$nodeInfo['region']]['response_times'][] = $nodeInfo['response_time'];
                }
            } else {
                $processed['regions'][$nodeInfo['region']]['offline_nodes']++;
            }
            
            // Add node to the list
            $processed['nodes'][$nodeId] = $nodeInfo;
        }
        
        // Calculate availability percentage
        if ($processed['metrics']['total_nodes'] > 0) {
            $processed['metrics']['availability_percentage'] = round(
                ($processed['metrics']['online_nodes'] / $processed['metrics']['total_nodes']) * 100, 
                1
            );
        }
        
        // Calculate average response time
        if (!empty($responseTimes)) {
            $processed['metrics']['average_response_time'] = round(
                array_sum($responseTimes) / count($responseTimes),
                1
            );
        }
        
        // Calculate region metrics
        foreach ($processed['regions'] as $region => $regionData) {
            if ($regionData['total_nodes'] > 0) {
                $processed['regions'][$region]['availability_percentage'] = round(
                    ($regionData['online_nodes'] / $regionData['total_nodes']) * 100,
                    1
                );
                
                if (!empty($regionData['response_times'])) {
                    $processed['regions'][$region]['average_response_time'] = round(
                        array_sum($regionData['response_times']) / count($regionData['response_times']),
                        1
                    );
                }
            }
        }
        
        return $processed;
    }
    
    /**
     * Get region for a node ID
     * 
     * @param string $nodeId Node identifier
     * @return string Region name
     */
    protected function getNodeRegion($nodeId)
    {
        // Extract country code from node ID
        $countryCode = substr($nodeId, 0, 2);
        
        // Region mapping
        $regions = [
            // North America
            'us' => 'North America',
            'ca' => 'North America',
            'mx' => 'North America',
            
            // Europe
            'uk' => 'Europe',
            'gb' => 'Europe',
            'de' => 'Europe',
            'fr' => 'Europe',
            'es' => 'Europe',
            'it' => 'Europe',
            'nl' => 'Europe',
            'se' => 'Europe',
            'no' => 'Europe',
            'fi' => 'Europe',
            'dk' => 'Europe',
            'ch' => 'Europe',
            'at' => 'Europe',
            'be' => 'Europe',
            'ie' => 'Europe',
            'pl' => 'Europe',
            'pt' => 'Europe',
            'ru' => 'Europe',
            'ua' => 'Europe',
            'cz' => 'Europe',
            'hu' => 'Europe',
            'ro' => 'Europe',
            
            // Asia
            'jp' => 'Asia',
            'cn' => 'Asia',
            'kr' => 'Asia',
            'sg' => 'Asia',
            'in' => 'Asia',
            'hk' => 'Asia',
            'tw' => 'Asia',
            'th' => 'Asia',
            'my' => 'Asia',
            'id' => 'Asia',
            'ph' => 'Asia',
            'vn' => 'Asia',
            
            // Oceania
            'au' => 'Oceania',
            'nz' => 'Oceania',
            
            // South America
            'br' => 'South America',
            'ar' => 'South America',
            'cl' => 'South America',
            'co' => 'South America',
            'pe' => 'South America',
            
            // Africa
            'za' => 'Africa',
            'eg' => 'Africa',
            'ma' => 'Africa',
            'ng' => 'Africa',
            'ke' => 'Africa',
            
            // Middle East
            'ae' => 'Middle East',
            'sa' => 'Middle East',
            'il' => 'Middle East',
            'tr' => 'Middle East'
        ];
        
        return $regions[$countryCode] ?? 'Other';
    }
}