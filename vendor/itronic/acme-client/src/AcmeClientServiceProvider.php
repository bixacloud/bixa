<?php

namespace Itronic\AcmeClient;

use Illuminate\Support\ServiceProvider;

class AcmeClientServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Đảm bảo config trả về array
        $this->mergeConfigFrom(
            __DIR__.'/../config/acme.php', 'acme'
        );

        $this->app->singleton('acme.client', function ($app) {
            $client = new \itrAcmeClient();
            
            // Đảm bảo các config đều có giá trị mặc định là array nếu null
            $client->testing = config('acme.testing', false);
            $client->certDir = storage_path(config('acme.cert_dir', 'app/ssl/certificates'));
            $client->certAccountDir = storage_path(config('acme.account_dir', 'app/ssl/accounts'));
            $client->certAccountToken = config('acme.account_token', 'laravel');
            
            $client->certAccountContact = [
                'mailto:' . config('acme.contact_email', 'admin@example.com'),
                'tel:' . config('acme.contact_phone', '+1234567890')
            ];
            
            $client->certDistinguishedName = config('acme.distinguished_name', [
                'countryName' => 'VN',
                'stateOrProvinceName' => 'State',
                'localityName' => 'City',
                'organizationName' => 'Organization',
                'organizationalUnitName' => 'IT'
            ]);
            
            $client->webRootDir = public_path();
            $client->appendDomain = false;
            $client->appendWellKnownPath = true;
            
            $client->logger = $app->make('log');
            
            return $client;
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/acme.php' => config_path('acme.php'),
            ], 'acme-config');
        }
    }
}