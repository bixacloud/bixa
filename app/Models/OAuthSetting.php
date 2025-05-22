<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OAuthSetting extends Model
{
    protected $table = 'oauth_settings';  // Chỉ định tên bảng

    protected $fillable = [
        'provider',
        'client_id',
        'client_secret',
        'is_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean'
    ];

    public static function getForProvider($provider)
    {
        return static::where('provider', $provider)
                    ->where('is_enabled', true)
                    ->first();
    }
}