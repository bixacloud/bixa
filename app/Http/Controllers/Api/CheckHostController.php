<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Alirezax5\CheckHost\CheckHost;

class CheckHostController extends Controller
{
    /**
     * Cache TTL in minutes
     */
    protected $cacheTtl = 15;
    
    /**
     * Run comprehensive tests from CheckHost
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function runTests(Request $request)
    {
        try {
            // Validate request
            $validator = $request->validate([
                'url' => 'required|string',
                'nodes' => 'sometimes|array'
            ]);
            
            $url = $request->input('url');
            $nodes = $request->input('nodes', []);
            
            // Normalize URL (add protocol if missing)
            if (!preg_match('/^https?:\/\//i', $url)) {
                $url = 'https://' . $url;
            }
            
            // Generate cache key
            $cacheKey = 'checkhost_test_' . md5($url . implode(',', $nodes));
            
            // Try to get from cache first
            if (Cache::has($cacheKey)) {
                return response()->json([
                    'success' => true,
                    'test_results' => Cache::get($cacheKey),
                    'from_cache' => true
                ]);
            }
            
            // Create CheckHost client
            $checkHost = new CheckHost($url);
            
            // Add selected nodes or defaults
            if (!empty($nodes)) {
                foreach ($nodes as $node) {
                    $checkHost->node($node);
                }
            } else {
                // Default nodes for better geographical coverage
                $defaultNodes = ['us1', 'uk1', 'de1', 'fr1', 'jp1', 'sg1', 'au1', 'br1', 'in1', 'ru1'];
                foreach ($defaultNodes as $node) {
                    $checkHost->node($node);
                }
            }
            
            // Run tests
            $startTime = microtime(true);
            
            $results = [
                'ping' => $this->runPingTest($checkHost),
                'http' => $this->runHttpTest($checkHost),
                'tcp' => $this->runTcpTest($checkHost),
                'dns' => $this->runDnsTest($checkHost),
            ];
            
            $executionTime = round((microtime(true) - $startTime), 2);
            
            // Cache results
            Cache::put($cacheKey, $results, $this->cacheTtl * 60);
            
            // Return results
            return response()->json([
                'success' => true,
                'test_results' => $results,
                'execution_time' => $executionTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('CheckHost API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error running CheckHost tests: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Run ping test
     *
     * @param CheckHost $client
     * @return array|null
     */
    protected function runPingTest($client)
    {
        try {
            return $client->ping();
        } catch (\Exception $e) {
            Log::error('Ping test error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Run HTTP test
     *
     * @param CheckHost $client
     * @return array|null
     */
    protected function runHttpTest($client)
    {
        try {
            return $client->http();
        } catch (\Exception $e) {
            Log::error('HTTP test error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Run TCP test
     *
     * @param CheckHost $client
     * @return array|null
     */
    protected function runTcpTest($client)
    {
        try {
            return $client->tcp();
        } catch (\Exception $e) {
            Log::error('TCP test error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Run DNS test
     *
     * @param CheckHost $client
     * @return array|null
     */
    protected function runDnsTest($client)
    {
        try {
            return $client->dns();
        } catch (\Exception $e) {
            Log::error('DNS test error: ' . $e->getMessage());
            return null;
        }
    }
}