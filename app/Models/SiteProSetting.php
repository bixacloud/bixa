<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SiteProSetting extends Model
{
    protected $fillable = [
        'hostname',
        'username', 
        'password',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function isActive()
    {
        return $this->status;
    }

    public function getStatusText()
    {
        return $this->status ? 'active' : 'inactive';
    }

    public function loadBuilderUrl($username, $password, $domain, $dir = '/htdocs/')
    {
        try {
            $data = [
                "type" => "external",
                "username" => $username,
                "password" => $password,
                "domain" => $domain,
                "baseDomain" => $domain,
                "apiUrl" => "ftpupload.net", 
                "uploadDir" => $dir
            ];

            $ch = curl_init($this->hostname . '/api/requestLogin');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);

            $result = curl_exec($ch);
            $json = json_decode($result, true);

            if (isset($json['error'])) {
                Log::error('Site.pro API error', [
                    'error' => $json['error']
                ]);
                return [
                    'success' => false,
                    'message' => $json['error']['message']
                ];
            }

            if (isset($json['url'])) {
                return [
                    'success' => true,
                    'url' => $json['url']
                ];
            }

            Log::error('Invalid response from Site.pro API', [
                'response' => $result
            ]);
            return [
                'success' => false,
                'message' => 'Invalid response from builder API'
            ];

        } catch (\Exception $e) {
            Log::error('Error loading builder URL', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Failed to connect to builder API'
            ];
        }
    }
}