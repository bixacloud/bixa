<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\AuthLogSettings;
use Torann\GeoIP\Facades\GeoIP;

class DetectLanguage
{
    /**
     * Mapping of country codes to language codes
     */
    protected $languageMap = [
        'US' => 'en',
        'GB' => 'en',
        'ES' => 'es',
        'MX' => 'es',
        'DE' => 'de',
        'AT' => 'de',
        'IT' => 'it',
        'FR' => 'fr',
        'BE' => 'fr',
        'PT' => 'pt',
        'BR' => 'pt',
        'NL' => 'nl',
        'JP' => 'ja',
        'CN' => 'zh',
        'TW' => 'zh',
        'RU' => 'ru',
        'IN' => 'hi',
        'KR' => 'ko',
        'VN' => 'vi',
        'TH' => 'th',
        'PH' => 'ph',
        'SA' => 'ar',
        'AE' => 'ar',
        // Add more country to language mappings as needed
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get settings
        $settings = app('App\Models\AuthLogSettings')->getSettings();
        
        // If language detection is disabled, skip detection but still apply session language
        if (!$settings->language_detection) {
            if (Session::has('lang')) {
                $locale = Session::get('lang');
                // Verify if the language exists in our application
                if ($this->languageExists($locale)) {
                    App::setLocale($locale);
                } else {
                    // If not, default to English
                    App::setLocale('en');
                    Session::put('lang', 'en');
                }
            }
            
            // Reset auto-detection flag if it exists
            if (Session::has('lang_auto_detected')) {
                Session::forget('lang_auto_detected');
            }
            
            return $next($request);
        }
        
        // If user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // If user already has a locale in DB
            if ($user->locale) {
                $locale = $user->locale;
                
                // Verify if the language exists in our application
                if (!$this->languageExists($locale)) {
                    $locale = 'en'; // Default to English if language doesn't exist
                    $user->setLocale($locale, false);
                }
                
                Session::put('lang', $locale);
                Session::put('lang_auto_detected', $user->locale_auto_detected ?? false);
                App::setLocale($locale);
            } 
            // If no locale in DB but exists in session
            elseif (Session::has('lang')) {
                $locale = Session::get('lang');
                
                // Verify if the language exists in our application
                if (!$this->languageExists($locale)) {
                    $locale = 'en'; // Default to English if language doesn't exist
                    Session::put('lang', $locale);
                }
                
                $user->setLocale($locale, false); // Not auto-detected
                App::setLocale($locale);
                
                // Keep auto-detection flag from session
                if (Session::has('lang_auto_detected')) {
                    $user->locale_auto_detected = Session::get('lang_auto_detected');
                    $user->save();
                }
            } 
            // If neither exists, detect from IP and save to DB
            else {
                $locale = $this->detectLanguage($request);
                
                // Verify if the detected language exists in our application
                if (!$this->languageExists($locale)) {
                    $locale = 'en'; // Default to English if language doesn't exist
                }
                
                Session::put('lang', $locale);
                Session::put('lang_auto_detected', true);
                App::setLocale($locale);
                $user->setLocale($locale, true); // Auto-detected
            }
        } 
        // If user is not authenticated
        else {
            // Check if language is already saved in session
            if (!Session::has('lang')) {
                // Detect language based on IP
                $locale = $this->detectLanguage($request);
                
                // Verify if the detected language exists in our application
                if (!$this->languageExists($locale)) {
                    $locale = 'en'; // Default to English if language doesn't exist
                }
                
                Session::put('lang', $locale);
                Session::put('lang_auto_detected', true);
                App::setLocale($locale);
            } else {
                $locale = Session::get('lang');
                
                // Verify if the language exists in our application
                if (!$this->languageExists($locale)) {
                    $locale = 'en'; // Default to English if language doesn't exist
                    Session::put('lang', $locale);
                }
                
                App::setLocale($locale);
                // Keep auto-detection flag if it exists
            }
        }

        return $next($request);
    }

    /**
     * Detect language based on IP address using GeoIP2 Lite
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function detectLanguage(Request $request)
    {
        try {
            // Get IP address
            $ip = $request->ip();
            
            // Skip local IPs
            if ($this->isLocalIp($ip)) {
                return config('app.locale');
            }
            
            // Use the Torann\GeoIP package
            $geoip = GeoIP::getLocation($ip);
            
            // If we have a country code
            if (isset($geoip['iso_code'])) {
                $countryCode = $geoip['iso_code'];
                
                // Map country to language
                if (isset($this->languageMap[$countryCode])) {
                    return $this->languageMap[$countryCode];
                }
            }
        } catch (\Exception $e) {
            // Log error but continue with default language
            \Log::error('Error detecting language from IP: ' . $e->getMessage());
        }
        
        // Return default language
        return config('app.locale');
    }
    
    /**
     * Check if a language exists in the application
     *
     * @param string $locale
     * @return bool
     */
    protected function languageExists(string $locale): bool
    {
        // Determine language path (Laravel 9+ or earlier)
        $langPath = function_exists('lang_path') ? lang_path() : base_path('resources/lang');
        
        // Check if language directory exists
        $directoryExists = File::isDirectory($langPath . '/' . $locale);
        
        // Check if language JSON file exists
        $jsonExists = File::exists($langPath . '/' . $locale . '.json');
        
        return $directoryExists || $jsonExists;
    }
    
    /**
     * Check if IP is a local/private IP
     *
     * @param string $ip
     * @return bool
     */
    protected function isLocalIp($ip)
    {
        return in_array($ip, ['127.0.0.1', '::1']) || 
               preg_match('/^192\.168\./', $ip) || 
               preg_match('/^10\./', $ip) || 
               preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip);
    }
}