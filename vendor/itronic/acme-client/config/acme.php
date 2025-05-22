<?php

return [
    // Đảm bảo tất cả giá trị đều có default
    'testing' => env('ACME_TESTING', true),
    'cert_dir' => 'app/ssl/certificates',
    'account_dir' => 'app/ssl/accounts',
    'account_token' => env('ACME_ACCOUNT_TOKEN', 'laravel'),
    'contact_email' => env('ACME_CONTACT_EMAIL', 'admin@example.com'),
    'contact_phone' => env('ACME_CONTACT_PHONE', '+1234567890'),
    
    'distinguished_name' => [
        'countryName' => env('ACME_COUNTRY', 'VN'),
        'stateOrProvinceName' => env('ACME_STATE', 'State'),
        'localityName' => env('ACME_CITY', 'City'),
        'organizationName' => env('ACME_ORGANIZATION', 'Organization'),
        'organizationalUnitName' => env('ACME_UNIT', 'IT')
    ],
    
    'challenge_type' => env('ACME_CHALLENGE_TYPE', 'dns-01'),
];