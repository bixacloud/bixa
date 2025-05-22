<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * FontAwesomeHandler - Specialized class for handling Font Awesome packages
 * This class manages the differences between font-awesome and @fortawesome/fontawesome-free
 */
class FontAwesomeHandler
{
    /**
     * Classic Font Awesome package name (v4.x)
     */
    const CLASSIC_PACKAGE = 'font-awesome';
    
    /**
     * Modern Font Awesome package name (v5.x+)
     */
    const MODERN_PACKAGE = '@fortawesome/fontawesome-free';
    
    /**
     * Alternative modern package name (sometimes used by CDNs)
     */
    const ALT_MODERN_PACKAGE = 'fontawesome-free';
    
    /**
     * Modern Font Awesome starting version
     */
    const MODERN_START_VERSION = '5.0.0';
    
    /**
     * Check if a package name is Font Awesome
     *
     * @param string $packageName
     * @return bool
     */
    public static function isFontAwesome($packageName)
    {
        return in_array($packageName, [
            self::CLASSIC_PACKAGE,
            self::MODERN_PACKAGE,
            self::ALT_MODERN_PACKAGE
        ]) || preg_match('/font.*awesome/i', $packageName);
    }
    
    /**
     * Get all Font Awesome packages data
     * Retrieves both classic and modern package data
     *
     * @return array
     */
    public static function getAllPackagesData()
    {
        $cacheKey = 'font_awesome_all_packages';
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $classicData = self::getClassicPackageData();
        $modernData = self::getModernPackageData();
        
        $data = [
            'classic' => $classicData,
            'modern' => $modernData
        ];
        
        Cache::put($cacheKey, $data, 3600); // Cache for 1 hour
        
        return $data;
    }
    
    /**
 * Update getClassicPackageData in FontAwesomeHandler to sort versions properly
 */
public static function getClassicPackageData()
{
    try {
        $url = "https://api.cdnjs.com/libraries/" . self::CLASSIC_PACKAGE;
        $url .= "?fields=name,description,version,author,homepage,license,filename,keywords,assets,tags,versions,github";
        
        $response = Http::get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            // Sort versions properly (newest first)
            if (isset($data['versions']) && is_array($data['versions'])) {
                usort($data['versions'], function($a, $b) {
                    return version_compare($b, $a); // Descending order (newest first)
                });
                
                // Set the latest version to the first one in the sorted array
                if (!empty($data['versions'])) {
                    $data['version'] = $data['versions'][0];
                    
                    // Set tags.latest as well for consistency
                    if (!isset($data['tags'])) {
                        $data['tags'] = [];
                    }
                    $data['tags']['latest'] = $data['version'];
                }
            }
            
            // Add type information
            $data['font_awesome_type'] = 'classic';
            $data['display_name'] = 'Font Awesome 4';
            $data['version_range'] = 'Up to 4.7.0';
            $data['description'] = 'Font Awesome 4 - The iconic font and CSS toolkit';
            
            return $data;
        }
    } catch (\Exception $e) {
        Log::error("Error fetching Font Awesome classic data: " . $e->getMessage());
    }
    
    return null;
}

    
    /**
     * Get modern Font Awesome package data from npm/jsDelivr
     *
     * @return array|null
     */
    public static function getModernPackageData()
    {
        try {
            $url = "https://data.jsdelivr.com/v1/packages/npm/" . self::MODERN_PACKAGE;
            
            $response = Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Transform to match CDNJS format
                $transformedData = [
                    'name' => self::MODERN_PACKAGE,
                    'font_awesome_type' => 'modern',
                    'display_name' => 'Font Awesome 5+',
                    'version_range' => '5.0.0 and above',
                    'description' => 'Font Awesome 5+ - The iconic SVG, font, and CSS toolkit',
                    'versions' => [],
                    'github' => [
                        'user' => 'FortAwesome',
                        'repo' => 'Font-Awesome'
                    ],
                    'homepage' => 'https://fontawesome.com'
                ];
                
                // Extract versions
                if (isset($data['versions']) && is_array($data['versions'])) {
                    foreach ($data['versions'] as $versionObj) {
                        if (isset($versionObj['version'])) {
                            $transformedData['versions'][] = $versionObj['version'];
                        }
                    }
                }
                
                // Set latest version
                if (!empty($transformedData['versions'])) {
                    $transformedData['version'] = $transformedData['versions'][0];
                    $transformedData['tags'] = ['latest' => $transformedData['version']];
                }
                
                return $transformedData;
            }
        } catch (\Exception $e) {
            Log::error("Error fetching Font Awesome modern data: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
 * Update getPackageDetails in FontAwesomeHandler to properly sort versions
 */
public static function getPackageDetails($packageName)
{
    if (!self::isFontAwesome($packageName)) {
        return null;
    }
    
    $allPackages = self::getAllPackagesData();
    
    // Determine which package data to return
    $isModern = ($packageName === self::MODERN_PACKAGE || $packageName === self::ALT_MODERN_PACKAGE);
    
    if ($isModern) {
        $data = $allPackages['modern'];
        
        // Add alternative package info
        $data['alternative_package'] = self::CLASSIC_PACKAGE;
        $data['alternative_description'] = 'For Font Awesome 4 and older, use font-awesome';
        
        // Make sure the versions are properly sorted (newest first)
        if (isset($data['versions']) && is_array($data['versions'])) {
            usort($data['versions'], function($a, $b) {
                return version_compare($b, $a); // Descending order (newest first)
            });
        }
        
        return $data;
    } else {
        $data = $allPackages['classic'];
        
        // Add alternative package info
        $data['alternative_package'] = self::MODERN_PACKAGE;
        $data['alternative_description'] = 'For Font Awesome 5+ and newer, use @fortawesome/fontawesome-free';
        
        // Make sure the versions are properly sorted (newest first)
        if (isset($data['versions']) && is_array($data['versions'])) {
            usort($data['versions'], function($a, $b) {
                return version_compare($b, $a); // Descending order (newest first)
            });
        }
        
        return $data;
    }
}
    
   public static function getVersionFiles($packageName, $version)
{
    if (!self::isFontAwesome($packageName)) {
        return null;
    }
    
    $isModern = ($packageName === self::MODERN_PACKAGE || $packageName === self::ALT_MODERN_PACKAGE);
    
    try {
        if ($isModern) {
            // For modern package, use jsDelivr API
            $url = "https://data.jsdelivr.com/v1/packages/npm/{$packageName}@{$version}";
            $response = Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Extract files from jsDelivr structure
                $files = [];
                if (isset($data['files'])) {
                    self::extractFilesFromJsdelivrData($data['files'], $files);
                }
                
                return [
                    'version' => $version,
                    'files' => $files,
                    'source' => 'jsdelivr' // Ensure source is always set
                ];
            }
        } else {
            // For classic package, use CDNJS API
            $url = "https://api.cdnjs.com/libraries/{$packageName}/{$version}";
            $response = Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Ensure source is set in the response
                if (!isset($data['source'])) {
                    $data['source'] = 'cdnjs';
                }
                
                return $data;
            }
        }
    } catch (\Exception $e) {
        Log::error("Error fetching Font Awesome version files: " . $e->getMessage());
    }
    
    // Return empty structure with source to avoid undefined key error
    return [
        'version' => $version,
        'files' => [],
        'source' => $isModern ? 'jsdelivr' : 'cdnjs'
    ];
}
    
    /**
     * Extract files from jsDelivr response data
     *
     * @param array $filesData
     * @param array &$result
     * @param string $prefix
     * @return void
     */
    private static function extractFilesFromJsdelivrData($filesData, &$result, $prefix = '')
    {
        if (!is_array($filesData)) {
            return;
        }
        
        foreach ($filesData as $item) {
            if (!is_array($item) || !isset($item['type'])) {
                continue;
            }
            
            if ($item['type'] === 'directory' && isset($item['files']) && is_array($item['files'])) {
                self::extractFilesFromJsdelivrData(
                    $item['files'], 
                    $result, 
                    $prefix . $item['name'] . '/'
                );
            } else if ($item['type'] === 'file' && isset($item['name'])) {
                $result[] = $prefix . $item['name'];
            }
        }
    }
    
    /**
     * Generate CDN links for Font Awesome
     *
     * @param string $packageName
     * @param string $version
     * @param array $files
     * @param bool $existsOnCdnjs
     * @return array
     */
    public static function generateCdnLinks($packageName, $version, $files, $existsOnCdnjs = true)
    {
        if (!self::isFontAwesome($packageName) || !is_array($files)) {
            return ['js' => [], 'css' => []];
        }
        
        $jsFiles = array_filter($files, function($file) {
            return is_string($file) && pathinfo($file, PATHINFO_EXTENSION) === 'js';
        });
        
        $cssFiles = array_filter($files, function($file) {
            return is_string($file) && pathinfo($file, PATHINFO_EXTENSION) === 'css';
        });
        
        // Sort to prioritize minified files
        usort($jsFiles, function($a, $b) {
            $aIsMin = strpos($a, '.min.') !== false;
            $bIsMin = strpos($b, '.min.') !== false;
            
            if ($aIsMin && !$bIsMin) return -1;
            if (!$aIsMin && $bIsMin) return 1;
            return 0;
        });
        
        usort($cssFiles, function($a, $b) {
            $aIsMin = strpos($a, '.min.') !== false;
            $bIsMin = strpos($b, '.min.') !== false;
            
            if ($aIsMin && !$bIsMin) return -1;
            if (!$aIsMin && $bIsMin) return 1;
            return 0;
        });
        
        $jsLinks = [];
        $cssLinks = [];
        
        $isModern = ($packageName === self::MODERN_PACKAGE || $packageName === self::ALT_MODERN_PACKAGE);
        
        // For CDNJS, always use classic package name
        $cdnjsPackage = $isModern ? self::CLASSIC_PACKAGE : $packageName;
        
        // For jsDelivr and unpkg, use the original package name
        $jsdelivrPackage = $packageName;
        $unpkgPackage = $packageName;
        
        // Prepare bases URLs for different CDNs
        $jsdelivrBase = "https://cdn.jsdelivr.net/npm/{$jsdelivrPackage}@{$version}/";
        $unpkgBase = "https://unpkg.com/{$unpkgPackage}@{$version}/";
        
        // For modern package, we may need to adjust paths
        $needsDistPrefix = $isModern;
        
        // Limit to first 3 files of each type
        foreach (array_slice($jsFiles, 0, 3) as $file) {
            // For jsDelivr and unpkg, we may need to add 'dist/' prefix
            $jsdelivrPath = $needsDistPrefix && strpos($file, 'dist/') === false ? 'dist/' . $file : $file;
            $unpkgPath = $needsDistPrefix && strpos($file, 'dist/') === false ? 'dist/' . $file : $file;
            
            $link = [
                'file' => $file,
                'jsdelivr' => [
                    'url' => $jsdelivrBase . $jsdelivrPath,
                    'html' => "<script src=\"{$jsdelivrBase}{$jsdelivrPath}\"></script>"
                ]
            ];
            
            // Add CDNJS links only if this version exists on CDNJS
            // For modern Font Awesome, we don't add CDNJS links
            if (!$isModern && $existsOnCdnjs) {
                $link['cdnjs'] = [
                    'url' => "https://cdnjs.cloudflare.com/ajax/libs/{$cdnjsPackage}/{$version}/{$file}",
                    'html' => "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/{$cdnjsPackage}/{$version}/{$file}\"></script>"
                ];
            }
            
            // Add unpkg links
            $link['unpkg'] = [
                'url' => $unpkgBase . $unpkgPath,
                'html' => "<script src=\"{$unpkgBase}{$unpkgPath}\"></script>"
            ];
            
            $jsLinks[] = $link;
        }
        
        foreach (array_slice($cssFiles, 0, 3) as $file) {
            $jsdelivrPath = $needsDistPrefix && strpos($file, 'dist/') === false ? 'dist/' . $file : $file;
            $unpkgPath = $needsDistPrefix && strpos($file, 'dist/') === false ? 'dist/' . $file : $file;
            
            $link = [
                'file' => $file,
                'jsdelivr' => [
                    'url' => $jsdelivrBase . $jsdelivrPath,
                    'html' => "<link rel=\"stylesheet\" href=\"{$jsdelivrBase}{$jsdelivrPath}\">"
                ]
            ];
            
            // Add CDNJS links only if this version exists on CDNJS
            // For modern Font Awesome, we don't add CDNJS links
            if (!$isModern && $existsOnCdnjs) {
                $link['cdnjs'] = [
                    'url' => "https://cdnjs.cloudflare.com/ajax/libs/{$cdnjsPackage}/{$version}/{$file}",
                    'html' => "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/{$cdnjsPackage}/{$version}/{$file}\">"
                ];
            }
            
            // Add unpkg links
            $link['unpkg'] = [
                'url' => $unpkgBase . $unpkgPath,
                'html' => "<link rel=\"stylesheet\" href=\"{$unpkgBase}{$unpkgPath}\">"
            ];
            
            $cssLinks[] = $link;
        }
        
        return [
            'js' => $jsLinks,
            'css' => $cssLinks
        ];
    }
    
    /**
     * Search for Font Awesome packages
     *
     * @param string $query
     * @return array
     */
    public static function searchPackages($query)
    {
        // Check if it's a Font Awesome search
        if (stripos($query, 'font') === false || stripos($query, 'awesome') === false) {
            return [];
        }
        
        $packages = self::getAllPackagesData();
        
        $results = [];
        
        // Add classic package
        if (isset($packages['classic'])) {
            $results[] = [
                'name' => self::CLASSIC_PACKAGE,
                'description' => 'Font Awesome 4 - The iconic font and CSS toolkit (versions up to 4.7.0)',
                'version' => $packages['classic']['version'] ?? '4.7.0',
                'latest' => $packages['classic']['version'] ?? '4.7.0',
                'font_awesome_type' => 'classic'
            ];
        }
        
        // Add modern package
        if (isset($packages['modern'])) {
            $results[] = [
                'name' => self::MODERN_PACKAGE,
                'description' => 'Font Awesome 5+ - The iconic SVG, font, and CSS toolkit (versions 5.x and newer)',
                'version' => $packages['modern']['version'] ?? '6.4.0',
                'latest' => $packages['modern']['version'] ?? '6.4.0',
                'font_awesome_type' => 'modern'
            ];
        }
        
        return $results;
    }
}