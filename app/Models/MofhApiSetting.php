<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MofhApiSetting extends Model
{
    protected $fillable = [
        'api_username',
        'api_password', 
        'plan',
        'cpanel_url'
    ];

    public static function getSettings()
    {
        return self::first();
    }

    public static function getCallbackUrl()
    {
        return config('app.url') . '/callback/mofh';
    }

    public static function getServerIp()
    {
        return gethostbyname($_SERVER['HTTP_HOST']);
    }
}