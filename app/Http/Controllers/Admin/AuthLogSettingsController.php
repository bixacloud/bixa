<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthLogSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AuthLogSettingsController extends Controller
{
    /**
     * Show the authentication log settings form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = AuthLogSettings::getSettings();
        
        // Get GeoIP database status information if viewing settings
        $geoipInfo = $this->getGeoIPDatabaseInfo();
        
        return view('admin.auth-log-settings', compact('settings', 'geoipInfo'));
    }

    /**
     * Update the authentication log settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'retention_days' => 'nullable|integer|min:1|max:365',
            'geoip_update_frequency' => 'nullable|integer|min:1|max:90',
        ]);

        $settings = AuthLogSettings::getSettings();
        
        // Get the old values for comparison
        $oldLocationTracking = $settings->location_tracking;
        $oldLanguageDetection = $settings->language_detection;
        
        // Update settings
        $settings->update([
            'new_device_notification' => $request->has('new_device_notification'),
            'failed_login_notification' => $request->has('failed_login_notification'),
            'location_tracking' => $request->has('location_tracking'),
            'language_detection' => $request->has('language_detection'),
            'save_user_agent' => $request->has('save_user_agent'),
            'retention_days' => $request->input('retention_days', 90),
            'geoip_update_frequency' => $request->input('geoip_update_frequency', 30),
        ]);
        
        // If location or language features were enabled, check if we need to update GeoIP
        $newLocationTracking = $settings->location_tracking;
        $newLanguageDetection = $settings->language_detection;
        
        if ((!$oldLocationTracking && $newLocationTracking) || 
            (!$oldLanguageDetection && $newLanguageDetection)) {
            
            $geoipInfo = $this->getGeoIPDatabaseInfo();
            if ($geoipInfo['needsUpdate']) {
                // Trigger background update
                session(['trigger_geoip_update' => true]);
                
                return redirect()->route('admin.auth-log-settings')
                    ->with('info', 'Settings updated successfully. The GeoIP database will be updated in the background.');
            }
        }
        
        return redirect()->route('admin.auth-log-settings')
            ->with('success', 'Authentication log settings updated successfully.');
    }
    
    /**
     * Update the GeoIP database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGeoIP(Request $request)
    {
        // Set maximum execution time
        set_time_limit(300); // 5 minutes
        
        $licenseKey = $request->input('license_key') ?: config('geoip.license_key');
        
        if (empty($licenseKey)) {
            return redirect()->route('admin.auth-log-settings')
                ->with('error', 'MaxMind license key is not configured. Please set it in your .env file or enter it manually.');
        }
        
        try {
            // Create service instance
            $updater = new \App\Services\GeoIPUpdater();
            $result = $updater->update($licenseKey);
            
            if ($result['success']) {
                return redirect()->route('admin.auth-log-settings')
                    ->with('success', 'GeoIP database updated successfully.');
            } else {
                return redirect()->route('admin.auth-log-settings')
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.auth-log-settings')
                ->with('error', 'Failed to update GeoIP database: ' . $e->getMessage());
        }
    }
    
    /**
     * Update GeoIP database in the background
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function backgroundUpdateGeoIP()
    {
        try {
            // Set maximum execution time
            set_time_limit(300); // 5 minutes
            
            // Ignore user aborts
            ignore_user_abort(true);
            
            // Create service instance
            $updater = new \App\Services\GeoIPUpdater();
            $result = $updater->update();
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update GeoIP database: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Perform cleanup of old authentication logs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cleanup()
    {
        // Get retention period from settings
        $settings = AuthLogSettings::getSettings();
        $retentionDays = $settings->retention_days ?? 90;
        
        try {
            // Calculate cutoff date
            $cutoffDate = Carbon::now()->subDays($retentionDays);
            
            // Delete logs older than cutoff date
            $deleted = \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog::where('login_at', '<', $cutoffDate)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deleted} authentication logs older than {$retentionDays} days.",
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup authentication logs: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get information about GeoIP database
     * 
     * @return array
     */
    protected function getGeoIPDatabaseInfo()
    {
        // Last update time
        $lastUpdate = Storage::exists('geoip_last_update.txt') 
            ? Carbon::createFromTimestamp(Storage::get('geoip_last_update.txt')) 
            : null;
        
        // Database path and status
        $geoipPath = config('geoip.database_path');
        $databaseExists = file_exists($geoipPath);
        $databaseSize = $databaseExists ? $this->formatBytes(filesize($geoipPath)) : 'N/A';
        
        // Settings
        $settings = AuthLogSettings::getSettings();
        
        // Check if needs update
        $needsUpdate = false;
        if (!$lastUpdate) {
            $needsUpdate = true;
        } else {
            $nextUpdateDate = $lastUpdate->addDays($settings->geoip_update_frequency);
            $needsUpdate = $nextUpdateDate->isPast();
        }
        
        // Next update date
        $nextUpdate = $lastUpdate 
            ? $lastUpdate->addDays($settings->geoip_update_frequency)->format('Y-m-d') 
            : 'As soon as possible';
        
        return [
            'lastUpdate' => $lastUpdate,
            'databaseExists' => $databaseExists,
            'databaseSize' => $databaseSize,
            'needsUpdate' => $needsUpdate,
            'nextUpdate' => $nextUpdate,
            'path' => $geoipPath
        ];
    }
    
    /**
     * Format file size in human-readable format
     *
     * @param int $bytes File size in bytes
     * @param int $precision Precision for rounding
     * @return string Formatted file size
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}