<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
'acme' => [
    'email' => env('ACME_EMAIL'),
	'mode' => env('ACME_MODE', 'live'),
],
'buypass' => [
        'email' => env('BUYPASS_EMAIL'),
        'mode' => env('BUYPASS_MODE', 'live'),
    ],
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
'google' => [
        'client_id' => function() {
            $setting = OAuthSetting::where('provider', 'google')
                                 ->where('is_enabled', true)
                                 ->first();
            return $setting ? $setting->client_id : null;
        },
        'client_secret' => function() {
            $setting = OAuthSetting::where('provider', 'google')
                                 ->where('is_enabled', true)
                                 ->first();
            return $setting ? $setting->client_secret : null;
        },
        'redirect' => function() {
            return url('/auth/google/callback');
        },
    ],

    'facebook' => [
        'client_id' => function() {
            $setting = OAuthSetting::where('provider', 'facebook')
                                 ->where('is_enabled', true)
                                 ->first();
            return $setting ? $setting->client_id : null;
        },
        'client_secret' => function() {
            $setting = OAuthSetting::where('provider', 'facebook')
                                 ->where('is_enabled', true)
                                 ->first();
            return $setting ? $setting->client_secret : null;
        },
        'redirect' => function() {
            return url('/auth/facebook/callback');
        },
    ],
];
