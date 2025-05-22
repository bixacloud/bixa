<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IperfServer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ip_address',
        'port',
        'country_code',
        'country_name',
        'provider',
        'last_checked_at',
        'is_active'
    ];
    
    protected $casts = [
        'last_checked_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get random active servers grouped by region
     *
     * @param int $perRegion Number of servers to return per region
     * @return array
     */
    public static function getRandomByRegion($perRegion = 2)
    {
        $regions = [
            'North America' => ['US', 'CA', 'MX'],
            'Europe' => ['GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'SE', 'CH', 'FI', 'NO', 'DK', 'PL', 'RU'],
            'Asia' => ['JP', 'CN', 'SG', 'IN', 'KR', 'HK', 'TW', 'MY', 'TH', 'VN', 'PH', 'ID'],
            'Oceania' => ['AU', 'NZ'],
            'South America' => ['BR', 'AR', 'CL', 'CO', 'PE'],
            'Africa' => ['ZA', 'EG', 'NG', 'KE', 'MA']
        ];
        
        $result = [];
        
        foreach ($regions as $regionName => $countryCodes) {
            // Get active servers from this region
            $servers = self::whereIn('country_code', $countryCodes)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit($perRegion)
                ->get();
            
            if ($servers->isNotEmpty()) {
                foreach ($servers as $server) {
                    $result[] = [
                        'location' => $server->country_name . ' (' . $server->provider . ')',
                        'country' => $server->country_code,
                        'ip' => $server->ip_address,
                        'port' => $server->port,
                        'region' => $regionName
                    ];
                }
            }
        }
        
        // If we don't have at least 5 servers, get more random ones
        if (count($result) < 5) {
            $additionalServers = self::where('is_active', true)
                ->inRandomOrder()
                ->limit(10 - count($result))
                ->get();
                
            foreach ($additionalServers as $server) {
                // Check if this server is already in the results
                $exists = false;
                foreach ($result as $existingServer) {
                    if ($existingServer['ip'] === $server->ip_address) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    // FIX: Using self:: instead of $this-> for static method
                    $result[] = [
                        'location' => $server->country_name . ' (' . $server->provider . ')',
                        'country' => $server->country_code,
                        'ip' => $server->ip_address,
                        'port' => $server->port,
                        'region' => self::getRegionForCountry($server->country_code)
                    ];
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Get random active servers without region grouping
     *
     * @param int $count Number of servers to return
     * @return array
     */
    public static function getRandomServers($count = 10)
    {
        $servers = self::where('is_active', true)
            ->inRandomOrder()
            ->limit($count)
            ->get();
            
        $result = [];
        
        foreach ($servers as $server) {
            $result[] = [
                'location' => $server->country_name . ' (' . $server->provider . ')',
                'country' => $server->country_code,
                'ip' => $server->ip_address,
                'port' => $server->port
            ];
        }
        
        return $result;
    }
    
    /**
     * Get the region for a country code
     * 
     * @param string $countryCode
     * @return string
     */
    private static function getRegionForCountry($countryCode)
    {
        $regions = [
            'North America' => ['US', 'CA', 'MX'],
            'Europe' => ['GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'SE', 'CH', 'FI', 'NO', 'DK', 'PL', 'RU'],
            'Asia' => ['JP', 'CN', 'SG', 'IN', 'KR', 'HK', 'TW', 'MY', 'TH', 'VN', 'PH', 'ID'],
            'Oceania' => ['AU', 'NZ'],
            'South America' => ['BR', 'AR', 'CL', 'CO', 'PE'],
            'Africa' => ['ZA', 'EG', 'NG', 'KE', 'MA']
        ];
        
        foreach ($regions as $region => $countries) {
            if (in_array($countryCode, $countries)) {
                return $region;
            }
        }
        
        return 'Other';
    }
}