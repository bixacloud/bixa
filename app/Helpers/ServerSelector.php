<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ServerSelector
{
    /**
     * Get a list of random iPerf3 servers
     *
     * @param int $count Number of servers to retrieve
     * @return array
     */
    public static function getRandomIperfServers($count = 10)
    {
        // Check cache first to improve performance
        $cacheKey = 'iperf_servers_selection_' . $count;
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Default empty result
        $result = [];
        
        // Check if iperf_servers table exists in the database
        try {
            if (class_exists('\App\Models\IperfServer') && self::tableExists('iperf_servers')) {
                // If model and table exist, use the model
                $result = \App\Models\IperfServer::getRandomServers($count);
                
                // Cache the result for 30 minutes to improve performance
                if (!empty($result)) {
                    Cache::put($cacheKey, $result, 30 * 60);
                }
                
                return $result;
            } else {
                Log::warning('IperfServer model or table not found. Returning empty result.');
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error getting iPerf servers: ' . $e->getMessage());
            
            // Return empty array instead of throwing exception for graceful degradation
            return [];
        }
        
        return $result;
    }
    
    /**
     * Check if a table exists in the database
     *
     * @param string $tableName
     * @return bool
     */
    private static function tableExists($tableName)
    {
        try {
            // Check if table exists by querying the schema
            return Schema::hasTable($tableName);
        } catch (\Exception $e) {
            Log::error('Error checking if table exists: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
 * Get servers from a specific region
 *
 * @param string $region Region name (Asia, Europe, North America, etc.)
 * @param int $count Number of servers to retrieve
 * @return array
 */
public static function getRegionalServers($region, $count = 10)
{
    // Define country codes for each region
    $regionCountries = [
        'North America' => ['US', 'CA', 'MX'],
        'Europe' => ['GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'SE', 'CH', 'FI', 'NO', 'DK', 'PL', 'RU'],
        'Asia' => ['JP', 'CN', 'SG', 'IN', 'KR', 'HK', 'TW', 'MY', 'TH', 'VN', 'PH', 'ID'],
        'Oceania' => ['AU', 'NZ'],
        'South America' => ['BR', 'AR', 'CL', 'CO', 'PE'],
        'Africa' => ['ZA', 'EG', 'NG', 'KE', 'MA']
    ];
    
    // Check if the region exists
    if (!isset($regionCountries[$region])) {
        Log::warning("Invalid region: $region");
        return [];
    }
    
    $countryCodes = $regionCountries[$region];
    
    // Check cache first to improve performance
    $cacheKey = 'iperf_servers_' . $region . '_' . $count;
    
    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    }
    
    // Default empty result
    $result = [];
    
    // Check if iperf_servers table exists in the database
    try {
        if (class_exists('\App\Models\IperfServer') && self::tableExists('iperf_servers')) {
            // Use the model to query for servers in this region
            $servers = \App\Models\IperfServer::whereIn('country_code', $countryCodes)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit($count)
                ->get();
            
            foreach ($servers as $server) {
                $result[] = [
                    'location' => $server->country_name . ' (' . $server->provider . ')',
                    'country' => $server->country_code,
                    'ip' => $server->ip_address,
                    'port' => $server->port,
                    'region' => $region
                ];
            }
            
            // Cache the result for 30 minutes to improve performance
            if (!empty($result)) {
                Cache::put($cacheKey, $result, 30 * 60);
            }
        }
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error getting regional iPerf servers: ' . $e->getMessage());
    }
    
    return $result;
}
}