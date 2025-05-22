<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class IconCaptchaSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Get a captcha setting by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("icon_captcha_setting.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a captcha setting value
     */
    public static function set(string $key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        // Clear cache for this setting
        Cache::forget("icon_captcha_setting.{$key}");
        
        return $setting;
    }

    /**
     * Check if a boolean setting is enabled
     */
    public static function isEnabled(string $key, bool $default = false)
    {
        $value = self::get($key, $default ? '1' : '0');
        return $value === '1' || $value === 'true' || $value === true;
    }
}