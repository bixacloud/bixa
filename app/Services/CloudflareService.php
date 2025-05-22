<?php

namespace App\Services;

use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\Zones;
use App\Models\CloudflareConfig;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    protected $adapter;
    protected $dns;
    protected $zones;
    protected $config;

    public function __construct()
    {
        $this->config = CloudflareConfig::where('is_active', true)->first();
        
        if ($this->config) {
            $key = new APIKey($this->config->email, $this->config->api_key);
            $this->adapter = new Guzzle($key);
            $this->dns = new DNS($this->adapter);
            $this->zones = new Zones($this->adapter);
        }
    }
	
	 protected function getZoneId()
    {
        $zones = $this->zones->listZones($this->config->proxy_domain)->result;
        if (empty($zones)) {
            throw new \Exception("Zone not found for domain: {$this->config->proxy_domain}");
        }
        return $zones[0]->id;
    }
	
  protected function getRecordName($domain)
    {
        // Extract subdomain if exists
        $parts = explode('.', $domain);
        if (count($parts) > 2) {
            $subdomain = array_shift($parts); // Get first part as subdomain
            return "_acme-challenge.{$subdomain}.{$this->config->proxy_domain}";
        }
        return "_acme-challenge.{$this->config->proxy_domain}";
    }

public function createProxyRecordName($domain)
    {
        // Lấy subdomain từ domain gốc
        $parts = explode('.', $domain);
        $prefix = '_acme-challenge';
        
        if (count($parts) > 2) {
            // Nếu là subdomain, thêm subdomain vào proxy record
            $subdomain = array_shift($parts); // Lấy phần subdomain
            $prefix = $prefix . '.' . $subdomain;
        }
        
        return $prefix . '.' . $this->config->proxy_domain;
    }
	
    public function getZoneIdByDomain($domain)
    {
        try {
            if (!$this->config) {
                throw new \Exception('Cloudflare configuration not found');
            }

            $zones = $this->zones->listZones($domain);
            
            if (empty($zones->result)) {
                throw new \Exception("Zone not found for domain: {$domain}");
            }

            return $zones->result[0]->id;
        } catch (\Exception $e) {
            Log::error('Failed to get zone ID', [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

     public function createTxtRecord($name, $content, $domain)
    {
        if (!$this->config) {
            throw new \Exception('Cloudflare configuration not found');
        }

        try {
            // Generate proper record name based on domain type
            $proxyRecordName = $this->getRecordName($domain);
            
            Log::info('Creating Cloudflare TXT record', [
                'domain' => $domain,
                'record_name' => $proxyRecordName,
                'content' => $content
            ]);

            $result = $this->dns->addRecord(
                $this->getZoneId(),
                'TXT',
                $proxyRecordName, // Use the proper record name here
                $content,
                120,
                false
            );

            if (!$result) {
                throw new \Exception('Failed to create TXT record');
            }

            // Get the created record details
            $records = $this->dns->listRecords($this->getZoneId(), 'TXT', $proxyRecordName)->result;
            foreach ($records as $record) {
                if ($record->content === $content) {
                    return [
                        'id' => $record->id,
                        'name' => $record->name,
                        'content' => $record->content,
                        'zone_id' => $this->getZoneId()
                    ];
                }
            }

            throw new \Exception('Record created but not found');
        } catch (\Exception $e) {
            Log::error('Cloudflare Error', [
                'operation' => 'createTxtRecord',
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function deleteTxtRecord($recordId, $zoneId)
    {
        if (!$this->config) {
            throw new \Exception('Cloudflare configuration not found');
        }

        try {
            return $this->dns->deleteRecord($zoneId, $recordId);
        } catch (\Exception $e) {
            Log::error('Failed to delete TXT record', [
                'recordId' => $recordId,
                'zoneId' => $zoneId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getProxyDomain()
    {
        return $this->config ? $this->config->proxy_domain : null;
    }

    public function isConfigured()
    {
        return $this->config !== null;
    }

    public function testConnection()
    {
        try {
            if (!$this->config) {
                throw new \Exception('Cloudflare configuration not found');
            }

            // Try to list zones as a connection test
            $this->zones->listZones();
            return true;
        } catch (\Exception $e) {
            Log::error('Cloudflare connection test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
	
	
}