<?php

namespace App\Services;

use App\Models\MofhApiSetting;
use App\Models\HostingAccount;
use InfinityFree\MofhClient\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MofhService
{
    protected $client;
    protected $settings;
    protected $logger;
    protected $logPath;

    public function __construct()
    {
        $this->logger = Log::channel('hosting');
        $this->settings = MofhApiSetting::first();
        $this->logPath = storage_path('logs/mofh_callback.json');
        
        if ($this->settings) {
            $this->client = new Client(
                $this->settings->api_username,
                $this->settings->api_password,
                'https://panel.myownfreehost.net/xml-api/'
            );
        }
    }

    /**
     * Create new hosting account
     */
    public function createAccount($label, $domain, $email)
    {
        try {
            $password = Str::random(16);
            $base = preg_replace('/[^a-z0-9]/i', '', Str::lower($label));
            $base = substr($base, 0, 4);
            $random = Str::random(4, 'alnum');
            $username = $base . $random;

            $this->logger->info('Creating account', [
                'username' => $username,
                'domain' => $domain,
                'email' => $email
            ]);

            $response = $this->client->createAccount(
                $username,
                $password,
                $email,     
                $domain,    
                $this->settings->plan
            );

            if ($response->isSuccessful()) {
                $vpUsername = $response->getVpUsername();
                
                $account = HostingAccount::create([
                    'label' => $label,
                    'username' => $vpUsername,
                    'password' => $password,
                    'domain' => $domain,
                    'status' => 'pending',
                    'key' => $username,
                    'user_id' => auth()->id(),
                    'main_domain' => str_replace('cpanel', $username, $this->settings->cpanel_url)
                ]);

                $this->logger->info('Account created', [
                    'id' => $account->id,
                    'username' => $vpUsername
                ]);

                return [
                    'success' => true,
                    'account' => $account,
                    'message' => 'Account created successfully! Please wait while your account is being set up.'
                ];
            }

            $this->logger->error('Creation failed', [
                'message' => $response->getMessage()
            ]);

            return [
                'success' => false, 
                'message' => $response->getMessage()
            ];

        } catch (\Exception $e) {
            $this->logger->error('Creation error', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Error creating account: ' . $e->getMessage()
            ];
        }
    }

 /**
 * Handle MOFH callback
 */
public function handleCallback($data)
{
    try {
        $this->logger->info('Received callback', $data);
        
        // Read existing logs
        $callbackPath = storage_path('logs/mofh_callback.json');
        $callbacks = [];
        
        if (file_exists($callbackPath)) {
            $callbacks = json_decode(file_get_contents($callbackPath), true) ?? [];
        }

        // Add new callback
        $callbacks[] = [
            'username' => $data['username'] ?? null,
            'status' => $data['status'] ?? null,
            'comments' => $data['comments'] ?? null,
            'time' => date('Y-m-d H:i:s')
        ];

        // Save updated logs
        file_put_contents($callbackPath, json_encode($callbacks, JSON_PRETTY_PRINT));

        // Find account
        $account = HostingAccount::where('username', $data['username'])
                               ->orWhere('key', $data['username'])
                               ->first();

        if (!$account) {
            $this->logger->error('Account not found', ['username' => $data['username']]);
            return false;
        }

        // Get last SQL callback if exists
        $sqlServer = null;
        foreach ($callbacks as $callback) {
            if (isset($callback['status']) && substr($callback['status'], 0, 3) === 'sql') {
                $sqlServer = $callback['status'];
            }
        }

        // Update SQL server if found in history
        if ($sqlServer && $account->sql_server !== $sqlServer) {
            $account->sql_server = $sqlServer;
            $this->logger->info('Updated SQL server from history', [
                'username' => $account->username,  
                'sql_server' => $sqlServer
            ]);
        }

        // Handle new status updates
        switch ($data['status']) {
            case 'ACTIVATED':
                $account->status = 'active';
                break;

            case 'REACTIVATE':
                $account->status = 'active';
                break;

            case 'SUSPENDED':
                $comment = $data['comments'] ?? '';
                $account->status = $this->getSuspensionStatus($comment);
                break;

            case 'DELETE':
                $account->status = 'deleted';
                break;

            // Handle new SQL server assignment
            default:
                if (substr($data['status'], 0, 3) === 'sql') {
                    $account->sql_server = $data['status'];
                    $this->logger->info('Updated SQL server from new callback', [
                        'username' => $account->username,
                        'sql_server' => $data['status']  
                    ]);
                }
                break;
        }

        $account->save();

        $this->logger->info('Processed callback successfully', [
            'username' => $account->username,
            'status' => $account->status,
            'sql_server' => $account->sql_server
        ]);

        return true;

    } catch (\Exception $e) {
        $this->logger->error('Callback processing failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

/**
 * Get SQL server from callback history
 */
public function getSqlServerFromHistory($username)
{
    $callbackPath = storage_path('logs/mofh_callback.json');
    if (!file_exists($callbackPath)) {
        return null;
    }

    $callbacks = json_decode(file_get_contents($callbackPath), true) ?? [];
    
    foreach ($callbacks as $callback) {
        if (($callback['username'] === $username) && 
            isset($callback['status']) && 
            substr($callback['status'], 0, 3) === 'sql') {
            return $callback['status'];
        }
    }

    return null;
}

/**
 * Get callback logs for username
 */
public function getCallbackLogs($username = null)
{
    $path = storage_path('logs/mofh_callback.json');
    
    if (!file_exists($path)) {
        return [];
    }

    $logs = json_decode(file_get_contents($path), true) ?? [];

    if ($username) {
        return array_filter($logs, function($log) use ($username) {
            return ($log['username'] ?? '') === $username;
        });
    }

    return $logs;
}

/**
 * Get SQL server from callback logs
 */
public function getSqlServerFromLogs($username)
{
    $logs = $this->getCallbackLogs($username);
    
    foreach ($logs as $log) {
        if (substr($log['status'] ?? '', 0, 3) === 'sql') {
            return $log['status'];
        }
    }

    return null;
}

/**
 * Clean old callback logs
 */
public function cleanCallbackLogs($days = 30)
{
    $path = storage_path('logs/mofh_callback.json');
    
    if (!file_exists($path)) {
        return;
    }

    $logs = json_decode(file_get_contents($path), true) ?? [];
    $cutoff = strtotime("-{$days} days");

    $filtered = array_filter($logs, function($log) use ($cutoff) {
        return strtotime($log['time']) > $cutoff;
    });

    file_put_contents($path, json_encode($filtered, JSON_PRETTY_PRINT));
}






    /**
     * Get domains for account
     */
    public function getDomains($username) 
    {
        try {
            $this->logger->info('Getting domains', ['username' => $username]);

            $response = $this->client->getUserDomains($username);
            
            if ($response->isSuccessful()) {
                $domains = $response->getDomains();
                
                $this->logger->info('Domains retrieved', [
                    'username' => $username,
                    'count' => count($domains)
                ]);

                return $domains;
            }

            return [];

        } catch (\Exception $e) {
            $this->logger->error('Get domains error', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get account status
     */
    public function getAccountStatus($username)
    {
        try {
            $response = $this->client->getUserDomains($username);
            
            if ($response->isSuccessful()) {
                return 'active';
            }

            $message = Str::lower($response->getMessage());

            if (Str::contains($message, ['res_close', 'terminated', 'deleted'])) {
                return 'deactivated';
            }

            if (Str::contains($message, ['suspended', 'disable'])) {
                return 'suspended';
            }

            return 'pending';

        } catch (\Exception $e) {
            $this->logger->error('Status check error', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Update account status
     */
    public function updateAccountStatus(HostingAccount $account)
    {
        try {
            if ($account->status !== 'pending') {
                return true;
            }

            $status = $this->getAccountStatus($account->username);
            
            if ($status && $status !== $account->status) {
                $account->status = $status;
                $account->save();

                $this->logger->info('Status updated', [
                    'username' => $account->username,
                    'status' => $status
                ]);
            }

            return true;

        } catch (\Exception $e) {
            $this->logger->error('Status update error', [
                'username' => $account->username,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Parse suspension reason
     */
    protected function getSuspensionStatus($reason)
    {
        $parts = explode(':', $reason);
        
        if (trim($parts[0]) === 'RES_CLOSE') {
            return 'deactivated';
        }

        return 'suspended';
    }

    /**
     * Check domain availability
     */
    public function checkDomain($domain)
    {
        try {
            $this->logger->info('Checking domain', ['domain' => $domain]);
            
            $response = $this->client->availability($domain);
            
            if ($response->getData() === "1") {
                return true;
            }

            if ($response->getData() === "0") {
                return false;
            }

            if ($response->getMessage()) {
                return $response->getMessage();
            }

            return false;

        } catch (\Exception $e) {
            $this->logger->error('Domain check error', [
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            return "Error checking domain: " . $e->getMessage();
        }
    }

    /**
     * Test API connection
     */
    public function test()
{
    try {
        if (!$this->settings) {
            return [
                'success' => false,
                'message' => 'MOFH API settings not configured'
            ];
        }

        $response = $this->client->availability('example.com');
        
        return [
            'success' => $response->isSuccessful(),
            'message' => $response->isSuccessful() ? 
                'Successfully connected to MOFH API' : 
                'Failed to connect to MOFH API: ' . ($response->getMessage() ?? 'Unknown error')
        ];

    } catch (\Exception $e) {
        $this->logger->error('API test error', [
            'error' => $e->getMessage()
        ]);
        return [
            'success' => false,
            'message' => 'Error connecting to MOFH API: ' . $e->getMessage()
        ];
    }
}


    /**
     * Get callback for username
     */
    public function getCallbackForUsername($username)
    {
        $logs = $this->getCallbackLogs();
        return array_filter($logs, function($log) use ($username) {
            return $log['username'] === $username;
        });
    }
	
/**
 * Suspend/deactivate account
 */ 
public function suspendAccount($username, $reason)
{
    try {
        $this->logger->info('Suspending account', [
            'username' => $username,
            'reason' => $reason
        ]);

        // Get account to get original key (username without _)
        $account = HostingAccount::where('username', $username)->first();
        if (!$account) {
            $this->logger->error('Account not found for suspension', [
                'username' => $username
            ]);
            return [
                'success' => false,
                'message' => 'Account not found'
            ];
        }

        // Use account key (original username) for suspension
        $response = $this->client->suspend($account->key, $reason);
        
        if ($response->isSuccessful()) {
            $this->logger->info('Account suspension initiated', [
                'username' => $username,
                'key' => $account->key
            ]);

            return [
                'success' => true,
                'message' => 'Account suspension initiated successfully'
            ];
        }

        $this->logger->error('Failed to suspend account', [
            'username' => $username,
            'key' => $account->key,
            'message' => $response->getMessage()
        ]);

        return [
            'success' => false,
            'message' => $response->getMessage() ?? 'Failed to suspend account'
        ];

    } catch (\Exception $e) {
        $this->logger->error('Error suspending account', [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return [
            'success' => false,
            'message' => 'Error suspending account: ' . $e->getMessage()
        ];
    }
}
/**
 * Reactivate suspended account
 */
public function reactivateAccount($username)
{
    try {
        $this->logger->info('Reactivating account', [
            'username' => $username
        ]);

        // Get account to get original key
        $account = HostingAccount::where('username', $username)->first();
        if (!$account) {
            $this->logger->error('Account not found for reactivation', [
                'username' => $username
            ]);
            return false;
        }

        // Use account key for reactivation
        $response = $this->client->unsuspend($account->key);
        
        if ($response->isSuccessful()) {
            // Update account status
            $account->status = 'reactivating';
            $account->save();

            $this->logger->info('Account reactivation initiated', [
                'username' => $username,
                'key' => $account->key
            ]);

            return true;
        }

        $this->logger->error('Failed to reactivate account', [
            'username' => $username,
            'key' => $account->key,
            'message' => $response->getMessage()
        ]);

        return false;

    } catch (\Exception $e) {
        $this->logger->error('Error reactivating account', [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

/**
 * Change account password
 */
public function changePassword($username, $oldPassword, $newPassword)
{
    try {
        $this->logger->info('Changing password', [
            'username' => $username
        ]);

        // Get account to get original key (username without _)
        $account = HostingAccount::where('username', $username)->first();
        if (!$account) {
            $this->logger->error('Account not found for password change', [
                'username' => $username
            ]);
            return [
                'success' => false,
                'message' => 'Account not found'
            ];
        }

        // Use the password method from the MOFH Client
        $response = $this->client->password($account->key, $newPassword);
        
        if ($response->isSuccessful()) {
            $this->logger->info('Password changed successfully', [
                'username' => $username,
                'key' => $account->key
            ]);

            return [
                'success' => true,
                'message' => 'Password changed successfully'
            ];
        }

        // If not successful, get the error message
        $this->logger->error('Failed to change password', [
            'username' => $username,
            'key' => $account->key,
            'message' => $response->getMessage(),
            'status' => $response->getStatus()
        ]);

        return [
            'success' => false,
            'message' => $response->getMessage() ?? 'Failed to change password'
        ];

    } catch (\Exception $e) {
        $this->logger->error('Error changing password', [
            'username' => $username,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return [
            'success' => false,
            'message' => 'Error changing password: ' . $e->getMessage()
        ];
    }
}

}