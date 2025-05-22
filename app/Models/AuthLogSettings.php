<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthLogSettings extends Model
{
    protected $table = 'auth_log_settings';
    
    protected $fillable = [
        'new_device_notification',
        'failed_login_notification',
        'location_tracking',
        'language_detection',
        'save_user_agent',
        'retention_days',
        'geoip_update_frequency'
    ];
    
    protected $casts = [
        'new_device_notification' => 'boolean',
        'failed_login_notification' => 'boolean',
        'location_tracking' => 'boolean',
        'language_detection' => 'boolean',
        'save_user_agent' => 'boolean',
        'retention_days' => 'integer',
        'geoip_update_frequency' => 'integer'
    ];
    
    /**
     * Get current settings or create default if not exists
     *
     * @return \App\Models\AuthLogSettings
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'new_device_notification' => true,
                'failed_login_notification' => true,
                'location_tracking' => true,
                'language_detection' => true,
                'save_user_agent' => true,
                'retention_days' => 90,
                'geoip_update_frequency' => 30 // Update GeoIP database every 30 days
            ]);
        }
        
        return $settings;
    }
}