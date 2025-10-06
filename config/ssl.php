<?php

return [
    'providers' => [
        'letsencrypt' => [
            'url' => env('LETSENCRYPT_URL', 'https://acme-v02.api.letsencrypt.org/directory'),
            'staging_url' => env('LETSENCRYPT_STAGING_URL', 'https://acme-staging-v02.api.letsencrypt.org/directory'),
        ],
        
        'zerossl' => [
            'url' => env('ZEROSSL_URL', 'https://acme.zerossl.com/v2/DV90'),
        ],
        
        'googletrust' => [
            'url' => env('GOOGLETRUST_URL', '#'),
        ],
    ],

    'storage' => [
        'keys_path' => env('SSL_KEYS_PATH', 'ssl_certs/account_keys'),
        'certs_path' => env('SSL_CERTS_PATH', 'ssl_certs/certificates'),
    ],

    'renewal' => [
        'days_before' => env('SSL_RENEWAL_DAYS', 30), 
        'notification_email' => env('SSL_NOTIFICATION_EMAIL'), 
    ],
];
