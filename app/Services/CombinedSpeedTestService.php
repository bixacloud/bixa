<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Libraries\CheckHostApi;
use Illuminate\Support\Facades\Cache;

/**
 * Enhanced CombinedSpeedTestService with improved CheckHost integration
 */
class CombinedSpeedTestService
{
    /**
     * @var ServerSideSpeedTestService
     */
    protected $serverSideService;
    
    /**
     * @var int
     */
    protected $cacheMinutes = 10;
    
    /**
     * @var array
     */
    protected $selectedNodes = [
        // North America
        'us1', 'us2', 'us3',
        // Europe
        'uk1', 'de1', 'fr1',
        // Asia
        'jp1', 'in1', 'sg1',
        // Other regions
        'au1', 'br1'
    ];
    
    /**
     * Constructor
     * 
     * @param ServerSideSpeedTestService $serverSideService
     */
    public function __construct(ServerSideSpeedTestService $serverSideService)
    {
        $this->serverSideService = $serverSideService;
    }
    
    /**
     * Run combined speed test
     * 
     * @param string $url URL to test
     * @param string $strategy Test strategy (mobile/desktop)
     * @param array $options Additional options for the test
     * @return array Combined test results
     */
    public function runCombinedTest($url, $strategy = 'desktop', array $options = [])
    {
        // Extract options with defaults
        $useCheckHost = $options['use_check_host'] ?? true;
        $useAllNodes = $options['use_all_nodes'] ?? false;
        
        // Start timing
        $startTime = microtime(true);
        
        // Normalize URL
        $url = $this->normalizeUrl($url);
        
        // Create unique cache key
        $cacheKeyBase = 'speed_test_' . md5($url . '_' . $strategy);
        $cacheKey = $cacheKeyBase;
        
        // If using CheckHost, add that to the cache key
        if ($useCheckHost) {
            $cacheKey .= '_checkhost';
            
            // If using all nodes, add that to the cache key
            if ($useAllNodes) {
                $cacheKey .= '_allnodes';
            }
        }
        
        // Add cache buster if specified in options to force a fresh test
        if (isset($options['_cache_buster'])) {
            $cacheKey .= '_' . substr(md5($options['_cache_buster']), 0, 8);
        }
        
        // Check cache
        if (Cache::has($cacheKey)) {
            Log::info("Retrieved test results from cache for URL: $url");
            return Cache::get($cacheKey);
        }
        
        // Run server-side test
        Log::info("Starting ServerSide test for URL: $url");
        $serverSideResults = $this->serverSideService->runComprehensiveTest($url);
        
        // Initialize combined results
        $combinedResults = [
            'url' => $url,
            'timestamp' => time(),
            'server_side' => $serverSideResults,
        ];
        
        // Run CheckHost test if enabled
        if ($useCheckHost) {
            Log::info("Starting CheckHost test for URL: $url with all_nodes=" . ($useAllNodes ? "true" : "false"));
            $checkHostResults = $this->runCheckHostTests($url, $useAllNodes);
            $combinedResults['check_host'] = $checkHostResults;
            
            // Combine results
            $combinedStats = $this->calculateCombinedStats($serverSideResults, $checkHostResults);
            $combinedResults['combined_stats'] = $combinedStats;
        } else {
            Log::info("CheckHost tests disabled for URL: $url");
            // Create empty combined stats structure
            $combinedResults['combined_stats'] = $this->createEmptyCombinedStats($serverSideResults);
            $combinedResults['check_host'] = null;
        }
        
        // Calculate execution time
        $executionTime = round(microtime(true) - $startTime, 2);
        $combinedResults['execution_time'] = $executionTime;
        
        // Save to cache
        Cache::put($cacheKey, $combinedResults, $this->cacheMinutes * 60);
        
        Log::info("Completed combined test for URL: $url in {$executionTime}s");
        
        return $combinedResults;
    }
    
    /**
     * Run tests using CheckHost with improved error handling
     * 
     * @param string $url URL to test
     * @param bool $useAllNodes Whether to use all available nodes
     * @return array CheckHost results
     */
    protected function runCheckHostTests($url, $useAllNodes = false)
    {
        try {
            // Get domain from URL
            $domain = parse_url($url, PHP_URL_HOST);
            if (empty($domain)) {
                $domain = $url;
            }
            
            $checkHostApi = new CheckHostApi($domain);
            
            // Determine which nodes to use
            $nodesToUse = [];
            
            if ($useAllNodes) {
                // Get all available nodes
                $nodesToUse = $checkHostApi->getAvailableNodes();
                // Limit to 30 nodes maximum to prevent timeouts
                if (count($nodesToUse) > 30) {
                    $nodesToUse = array_slice($nodesToUse, 0, 30);
                }
                Log::info("Using all available nodes: " . count($nodesToUse) . " nodes");
            } else {
                // Use only selected nodes
                $nodesToUse = $this->getValidNodes($checkHostApi);
                Log::info("Using selected nodes: " . count($nodesToUse) . " nodes");
            }
            
            // Add selected nodes
            foreach ($nodesToUse as $node) {
                $checkHostApi->node($node);
            }
            
            // Set longer timeout for more nodes - increased for better reliability
            $timeoutSeconds = $useAllNodes ? 40 : 25;
            $checkHostApi->setTimeout($timeoutSeconds);
            
            // Increase wait time as well
            $checkHostApi->setWaitTime($timeoutSeconds - 5);
            
            // Run HTTP test
            $httpResults = $checkHostApi->http();
            
            // Check if results are empty or error
            if (empty($httpResults) || (is_array($httpResults) && isset($httpResults['error']))) {
                Log::warning("CheckHost HTTP test returned empty or error result", [
                    'results' => $httpResults
                ]);
                
                // Try one more time with fewer nodes
                if (count($nodesToUse) > 5) {
                    $checkHostApi->resetNodes();
                    $reducedNodes = array_slice($nodesToUse, 0, 5);
                    
                    foreach ($reducedNodes as $node) {
                        $checkHostApi->node($node);
                    }
                    
                    Log::info("Retrying CheckHost with fewer nodes: " . count($reducedNodes));
                    $httpResults = $checkHostApi->http();
                }
            }
            
            // Process HTTP results and include additional data
            $enhancedHttpResults = $this->enhanceHttpResults($httpResults);
            
            // Run Ping test (in parallel, but with fewer nodes to be quicker)
            $checkHostApi->resetNodes();
            $pingNodes = $useAllNodes 
                ? array_slice($nodesToUse, 0, 10) 
                : array_slice($nodesToUse, 0, 5);
                
            foreach ($pingNodes as $node) {
                $checkHostApi->node($node);
            }
            $pingResults = $checkHostApi->ping();
            
            // Combine results with enhanced data
            return [
                'http' => $enhancedHttpResults,
                'ping' => $pingResults,
                'domain' => $domain,
                'timestamp' => time(),
                'nodes_used' => $nodesToUse,
                'node_details' => $this->getNodeDetails($nodesToUse)
            ];
        } catch (\Exception $e) {
            Log::error("CheckHost test error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return [
                'error' => $e->getMessage(),
                'domain' => $domain ?? '',
                'timestamp' => time()
            ];
        }
    }
    
    /**
     * Enhance HTTP results with additional data for better display
     * Improved error handling and null value prevention
     *
     * @param array $httpResults Raw HTTP results from CheckHost
     * @return array Enhanced HTTP results
     */
    protected function enhanceHttpResults($httpResults)
    {
        if (!is_array($httpResults)) {
            return [];
        }
        
        $enhanced = [];
        
        foreach ($httpResults as $node => $results) {
            // Initialize with default values to prevent nulls
            $enhanced[$node] = [
                'status' => 'offline',
                'response_time_ms' => null
            ];
            
            // Skip if results are not properly formatted
            if (!is_array($results) || empty($results)) {
                continue;
            }
            
            try {
                // Copy the original result
                $enhanced[$node] = array_merge($enhanced[$node], $results);
                
                // Pattern 1: [[1, 0.123, "Found", "302", "IP"]] (most common) 
                if (isset($results[0]) && is_array($results[0]) && count($results[0]) >= 2) {
                    $isSuccess = !empty($results[0][0]);
                    $responseTime = isset($results[0][1]) ? $results[0][1] : null;
                    
                    if ($isSuccess) {
                        // Add response time in milliseconds for easier frontend processing
                        $enhanced[$node]['response_time_ms'] = $responseTime ? round($responseTime * 1000) : null;
                        
                        // Add status classification
                        if ($responseTime) {
                            if ($responseTime < 0.3) {
                                $enhanced[$node]['status'] = 'online';
                            } else if ($responseTime < 0.8) {
                                $enhanced[$node]['status'] = 'slow';
                            } else {
                                $enhanced[$node]['status'] = 'slow';
                            }
                        } else {
                            $enhanced[$node]['status'] = 'online';
                        }
                    } else {
                        $enhanced[$node]['status'] = 'offline';
                    }
                }
                // Pattern 2: [1, 0.123, "Found", "302", "IP"] (alternative format)
                else if (isset($results[0]) && is_numeric($results[0]) && isset($results[1]) && is_numeric($results[1])) {
                    $isSuccess = !empty($results[0]);
                    $responseTime = $results[1];
                    
                    if ($isSuccess) {
                        $enhanced[$node]['response_time_ms'] = round($responseTime * 1000);
                        
                        if ($responseTime < 0.3) {
                            $enhanced[$node]['status'] = 'online';
                        } else if ($responseTime < 0.8) {
                            $enhanced[$node]['status'] = 'slow';
                        } else {
                            $enhanced[$node]['status'] = 'slow';
                        }
                    } else {
                        $enhanced[$node]['status'] = 'offline';
                    }
                }
                // Status field exists directly (special case for some responses)
                else if (isset($results['status'])) {
                    $enhanced[$node]['status'] = $results['status'];
                    
                    // If both response_time_ms and time exist, make sure response_time_ms is set
                    if (!isset($enhanced[$node]['response_time_ms']) && isset($results['time'])) {
                        if (is_numeric($results['time'])) {
                            // Check if time is already in ms or seconds
                            if ($results['time'] < 100) {
                                // Convert from seconds to ms
                                $enhanced[$node]['response_time_ms'] = round($results['time'] * 1000);
                            } else {
                                // Already in ms
                                $enhanced[$node]['response_time_ms'] = round($results['time']);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error enhancing HTTP results for node {$node}: " . $e->getMessage());
                // Ensure we have default values
                $enhanced[$node]['status'] = 'offline';
                $enhanced[$node]['response_time_ms'] = null;
                $enhanced[$node]['error'] = "Processing error: " . $e->getMessage();
            }
        }
        
        return $enhanced;
    }
    
    /**
     * Get detailed information about nodes
     *
     * @param array $nodes Node IDs
     * @return array Node details
     */
    protected function getNodeDetails($nodes)
    {
        // Node details mapping (country, region, etc.)
        $nodeDetails = [];
        
        // Common locations for known nodes
        $locations = [
            'us1' => ['country' => 'US', 'name' => 'United States (East)', 'region' => 'North America'],
            'us2' => ['country' => 'US', 'name' => 'United States (West)', 'region' => 'North America'],
            'us3' => ['country' => 'US', 'name' => 'United States (Central)', 'region' => 'North America'],
            'uk1' => ['country' => 'GB', 'name' => 'United Kingdom', 'region' => 'Europe'],
            'de1' => ['country' => 'DE', 'name' => 'Germany', 'region' => 'Europe'],
            'fr1' => ['country' => 'FR', 'name' => 'France', 'region' => 'Europe'],
            'nl1' => ['country' => 'NL', 'name' => 'Netherlands', 'region' => 'Europe'],
            'jp1' => ['country' => 'JP', 'name' => 'Japan', 'region' => 'Asia'],
            'sg1' => ['country' => 'SG', 'name' => 'Singapore', 'region' => 'Asia'],
            'in1' => ['country' => 'IN', 'name' => 'India', 'region' => 'Asia'],
            'au1' => ['country' => 'AU', 'name' => 'Australia', 'region' => 'Oceania'],
            'br1' => ['country' => 'BR', 'name' => 'Brazil', 'region' => 'South America']
        ];
        
        foreach ($nodes as $node) {
            if (isset($locations[$node])) {
                $nodeDetails[$node] = $locations[$node];
            } else {
                // For unknown nodes, extract country code and guess region
                $countryCode = substr($node, 0, 2);
                $region = $this->getRegionFromNode($node);
                
                $nodeDetails[$node] = [
                    'country' => strtoupper($countryCode),
                    'name' => $node,
                    'region' => $region
                ];
            }
        }
        
        return $nodeDetails;
    }
    
    /**
     * Get list of valid nodes from the selected nodes
     * 
     * @param CheckHostApi $api
     * @param int $limit Limit number of nodes
     * @return array Node list
     */
    protected function getValidNodes($api, $limit = 12)
    {
        $availableNodes = $api->getAvailableNodes();
        $validNodes = array_intersect($this->selectedNodes, $availableNodes);
        
        // Ensure we have enough nodes from different regions
        if (count($validNodes) < 5 && count($availableNodes) > 5) {
            // Add more nodes until we reach at least 5
            foreach ($availableNodes as $node) {
                if (!in_array($node, $validNodes)) {
                    $validNodes[] = $node;
                    if (count($validNodes) >= 5) break;
                }
            }
        }
        
        // Limit number of nodes
        return array_slice($validNodes, 0, $limit);
    }
    
    /**
     * Create empty combined stats structure based on server-side results
     *
     * @param array $serverSideResults
     * @return array Empty combined stats structure
     */
    protected function createEmptyCombinedStats($serverSideResults)
    {
        // Get regions from server-side results
        $regions = [];
        if (isset($serverSideResults['stats']['region_stats'])) {
            $regions = array_keys($serverSideResults['stats']['region_stats']);
        }
        
        // Create basic stats structure
        $stats = [
            'total_nodes_tested' => isset($serverSideResults['results']) ? count($serverSideResults['results']) : 0,
            'successful_nodes' => 0,
            'average_response_time' => isset($serverSideResults['stats']['average_time']) ? $serverSideResults['stats']['average_time'] : null,
            'median_response_time' => isset($serverSideResults['stats']['median_time']) ? $serverSideResults['stats']['median_time'] : null,
            'global_availability' => isset($serverSideResults['stats']['success_rate']) ? $serverSideResults['stats']['success_rate'] : 0,
            'response_times_by_region' => [],
            'availability_by_region' => [],
        ];
        
        // Count successful nodes
        if (isset($serverSideResults['results'])) {
            foreach ($serverSideResults['results'] as $result) {
                if ($result['status'] === 'online' || $result['status'] === 'slow') {
                    $stats['successful_nodes']++;
                }
            }
        }
        
        // Add regions
        foreach ($regions as $region) {
            if (isset($serverSideResults['stats']['region_stats'][$region])) {
                $regionStats = $serverSideResults['stats']['region_stats'][$region];
                
                // Add response time
                $stats['response_times_by_region'][$region] = $regionStats['avgResponseTime'] ?? null;
                
                // Add availability
                if (isset($regionStats['total']) && $regionStats['total'] > 0) {
                    $available = ($regionStats['online'] ?? 0) + ($regionStats['slow'] ?? 0);
                    $percentage = round(($available / $regionStats['total']) * 100, 1);
                    
                    $stats['availability_by_region'][$region] = [
                        'total' => $regionStats['total'],
                        'successful' => $available,
                        'percentage' => $percentage
                    ];
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * Calculate combined statistics with improved accuracy and error handling
     * 
     * @param array $serverSideResults
     * @param array $checkHostResults
     * @return array Combined statistics
     */
    protected function calculateCombinedStats($serverSideResults, $checkHostResults)
    {
        // Validate input data
        if (!is_array($serverSideResults) || !isset($serverSideResults['results'])) {
            Log::warning("Invalid serverSideResults in calculateCombinedStats");
            return $this->createEmptyCombinedStats([]);
        }
        
        if (!is_array($checkHostResults)) {
            Log::warning("Invalid checkHostResults in calculateCombinedStats");
            $checkHostResults = ['http' => []];
        }
        
        $stats = [
            'total_nodes_tested' => 0,
            'successful_nodes' => 0,
            'average_response_time' => 0,
            'median_response_time' => 0,
            'global_availability' => 0,
            'response_times_by_region' => [],
            'availability_by_region' => [],
        ];
        
        // Process server-side results
        $responseTimes = [];
        
        if (isset($serverSideResults['results']) && is_array($serverSideResults['results'])) {
            $stats['total_nodes_tested'] += count($serverSideResults['results']);
            
            foreach ($serverSideResults['results'] as $result) {
                if ($result['status'] === 'online' || $result['status'] === 'slow') {
                    $stats['successful_nodes']++;
                    
                    if (isset($result['time']) && $result['time'] > 0) {
                        $responseTimes[] = $result['time'];
                        
                        // Categorize by region
                        $region = $result['region'] ?? 'Unknown';
                        if (!isset($stats['response_times_by_region'][$region])) {
                            $stats['response_times_by_region'][$region] = [];
                            $stats['availability_by_region'][$region] = [
                                'total' => 0,
                                'successful' => 0
                            ];
                        }
                        
                        $stats['response_times_by_region'][$region][] = $result['time'];
                        $stats['availability_by_region'][$region]['total']++;
                        $stats['availability_by_region'][$region]['successful']++;
                    }
                } else {
                    // Unsuccessful
                    $region = $result['region'] ?? 'Unknown';
                    if (!isset($stats['availability_by_region'][$region])) {
                        $stats['availability_by_region'][$region] = [
                            'total' => 0,
                            'successful' => 0
                        ];
                    }
                    $stats['availability_by_region'][$region]['total']++;
                }
            }
        }
        
        // Process CheckHost results
        if (isset($checkHostResults['http']) && is_array($checkHostResults['http'])) {
            foreach ($checkHostResults['http'] as $node => $result) {
                $stats['total_nodes_tested']++;
                
                // Extract status and response time with improved handling
                $isSuccessful = false;
                $responseTime = null;
                
                // Handle different result formats
                if (is_array($result)) {
                    if (isset($result['status'])) {
                        $isSuccessful = $result['status'] === 'online' || $result['status'] === 'slow';
                        $responseTime = $result['response_time_ms'] ?? null;
                    } else if (isset($result[0]) && is_array($result[0]) && count($result[0]) >= 2) {
                        $isSuccessful = !empty($result[0][0]);
                        // Response time could be at index 1 or in a special field we added
                        if (isset($result[1]) && is_numeric($result[1])) {
                            // Convert from seconds to milliseconds
                            $responseTime = round($result[1] * 1000);
                        }
                    } else if (isset($result[0]) && is_numeric($result[0]) && isset($result[1]) && is_numeric($result[1])) {
                        $isSuccessful = !empty($result[0]);
                        $responseTime = round($result[1] * 1000);
                    }
                }
                
                if ($isSuccessful) {
                    $stats['successful_nodes']++;
                    
                    if ($responseTime) {
                        $responseTimes[] = $responseTime;
                        
                        // Determine region from node
                        $region = $this->getRegionFromNode($node);
                        if (!isset($stats['response_times_by_region'][$region])) {
                            $stats['response_times_by_region'][$region] = [];
                            $stats['availability_by_region'][$region] = [
                                'total' => 0,
                                'successful' => 0
                            ];
                        }
                        
                        $stats['response_times_by_region'][$region][] = $responseTime;
                        $stats['availability_by_region'][$region]['total']++;
                        $stats['availability_by_region'][$region]['successful']++;
                    }
                } else {
                    // Unsuccessful
                    $region = $this->getRegionFromNode($node);
                    if (!isset($stats['availability_by_region'][$region])) {
                        $stats['availability_by_region'][$region] = [
                            'total' => 0,
                            'successful' => 0
                        ];
                    }
                    $stats['availability_by_region'][$region]['total']++;
                }
            }
        }
        
        // Calculate average and median
        if (!empty($responseTimes)) {
            $stats['average_response_time'] = round(array_sum($responseTimes) / count($responseTimes));
            
            // Calculate median
            sort($responseTimes);
            $mid = floor(count($responseTimes) / 2);
            if (count($responseTimes) % 2 == 0) {
                $stats['median_response_time'] = round(($responseTimes[$mid-1] + $responseTimes[$mid]) / 2);
            } else {
                $stats['median_response_time'] = $responseTimes[$mid];
            }
        }
        
        // Calculate global availability
        if ($stats['total_nodes_tested'] > 0) {
            $stats['global_availability'] = round(($stats['successful_nodes'] / $stats['total_nodes_tested']) * 100, 1);
        }
        
        // Calculate average response time by region
        foreach ($stats['response_times_by_region'] as $region => $times) {
            if (!empty($times)) {
                $stats['response_times_by_region'][$region] = round(array_sum($times) / count($times));
            } else {
                $stats['response_times_by_region'][$region] = null;
            }
        }
        
        // Calculate availability percentage by region
        foreach ($stats['availability_by_region'] as $region => $data) {
            if ($data['total'] > 0) {
                $stats['availability_by_region'][$region]['percentage'] = round(($data['successful'] / $data['total']) * 100, 1);
            } else {
                $stats['availability_by_region'][$region]['percentage'] = 0;
            }
        }
        
        return $stats;
    }
    
    /**
     * Determine region from node code with extended coverage
     * 
     * @param string $node
     * @return string
     */
    protected function getRegionFromNode($node)
    {
        // Extract country code from node name
        $countryCode = substr($node, 0, 2);
        
        // Classify by country code (expanded)
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
            'ch' => 'Europe',
            'at' => 'Europe',
            'pl' => 'Europe',
            'ru' => 'Europe',
            'ua' => 'Europe',
            'tr' => 'Europe',
            'se' => 'Europe',
            'no' => 'Europe',
            'fi' => 'Europe',
            'dk' => 'Europe',
            'cz' => 'Europe',
            'hu' => 'Europe',
            'ro' => 'Europe',
            'be' => 'Europe',
            'pt' => 'Europe',
            'ie' => 'Europe',
            'gr' => 'Europe',
            
            // Asia
            'jp' => 'Asia',
            'cn' => 'Asia',
            'sg' => 'Asia',
            'in' => 'Asia',
            'kr' => 'Asia',
            'hk' => 'Asia',
            'th' => 'Asia',
            'vn' => 'Asia',
            'my' => 'Asia',
            'id' => 'Asia',
            'ph' => 'Asia',
            'ae' => 'Asia',
            'il' => 'Asia',
            
            // Oceania
            'au' => 'Oceania',
            'nz' => 'Oceania',
            'fj' => 'Oceania',
            
            // South America
            'br' => 'South America',
            'ar' => 'South America',
            'cl' => 'South America',
            'co' => 'South America',
            'pe' => 'South America',
            've' => 'South America',
            
            // Africa
            'za' => 'Africa',
            'eg' => 'Africa',
            'ng' => 'Africa',
            'ke' => 'Africa',
            'ma' => 'Africa'
        ];
        
        return $regions[$countryCode] ?? 'Other';
    }
    
    /**
     * Normalize URL
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
}