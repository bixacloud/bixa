<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use Google_Client;
use Google_Service_Pagespeedonline;
use Google_Service_Pagespeedonline_PagespeedApiPagespeedResponseV5;

class PageSpeedController extends Controller
{
    /**
     * Google API Client
     *
     * @var Google_Client
     */
    protected $client;
    
    /**
     * PageSpeed Service
     *
     * @var Google_Service_Pagespeedonline
     */
    protected $pageSpeedService;
    
    /**
     * API Key
     *
     * @var string|null
     */
    protected $apiKey;
    
    /**
     * Cache TTL in minutes
     */
    protected $cacheTtl = 60;
    
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->apiKey = Setting::get('pagespeed_api_key', null);
        
        // Initialize Google API Client
        $this->client = new Google_Client();
        $this->client->setApplicationName('Website Speed Test');
        
        if ($this->apiKey) {
            $this->client->setDeveloperKey($this->apiKey);
        }
        
        // Create PageSpeed service
        $this->pageSpeedService = new Google_Service_Pagespeedonline($this->client);
    }
    
    /**
     * Run PageSpeed Insights test
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function runTest(Request $request)
    {
        try {
            // Validate request
            $validator = $request->validate([
                'url' => 'required|string',
                'strategy' => 'required|in:mobile,desktop',
                'categories' => 'sometimes|array'
            ]);
            
            $url = $request->input('url');
            $strategy = $request->input('strategy', 'mobile');
            $categories = $request->input('categories', ['performance']);
            
            // Normalize URL (add protocol if missing)
            if (!preg_match('/^https?:\/\//i', $url)) {
                $url = 'https://' . $url;
            }
            
            // Generate cache key
            $cacheKey = 'pagespeed_' . md5($url . $strategy . implode(',', $categories));
            
            // Try to get from cache first
            if (Cache::has($cacheKey)) {
                return response()->json([
                    'success' => true,
                    'has_error' => false,
                    'metrics' => Cache::get($cacheKey),
                    'from_cache' => true
                ]);
            }
            
            // Prepare the request options
            $optParams = [
                'strategy' => $strategy,
            ];
            
            // Add categories if specified
            if (!empty($categories) && $categories !== ['performance']) {
                $optParams['category'] = $categories;
            }
            
            // Start timing
            $startTime = microtime(true);
            
            // Run the PageSpeed test
            /** @var Google_Service_Pagespeedonline_PagespeedApiPagespeedResponseV5 $response */
            $response = $this->pageSpeedService->pagespeedapi->runpagespeed($url, $optParams);
            
            // Process the response
            $metrics = $this->processPageSpeedResponse($response);
            
            // Calculate execution time
            $executionTime = round((microtime(true) - $startTime), 2);
            
            // Cache the results
            Cache::put($cacheKey, $metrics, $this->cacheTtl * 60);
            
            // Return the results
            return response()->json([
                'success' => true,
                'has_error' => false,
                'metrics' => $metrics,
                'execution_time' => $executionTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('PageSpeed API error: ' . $e->getMessage());
            
            $errorMessage = $e->getMessage();
            $friendlyMessage = 'Error running PageSpeed test';
            
            if (strpos($errorMessage, 'timed out') !== false) {
                $friendlyMessage = 'Request timed out while analyzing the website. The website may be too large or complex.';
            } elseif (strpos($errorMessage, 'quota') !== false) {
                $friendlyMessage = 'API quota exceeded. Please try again later.';
            }
            
            return response()->json([
                'success' => true,
                'has_error' => true,
                'message' => $friendlyMessage
            ]);
        }
    }
    
    /**
     * Process PageSpeed API response
     *
     * @param Google_Service_Pagespeedonline_PagespeedApiPagespeedResponseV5 $response
     * @return array
     */
    protected function processPageSpeedResponse($response)
    {
        // Convert response to array for easier handling
        $data = $response->toSimpleObject();
        
        // Extract lighthouse results
        $lighthouse = $data->lighthouseResult;
        
        if (!$lighthouse) {
            return null;
        }
        
        // Extract key scores and metrics
        $results = [
            'scores' => $this->extractScores($lighthouse),
            'metrics' => $this->extractCoreWebVitals($lighthouse),
            'opportunities' => $this->extractOpportunities($lighthouse),
            'diagnostics' => $this->extractDiagnostics($lighthouse),
        ];
        
        return $results;
    }
    
    /**
     * Extract category scores from Lighthouse results
     *
     * @param object $lighthouse
     * @return array
     */
    protected function extractScores($lighthouse)
    {
        $scores = [
            'performance' => null,
            'accessibility' => null,
            'best_practices' => null,
            'seo' => null,
        ];
        
        // Get categories if available
        if (isset($lighthouse->categories)) {
            $categories = $lighthouse->categories;
            
            // Extract scores (multiply by 100 to get 0-100 scale)
            if (isset($categories->performance->score)) {
                $scores['performance'] = round($categories->performance->score * 100);
            }
            
            if (isset($categories->accessibility->score)) {
                $scores['accessibility'] = round($categories->accessibility->score * 100);
            }
            
            if (isset($categories->{'best-practices'}->score)) {
                $scores['best_practices'] = round($categories->{'best-practices'}->score * 100);
            }
            
            if (isset($categories->seo->score)) {
                $scores['seo'] = round($categories->seo->score * 100);
            }
        }
        
        return $scores;
    }
    
    /**
     * Extract Core Web Vitals metrics
     *
     * @param object $lighthouse
     * @return array
     */
    protected function extractCoreWebVitals($lighthouse)
    {
        $metrics = [];
        
        // Get audits if available
        if (isset($lighthouse->audits)) {
            $audits = $lighthouse->audits;
            
            // Largest Contentful Paint (LCP)
            if (isset($audits->{'largest-contentful-paint'})) {
                $lcp = $audits->{'largest-contentful-paint'};
                $metrics['largest_contentful_paint'] = [
                    'id' => 'largest-contentful-paint',
                    'title' => $lcp->title ?? 'Largest Contentful Paint',
                    'description' => $lcp->description ?? '',
                    'value' => $lcp->displayValue ?? 'Unknown',
                    'score' => $lcp->score ?? null,
                    'numeric_value' => $lcp->numericValue ?? null,
                ];
            }
            
            // Total Blocking Time (TBT) - a proxy for First Input Delay (FID) in lab data
            if (isset($audits->{'total-blocking-time'})) {
                $tbt = $audits->{'total-blocking-time'};
                $metrics['total_blocking_time'] = [
                    'id' => 'total-blocking-time',
                    'title' => $tbt->title ?? 'Total Blocking Time',
                    'description' => $tbt->description ?? '',
                    'value' => $tbt->displayValue ?? 'Unknown',
                    'score' => $tbt->score ?? null,
                    'numeric_value' => $tbt->numericValue ?? null,
                ];
            }
            
            // Cumulative Layout Shift (CLS)
            if (isset($audits->{'cumulative-layout-shift'})) {
                $cls = $audits->{'cumulative-layout-shift'};
                $metrics['cumulative_layout_shift'] = [
                    'id' => 'cumulative-layout-shift',
                    'title' => $cls->title ?? 'Cumulative Layout Shift',
                    'description' => $cls->description ?? '',
                    'value' => $cls->displayValue ?? 'Unknown',
                    'score' => $cls->score ?? null,
                    'numeric_value' => $cls->numericValue ?? null,
                ];
            }
            
            // First Contentful Paint (FCP)
            if (isset($audits->{'first-contentful-paint'})) {
                $fcp = $audits->{'first-contentful-paint'};
                $metrics['first_contentful_paint'] = [
                    'id' => 'first-contentful-paint',
                    'title' => $fcp->title ?? 'First Contentful Paint',
                    'description' => $fcp->description ?? '',
                    'value' => $fcp->displayValue ?? 'Unknown',
                    'score' => $fcp->score ?? null,
                    'numeric_value' => $fcp->numericValue ?? null,
                ];
            }
            
            // Speed Index
            if (isset($audits->{'speed-index'})) {
                $si = $audits->{'speed-index'};
                $metrics['speed_index'] = [
                    'id' => 'speed-index',
                    'title' => $si->title ?? 'Speed Index',
                    'description' => $si->description ?? '',
                    'value' => $si->displayValue ?? 'Unknown',
                    'score' => $si->score ?? null,
                    'numeric_value' => $si->numericValue ?? null,
                ];
            }
            
            // Time to Interactive
            if (isset($audits->{'interactive'})) {
                $tti = $audits->{'interactive'};
                $metrics['time_to_interactive'] = [
                    'id' => 'interactive',
                    'title' => $tti->title ?? 'Time to Interactive',
                    'description' => $tti->description ?? '',
                    'value' => $tti->displayValue ?? 'Unknown',
                    'score' => $tti->score ?? null,
                    'numeric_value' => $tti->numericValue ?? null,
                ];
            }
        }
        
        return $metrics;
    }
    
    /**
     * Extract optimization opportunities
     *
     * @param object $lighthouse
     * @return array
     */
    protected function extractOpportunities($lighthouse)
    {
        $opportunities = [];
        
        // Get audits if available
        if (isset($lighthouse->audits)) {
            // Common opportunity audit IDs
            $opportunityIds = [
                'render-blocking-resources',
                'uses-responsive-images',
                'offscreen-images',
                'unminified-css',
                'unminified-javascript',
                'unused-css-rules',
                'unused-javascript',
                'uses-optimized-images',
                'uses-webp-images',
                'uses-text-compression',
                'uses-rel-preconnect',
                'server-response-time',
                'redirects',
                'uses-rel-preload',
                'efficient-animated-content',
                'duplicated-javascript',
                'legacy-javascript',
                'preload-lcp-image',
                'total-byte-weight',
                'uses-long-cache-ttl',
                'dom-size',
                'critical-request-chains',
                'network-requests',
                'user-timings',
                'bootup-time',
                'mainthread-work-breakdown',
                'font-display',
                'resource-summary',
                'third-party-summary',
                'large-javascript-libraries',
                'lcp-lazy-loaded',
                'layout-shifts',
            ];
            
            $audits = $lighthouse->audits;
            
            // Extract opportunities from audits
            foreach ($opportunityIds as $id) {
                if (isset($audits->$id) && $audits->$id->score !== null && $audits->$id->score < 1) {
                    $audit = $audits->$id;
                    $opportunities[] = [
                        'id' => $id,
                        'title' => $audit->title ?? $id,
                        'description' => $audit->description ?? '',
                        'score' => $audit->score ?? null,
                        'display_value' => $audit->displayValue ?? null,
                    ];
                }
            }
            
            // Sort opportunities by score (ascending)
            usort($opportunities, function($a, $b) {
                return $a['score'] <=> $b['score'];
            });
        }
        
        return $opportunities;
    }
    
    /**
     * Extract diagnostics
     *
     * @param object $lighthouse
     * @return array
     */
    protected function extractDiagnostics($lighthouse)
    {
        $diagnostics = [];
        
        // Get audits if available
        if (isset($lighthouse->audits)) {
            // Common diagnostic audit IDs
            $diagnosticIds = [
                'first-contentful-paint-3g',
                'max-potential-fid',
                'first-meaningful-paint',
                'uses-passive-event-listeners',
                'no-document-write',
                'uses-http2',
                'no-vulnerable-libraries',
                'js-libraries',
                'third-party-facades',
                'third-party-summary',
                'performance-budget',
                'timing-budget',
                'non-composited-animations',
                'long-tasks',
                'unsized-images',
                'viewport',
                'meta-description',
                'http-status-code',
                'link-text',
                'crawlable-anchors',
                'is-on-https',
                'external-anchors-use-rel-noopener',
                'geolocation-on-start',
                'doctype',
                'charset',
                'inspector-issues',
                'valid-source-maps',
                'deprecations',
                'errors-in-console',
                'image-size-responsive',
                'image-aspect-ratio',
                'preload-fonts'
            ];
            
            $audits = $lighthouse->audits;
            
            // Extract diagnostics from audits
            foreach ($diagnosticIds as $id) {
                if (isset($audits->$id) && $audits->$id->score !== null && $audits->$id->score < 1) {
                    $audit = $audits->$id;
                    $diagnostics[] = [
                        'id' => $id,
                        'title' => $audit->title ?? $id,
                        'description' => $audit->description ?? '',
                        'score' => $audit->score ?? null,
                        'display_value' => $audit->displayValue ?? null,
                    ];
                }
            }
            
            // Sort diagnostics by score (ascending)
            usort($diagnostics, function($a, $b) {
                return $a['score'] <=> $b['score'];
            });
        }
        
        return $diagnostics;
    }
}