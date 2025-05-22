<?php

namespace App\Helpers;

class LanguageHelper
{
    /**
     * Get language name from ISO code
     * 
     * @param string $code Language ISO code
     * @return string Language name
     */
    public static function getLanguageName($code)
    {
        $languages = [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'ru' => 'Russian',
            'pt' => 'Portuguese',
            'nl' => 'Dutch',
            'ja' => 'Japanese',
            'zh' => 'Chinese',
            'ar' => 'Arabic',
            'hi' => 'Hindi',
            'ko' => 'Korean',
            'tr' => 'Turkish',
            'vi' => 'Vietnamese',
            'th' => 'Thai',
        ];
        
        return $languages[$code] ?? ucfirst($code);
    }

    /**
     * Get available languages from the lang directory
     * 
     * @return array Available languages [code => name]
     */
    public static function getAvailableLanguages()
    {
        $availableLanguages = [];
        
        // Check if using Laravel filesystem or direct directory access
        if (function_exists('lang_path')) {
            // Laravel 9+ - use lang_path()
            $langPath = lang_path();
        } else {
            // Earlier Laravel versions - use base_path
            $langPath = base_path('resources/lang');
        }
        
        // Check if directory exists before scanning
        if (is_dir($langPath)) {
            $directories = array_filter(glob($langPath . '/*'), 'is_dir');
            
            foreach ($directories as $dir) {
                $langCode = basename($dir);
                $availableLanguages[$langCode] = self::getLanguageName($langCode);
            }
            
            // Also check for JSON files (Laravel's JSON-based translations)
            $jsonFiles = glob($langPath . '/*.json');
            foreach ($jsonFiles as $file) {
                $langCode = pathinfo($file, PATHINFO_FILENAME);
                if (!isset($availableLanguages[$langCode])) {
                    $availableLanguages[$langCode] = self::getLanguageName($langCode);
                }
            }
        }
        
        // If no languages found, use default ones
        if (empty($availableLanguages)) {
            $availableLanguages = [
                'en' => 'English',
                'es' => 'Spanish',
                'de' => 'German',
                'it' => 'Italian',
                'ru' => 'Russian',
            ];
        }
        
        // Sort languages alphabetically by name
        asort($availableLanguages);
        
        return $availableLanguages;
    }
    
    /**
     * Get country code for flag icon based on language code
     * 
     * @param string $langCode Language ISO code
     * @return string Country ISO code for the flag
     */
    public static function getFlagCode($langCode)
    {
        // Map language codes to country codes for flags
        $flagMap = [
            'en' => 'us', // English -> USA flag
            'es' => 'es', // Spanish -> Spain flag
            'fr' => 'fr', // French -> France flag
            'de' => 'de', // German -> Germany flag
            'it' => 'it', // Italian -> Italy flag
            'ru' => 'ru', // Russian -> Russia flag
            'pt' => 'pt', // Portuguese -> Portugal flag
            'pt-br' => 'br', // Brazilian Portuguese -> Brazil flag
            'nl' => 'nl', // Dutch -> Netherlands flag
            'ja' => 'jp', // Japanese -> Japan flag
            'zh' => 'cn', // Chinese -> China flag
            'zh-tw' => 'tw', // Traditional Chinese -> Taiwan flag
            'ar' => 'sa', // Arabic -> Saudi Arabia flag
            'hi' => 'in', // Hindi -> India flag
            'ko' => 'kr', // Korean -> South Korea flag
            'tr' => 'tr', // Turkish -> Turkey flag
            'vi' => 'vn', // Vietnamese -> Vietnam flag
            'th' => 'th', // Thai -> Thailand flag
            'pl' => 'pl', // Polish -> Poland flag
            'cs' => 'cz', // Czech -> Czech Republic flag
            'sv' => 'se', // Swedish -> Sweden flag
            'da' => 'dk', // Danish -> Denmark flag
            'fi' => 'fi', // Finnish -> Finland flag
            'no' => 'no', // Norwegian -> Norway flag
            'uk' => 'ua', // Ukrainian -> Ukraine flag
            'hu' => 'hu', // Hungarian -> Hungary flag
            'ro' => 'ro', // Romanian -> Romania flag
            'bg' => 'bg', // Bulgarian -> Bulgaria flag
            'el' => 'gr', // Greek -> Greece flag
            'id' => 'id', // Indonesian -> Indonesia flag
            'ms' => 'my', // Malay -> Malaysia flag
            'he' => 'il', // Hebrew -> Israel flag
            'fa' => 'ir', // Persian/Farsi -> Iran flag
            'bn' => 'bd', // Bengali -> Bangladesh flag
        ];
        
        return $flagMap[$langCode] ?? strtolower($langCode);
    }
}