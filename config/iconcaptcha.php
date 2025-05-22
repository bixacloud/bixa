<?php

return [
    'iconPath' => base_path('vendor/fabianwennink/iconcaptcha/assets/icons/'),
    'ipAddress' => static fn() => request()->ip(),
    'themes' => [],
    'token' => \IconCaptcha\Token\IconCaptchaToken::class,
    'hooks' => [],
    
    'storage' => [
        'driver' => 'session',
        'connection' => [],
        'datetimeFormat' => 'Y-m-d H:i:s',
    ],
    
    'challenge' => [
        'availableIcons' => 180,
        'iconAmount' => [
            'min' => 5,
            'max' => 8,
        ],
        'rotate' => true,
        'flip' => [
            'horizontally' => true,
            'vertically' => true,
        ],
        'border' => true,
        'generator' => \IconCaptcha\Challenge\Generators\GD::class,
    ],
    
    'validation' => [
        'inactivityExpiration' => 120,
        'completionExpiration' => 300,
        'attempts' => [
            'enabled' => true,
            'amount' => 3,
            'timeout' => 60,
            'valid' => 30,
            'storage' => [
                'driver' => null,
                'options' => [
                    'table' => 'iconcaptcha_attempts',
                    'purging' => true,
                ],
            ],
        ],
    ],
    
    'session' => [
        'driver' => null,
        'options' => [
            'table' => 'iconcaptcha_challenges',
            'purging' => true,
            'identifierTries' => 100,
        ],
    ],
    
    'cors' => [
        'enabled' => false,
        'origins' => [],
        'credentials' => true,
        'cache' => 86400,
    ],
];
