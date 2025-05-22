<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebFtpSetting extends Model
{
    protected $fillable = [
        'enabled',
        'use_external_service',
        'editor_theme',
        'code_beautify',
        'code_suggestion',
        'auto_complete',
        'max_upload_size',
        'allow_zip_operations'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'use_external_service' => 'boolean',
        'code_beautify' => 'boolean',
        'code_suggestion' => 'boolean',
        'auto_complete' => 'boolean',
        'allow_zip_operations' => 'boolean'
    ];

    /**
     * Get the web FTP settings
     */
    public static function getSettings()
    {
        return self::first() ?? self::createDefault();
    }

    /**
     * Create default settings
     */
    public static function createDefault()
    {
        return self::create([
            'enabled' => true,
            'use_external_service' => false,
            'editor_theme' => 'monokai',
            'code_beautify' => true,
            'code_suggestion' => true,
            'auto_complete' => true,
            'max_upload_size' => 10,
            'allow_zip_operations' => true
        ]);
    }

    /**
     * Check if Web FTP is enabled
     */
    public static function isEnabled()
    {
        $settings = self::getSettings();
        return $settings->enabled;
    }

    /**
     * Check if external service should be used
     */
    public static function useExternalService()
    {
        $settings = self::getSettings();
        return $settings->use_external_service;
    }
}