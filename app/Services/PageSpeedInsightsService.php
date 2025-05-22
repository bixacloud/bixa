<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class PageSpeedInsightsService
{
    /**
     * API key for Google PageSpeed Insights
     */
    protected $apiKey;
    
    /**
     * Timeout value in seconds
     */
    protected $timeout = 120; 
    
    /**
     * Constructor with API key
     */
    public function __construct()
    {
        $this->apiKey = Setting::get('pagespeed_api_key', null);
    }
    
    /**
     * Run PageSpeed Insights test
     * Adjusted to correctly pass multiple category parameters
     *
     * @param string $url URL to test
     * @param string $strategy either 'mobile' or 'desktop'
     * @return array|null PageSpeed results or null on failure
     */
    public function runPageSpeedTest($url, $strategy = 'mobile')
    {
        try {
            $apiUrl = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
            
            // Base parameters
            $params = [
                'url' => $url,
                'strategy' => $strategy,
            ];
            
            // Add categories separately as URL parameters
            $params['category'] = ['PERFORMANCE', 'ACCESSIBILITY', 'BEST_PRACTICES', 'SEO'];
            
            // Add API key if available
            if ($this->apiKey) {
                $params['key'] = $this->apiKey;
            }
            
            Log::info("Calling PageSpeed API for URL: {$url} with strategy: {$strategy}");
            
            // Build the URL manually to ensure correct parameter format
            $requestUrl = $apiUrl . '?url=' . urlencode($url) . '&strategy=' . $strategy;
            foreach ($params['category'] as $category) {
                $requestUrl .= '&category=' . $category;
            }
            if ($this->apiKey) {
                $requestUrl .= '&key=' . $this->apiKey;
            }
            
            Log::debug("Calling PageSpeed API with URL: " . $requestUrl);
            
            // Make request with manual URL and empty params
            $response = Http::timeout($this->timeout)
                ->retry(2, 5000)
                ->get($requestUrl);
            
            if ($response->successful()) {
                Log::info("PageSpeed API response successful for URL: {$url}");
                return $response->json();
            } else {
                Log::error("PageSpeed API error: " . $response->status() . " - " . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('PageSpeed Insights error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Extract key metrics from PageSpeed results with improved field handling
     * Now extracts scores directly from the categories structure
     *
     * @param array $pageSpeedResults Full PageSpeed results
     * @return array Key metrics in a simplified format
     */
    public function extractKeyMetrics($pageSpeedResults)
    {
        if (!$pageSpeedResults || !isset($pageSpeedResults['lighthouseResult'])) {
            Log::error('PageSpeed: Missing lighthouseResult in response');
            return null;
        }
        
        $lighthouse = $pageSpeedResults['lighthouseResult'];
        
        // Debug log to see the structure
        Log::debug('PageSpeed lighthouse result structure:', array_keys($lighthouse));
        
        // Verify categories exist
        if (!isset($lighthouse['categories'])) {
            Log::error('PageSpeed: Missing categories in lighthouseResult');
            return null;
        }
        
        // Log categories for debugging
        Log::debug('PageSpeed categories:', array_keys($lighthouse['categories']));
        
        // Extract scores directly from categories (scores are 0-1, multiply by 100)
        $scores = [];
        
        // Performance score
        if (isset($lighthouse['categories']['performance']['score'])) {
            $scores['performance'] = round($lighthouse['categories']['performance']['score'] * 100);
        }
        
        // Accessibility score
        if (isset($lighthouse['categories']['accessibility']['score'])) {
            $scores['accessibility'] = round($lighthouse['categories']['accessibility']['score'] * 100);
        }
        
        // Best Practices score - support both formats
        if (isset($lighthouse['categories']['best-practices']['score'])) {
            $bestPracticesScore = round($lighthouse['categories']['best-practices']['score'] * 100);
            $scores['best_practices'] = $bestPracticesScore; 
            $scores['best-practices'] = $bestPracticesScore;
            $scores['bestPractices'] = $bestPracticesScore;
        }
        
        // SEO score
        if (isset($lighthouse['categories']['seo']['score'])) {
            $scores['seo'] = round($lighthouse['categories']['seo']['score'] * 100);
        }
        
        // Log pour debugging
        Log::debug('PageSpeed scores extracted:', $scores);
        
        // Get key metrics from audits - Keep this part unchanged
        $metrics = [];
        
        if (isset($lighthouse['audits'])) {
            $audits = $lighthouse['audits'];
            
            // First Contentful Paint
            if (isset($audits['first-contentful-paint'])) {
                $metrics['first_contentful_paint'] = [
                    'title' => 'First Contentful Paint',
                    'value' => $audits['first-contentful-paint']['displayValue'],
                    'score' => $audits['first-contentful-paint']['score'],
                    'description' => $audits['first-contentful-paint']['description'] ?? null
                ];
            }
            
            // Largest Contentful Paint
            if (isset($audits['largest-contentful-paint'])) {
                $metrics['largest_contentful_paint'] = [
                    'title' => 'Largest Contentful Paint',
                    'value' => $audits['largest-contentful-paint']['displayValue'],
                    'score' => $audits['largest-contentful-paint']['score'],
                    'description' => $audits['largest-contentful-paint']['description'] ?? null
                ];
            }
            
            // Speed Index
            if (isset($audits['speed-index'])) {
                $metrics['speed_index'] = [
                    'title' => 'Speed Index',
                    'value' => $audits['speed-index']['displayValue'],
                    'score' => $audits['speed-index']['score'],
                    'description' => $audits['speed-index']['description'] ?? null
                ];
            }
            
            // Time to Interactive
            if (isset($audits['interactive'])) {
                $metrics['time_to_interactive'] = [
                    'title' => 'Time to Interactive',
                    'value' => $audits['interactive']['displayValue'],
                    'score' => $audits['interactive']['score'],
                    'description' => $audits['interactive']['description'] ?? null
                ];
            }
            
            // Total Blocking Time
            if (isset($audits['total-blocking-time'])) {
                $metrics['total_blocking_time'] = [
                    'title' => 'Total Blocking Time',
                    'value' => $audits['total-blocking-time']['displayValue'],
                    'score' => $audits['total-blocking-time']['score'],
                    'description' => $audits['total-blocking-time']['description'] ?? null
                ];
            }
            
            // Cumulative Layout Shift
            if (isset($audits['cumulative-layout-shift'])) {
                $metrics['cumulative_layout_shift'] = [
                    'title' => 'Cumulative Layout Shift',
                    'value' => $audits['cumulative-layout-shift']['displayValue'],
                    'score' => $audits['cumulative-layout-shift']['score'],
                    'description' => $audits['cumulative-layout-shift']['description'] ?? null
                ];
            }
        }
        
        // Get lab data
        $labData = isset($lighthouse['audits']['metrics']['details']['items'][0]) 
            ? $lighthouse['audits']['metrics']['details']['items'][0] 
            : null;
        
        // Get opportunities and diagnostics
        $opportunities = [];
        $diagnostics = [];
        
        if (isset($lighthouse['categories']['performance']['auditRefs'])) {
            $performanceAudits = $lighthouse['categories']['performance']['auditRefs'];
            
            foreach ($performanceAudits as $auditRef) {
                $id = $auditRef['id'];
                
                // Skip if not an opportunity or diagnostic
                if (!isset($auditRef['group']) || !in_array($auditRef['group'], ['load-opportunities', 'diagnostics'])) {
                    continue;
                }
                
                if (!isset($lighthouse['audits'][$id]) || $lighthouse['audits'][$id]['score'] === 1) {
                    continue;
                }
                
                $audit = $lighthouse['audits'][$id];
                $auditData = [
                    'id' => $id,
                    'title' => $audit['title'],
                    'description' => $audit['description'],
                    'score' => $audit['score'],
                    'display_value' => $audit['displayValue'] ?? null
                ];
                
                if ($auditRef['group'] === 'load-opportunities') {
                    $opportunities[] = $auditData;
                } elseif ($auditRef['group'] === 'diagnostics') {
                    $diagnostics[] = $auditData;
                }
            }
        }
        
        // Construct final result
        return [
            'scores' => $scores,
            'metrics' => $metrics,
            'lab_data' => $labData,
            'opportunities' => $opportunities,
            'diagnostics' => $diagnostics,
            'strategy' => $lighthouse['configSettings']['emulatedFormFactor'],
            'fetch_time' => $lighthouse['fetchTime'],
            'url' => $lighthouse['finalUrl']
        ];
    }
    
    /**
     * Set timeout for API requests
     *
     * @param int $seconds
     * @return $this
     */
    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
        return $this;
    }
}