<?php

namespace App\Services;

use App\Models\AuthLogSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class GeoIPUpdater
{
    /**
     * URLs for GeoIP database downloads
     */
    const GEOIP_CITY_URL = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=%s&suffix=tar.gz';
    const GEOIP_COUNTRY_URL = 'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key=%s&suffix=tar.gz';
    
    /**
     * Check if the GeoIP database needs updating
     * 
     * @return bool
     */
    public function needsUpdate()
    {
        $settings = AuthLogSettings::getSettings();
        $lastUpdate = Storage::exists('geoip_last_update.txt') 
            ? Carbon::createFromTimestamp(Storage::get('geoip_last_update.txt')) 
            : null;
            
        // If never updated or last update is older than the frequency setting
        return !$lastUpdate || 
               $lastUpdate->addDays($settings->geoip_update_frequency)->isPast();
    }
    
    /**
     * Update the GeoIP database
     * 
     * @param string|null $licenseKey MaxMind license key
     * @return array Status of the update operation
     */
    public function update($licenseKey = null)
    {
        if (!$licenseKey) {
            $licenseKey = config('geoip.license_key');
        }
        
        if (empty($licenseKey)) {
            return [
                'success' => false,
                'message' => 'MaxMind license key is not configured. Please set it in your environment file or settings.',
            ];
        }
        
        try {
            // Create temp directory if not exists
            $tempDir = storage_path('app/geoip_temp');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }
            
            // Download and extract City database
            $result = $this->downloadAndExtract(
                sprintf(self::GEOIP_CITY_URL, $licenseKey),
                $tempDir,
                config('geoip.database_path')
            );
            
            if (!$result['success']) {
                return $result;
            }
            
            // Record the update time
            Storage::put('geoip_last_update.txt', time());
            
            return [
                'success' => true,
                'message' => 'GeoIP database updated successfully.',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to update GeoIP database: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to update GeoIP database: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Download and extract GeoIP database
     * 
     * @param string $url URL to download from
     * @param string $tempDir Temporary directory for extraction
     * @param string $finalPath Final path for the database
     * @return array Status of the operation
     */
    protected function downloadAndExtract($url, $tempDir, $finalPath)
    {
        // Download file
        $tarGzPath = $tempDir . '/geoip.tar.gz';
        $result = $this->downloadFile($url, $tarGzPath);
        
        if (!$result['success']) {
            return $result;
        }
        
        // Extract tar.gz file
        $phar = new \PharData($tarGzPath);
        $phar->extractTo($tempDir);
        
        // Find the mmdb file in the extracted directory
        $mmdbFiles = glob($tempDir . '/**/GeoLite2-*.mmdb');
        
        if (empty($mmdbFiles)) {
            return [
                'success' => false,
                'message' => 'Could not find GeoIP database file in downloaded archive.',
            ];
        }
        
        // Make sure the destination directory exists
        $destDir = dirname($finalPath);
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }
        
        // Move the database file to the final location
        File::copy($mmdbFiles[0], $finalPath);
        
        // Clean up
        File::deleteDirectory($tempDir);
        
        return [
            'success' => true,
            'message' => 'Database downloaded and extracted successfully.',
        ];
    }
    
    /**
     * Download a file using PHP
     * 
     * @param string $url URL to download
     * @param string $path Path to save the file
     * @return array Status of the download
     */
    protected function downloadFile($url, $path)
    {
        // Initialize cURL
        $ch = curl_init($url);
        $fp = fopen($path, 'w');
        
        if (!$fp) {
            return [
                'success' => false,
                'message' => 'Could not open file for writing: ' . $path,
            ];
        }
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minute timeout
        
        // Execute cURL
        $success = curl_exec($ch);
        $error = curl_error($ch);
        
        // Close resources
        curl_close($ch);
        fclose($fp);
        
        if (!$success) {
            return [
                'success' => false,
                'message' => 'Failed to download file: ' . $error,
            ];
        }
        
        return [
            'success' => true,
            'message' => 'File downloaded successfully.',
        ];
    }
    
    /**
     * Alternative method for systems without cURL
     * 
     * @param string $url URL to download
     * @param string $path Path to save the file
     * @return array Status of the download
     */
    protected function downloadFileWithFopen($url, $path)
    {
        $options = [
            'http' => [
                'method' => 'GET',
                'timeout' => 300, // 5 minute timeout
            ]
        ];
        
        $context = stream_context_create($options);
        $content = @file_get_contents($url, false, $context);
        
        if ($content === false) {
            return [
                'success' => false,
                'message' => 'Failed to download file with file_get_contents().',
            ];
        }
        
        $result = file_put_contents($path, $content);
        
        if ($result === false) {
            return [
                'success' => false,
                'message' => 'Failed to write downloaded content to file.',
            ];
        }
        
        return [
            'success' => true,
            'message' => 'File downloaded successfully with file_get_contents().',
        ];
    }
}