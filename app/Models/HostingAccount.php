<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class HostingAccount extends Model
{
    protected $fillable = [
        'label',
        'username',  
        'password',
        'domain',
        'status', // pending, active, suspended, deactivated, reactivating, deactivating
        'key',    // Account key from MOFH
        'main_domain', // Main cPanel domain
        'sql_server', // SQL server name (e.g. sql123)
        'user_id', // Owner
        'cpanel_verified',
        'cpanel_verified_at',
        'admin_deactivated',
        'admin_deactivation_reason',
        'admin_deactivated_at'
    ];

    protected $casts = [
        'cpanel_verified' => 'boolean',
        'cpanel_verified_at' => 'datetime',
        'admin_deactivated' => 'boolean',
        'admin_deactivated_at' => 'datetime'
    ];

    /**
     * Check if cPanel is verified
     */
    public function isCpanelVerified()
    {
        return $this->cpanel_verified;
    }

    /**
     * Check if account was deactivated by admin
     */
    public function isAdminDeactivated()
    {
        return $this->admin_deactivated;
    }

    /**
     * Get account owner
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get active accounts count for a user
     */
    public static function getActiveCount($userId) 
    {
        return self::where('user_id', $userId)
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get MySQL host
     */
    public function getMysqlHostAttribute()
    {
        Log::channel('hosting')->info('Getting MySQL host', [
            'account_id' => $this->id,
            'status' => $this->status,
            'sql_server' => $this->sql_server
        ]);

        if ($this->status !== 'active') {
            return 'Loading...';
        }

        if (empty($this->sql_server)) {
            return 'Loading...'; 
        }

        // Get domain extension for MySQL host
        $domain = AllowedDomain::first();
        if (!$domain) {
            Log::channel('hosting')->error('No domain extension found for MySQL host');
            return 'Loading...';
        }

        // Combine SQL server with domain extension 
        $host = $this->sql_server . $domain->domain_name;

        Log::channel('hosting')->info('MySQL host generated', [
            'sql_server' => $this->sql_server,
            'domain' => $domain->domain_name,
            'full_host' => $host
        ]);

        return $host;
    }

    /**
     * Get FTP hostname
     */
    public function getFtpHostAttribute()
    {
        return 'ftpupload.net';
    }

    /**
     * Get File Manager URL with proper credentials
     */
    public function getFileManagerUrl($domain = null)
    {
        if ($this->status !== 'active') {
            return '#';
        }

        $domain = $domain ?? $this->domain;
        
        $params = [
            'host' => $this->ftp_host,
            'port' => 21,
            'user' => $this->username,
            'password' => $this->password,
            'dir' => $domain === $this->domain ? "/htdocs/" : "/{$domain}/htdocs/"
        ];

        return "https://filemanager.ai/new/?" . http_build_query($params);
    }

    /**
     * Get cPanel URL
     */
    public function getCpanelUrl()
    {
        return $this->status === 'active' ? $this->main_domain : '#';
    }

    /**
     * Get Softaculous installer URL
     */
    public function getSoftaculousUrl()
    {
        return $this->status === 'active' ? $this->main_domain . '/softaculous' : '#';
    }

    /**
     * Get domains associated with this account
     */
    public function getDomains($mofhService)
    {
        if ($this->status !== 'active') {
            return [];
        }

        try {
            $domains = $mofhService->getDomains($this->username);
            if (!$domains) {
                return [];
            }

            return collect($domains)->map(function($domain) {
                return [
                    'domain' => $domain,
                    'file_manager' => $this->getFileManagerUrl($domain)
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting domains', [
                'username' => $this->username,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}