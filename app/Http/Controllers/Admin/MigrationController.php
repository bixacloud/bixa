<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\MigrationPasswordResetMail;
use App\Models\User;
use App\Models\HostingAccount;
use App\Models\Certificate;
use Coderflex\LaravelTicket\Models\Category;
use App\Models\SmtpSetting;
use App\Models\Setting;
use App\Models\OAuthSetting;
use App\Models\SiteProSetting;
use App\Models\MofhApiSetting;
use App\Models\AllowedDomain;
use Coderflex\LaravelTicket\Models\Ticket;
use Coderflex\LaravelTicket\Models\Message;
use Illuminate\Support\Facades\Log;

class MigrationController extends Controller
{
    protected $oldConnection = 'old_database';
    protected $totalSteps = 6;
    
    /**
     * Show the migration dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $migrationStatus = [
            'users' => session('migration_status.users', 'pending'),
            'accounts' => session('migration_status.accounts', 'pending'),
            'tickets' => session('migration_status.tickets', 'pending'),
            'ssl' => session('migration_status.ssl', 'pending'),
            'settings' => session('migration_status.settings', 'pending'),
        ];
        
        $migrationStats = [
            'users' => session('migration_stats.users', ['total' => 0, 'migrated' => 0, 'failed' => 0]),
            'accounts' => session('migration_stats.accounts', ['total' => 0, 'migrated' => 0, 'failed' => 0]),
            'tickets' => session('migration_stats.tickets', ['total' => 0, 'migrated' => 0, 'failed' => 0]),
            'ssl' => session('migration_stats.ssl', ['total' => 0, 'migrated' => 0, 'failed' => 0]),
            'settings' => session('migration_stats.settings', ['total' => 0, 'migrated' => 0, 'failed' => 0]),
        ];
        
        $connectionStatus = session('connection_status', 'not_connected');
        $passwordsMigrated = session('passwords_migrated', false);
        
        return view('admin.migration.index', compact(
            'migrationStatus', 
            'migrationStats', 
            'connectionStatus',
            'passwordsMigrated'
        ));
    }
    
    /**
     * Connect to the old database
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host' => 'required|string',
            'port' => 'required|numeric',
            'database' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.migration.index')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Configure the old database connection
        config([
            'database.connections.old_database' => [
                'driver' => 'mysql',
                'host' => $request->host,
                'port' => $request->port,
                'database' => $request->database,
                'username' => $request->username,
                'password' => $request->password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => false,
                'engine' => null,
            ]
        ]);
        
        // Test the connection
        try {
            DB::connection('old_database')->getPdo();
            
            // Save connection config to session
            session([
                'old_db_config' => [
                    'host' => $request->host,
                    'port' => $request->port,
                    'database' => $request->database,
                    'username' => $request->username,
                    'password' => $request->password,
                ],
                'connection_status' => 'connected'
            ]);
            
            return redirect()->route('admin.migration.index')
                ->with('success', 'Successfully connected to the old database.');
        } catch (\Exception $e) {
            return redirect()->route('admin.migration.index')
                ->with('error', 'Failed to connect to the database: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Disconnect from the old database
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disconnect()
    {
        session()->forget(['old_db_config', 'connection_status', 'migration_status', 'migration_stats', 'passwords_migrated']);
        
        return redirect()->route('admin.migration.index')
            ->with('success', 'Successfully disconnected from the old database.');
    }
    
    /**
     * Get the old database connection
     * 
     * @return \Illuminate\Database\Connection
     * @throws \Exception
     */
    protected function getOldConnection()
    {
        if (session()->has('old_db_config')) {
            $config = session('old_db_config');
            
            config([
                'database.connections.old_database' => [
                    'driver' => 'mysql',
                    'host' => $config['host'],
                    'port' => $config['port'],
                    'database' => $config['database'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => false,
                    'engine' => null,
                ]
            ]);
            
            return DB::connection('old_database');
        }
        
        throw new \Exception('Not connected to the old database.');
    }
    
    /**
     * Start the migration process
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function start()
    {
        if (session('connection_status') !== 'connected') {
            return redirect()->route('admin.migration.index')
                ->with('error', 'You must connect to the old database first.');
        }
        
        // Reset migration status
        session([
            'migration_status' => [
                'users' => 'pending',
                'accounts' => 'pending',
                'tickets' => 'pending',
                'ssl' => 'pending',
                'settings' => 'pending',
            ],
            'migration_stats' => [
                'users' => ['total' => 0, 'migrated' => 0, 'failed' => 0],
                'accounts' => ['total' => 0, 'migrated' => 0, 'failed' => 0],
                'tickets' => ['total' => 0, 'migrated' => 0, 'failed' => 0],
                'ssl' => ['total' => 0, 'migrated' => 0, 'failed' => 0],
                'settings' => ['total' => 0, 'migrated' => 0, 'failed' => 0],
            ],
            'passwords_migrated' => false
        ]);
        
        return redirect()->route('admin.migration.migrate', ['step' => 1]);
    }
    
    /**
     * Perform the migration step by step
     * 
     * @param  int  $step
     * @return \Illuminate\Http\RedirectResponse
     */
    public function migrate($step)
    {
        if (session('connection_status') !== 'connected') {
            return redirect()->route('admin.migration.index')
                ->with('error', 'You must connect to the old database first.');
        }
        
        // Update total steps count
        $this->totalSteps = 5;
        
        try {
            switch ($step) {
                case 1:
                    $this->migrateUsers();
                    return redirect()->route('admin.migration.migrate', ['step' => 2]);
                case 2:
                    $this->migrateAccounts();
                    return redirect()->route('admin.migration.migrate', ['step' => 3]);
                case 3:
                    $this->migrateTickets();
                    return redirect()->route('admin.migration.migrate', ['step' => 4]);
                case 4:
                    $this->migrateSSL();
                    return redirect()->route('admin.migration.migrate', ['step' => 5]);
                case 5:
                    $this->migrateSettings();
                    return redirect()->route('admin.migration.index')
                        ->with('success', 'Migration completed successfully.');
                default:
                    return redirect()->route('admin.migration.index')
                        ->with('error', 'Invalid migration step.');
            }
        } catch (\Exception $e) {
            Log::error('Migration failed: ' . $e->getMessage(), [
                'step' => $step,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.migration.index')
                ->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Migrate users from the old database with random passwords
     * 
     * @return void
     */
    protected function migrateUsers()
    {
        session(['migration_status.users' => 'in_progress']);
        
        $db = $this->getOldConnection();
        
        // Get all users from the old database
        $oldUsers = $db->table('is_user')->get();
        $oldAdmins = $db->table('is_admin')->get();
        
        $totalUsers = $oldUsers->count() + $oldAdmins->count();
        $migratedCount = 0;
        $failedCount = 0;
        
        // Store password data for each user
        $passwordData = [];
        
        // Migrate regular users
        foreach ($oldUsers as $oldUser) {
            try {
                // Check if user already exists
                if (User::where('email', $oldUser->user_email)->exists()) {
                    $failedCount++;
                    continue;
                }
                
                // Generate a random password
                $randomPassword = Str::random(10);
                
                // Create the user with the new random password
                User::create([
                    'name' => $oldUser->user_name ?? 'User',
                    'email' => $oldUser->user_email,
                    'password' => Hash::make($randomPassword),
                    'role' => 'user',
                    'email_verified_at' => $oldUser->user_status === 'active' ? now() : null,
                    'created_at' => Carbon::createFromTimestamp($oldUser->user_date),
                    'updated_at' => Carbon::createFromTimestamp($oldUser->user_date),
                ]);
                
                // Store the password data
                $passwordData[$oldUser->user_email] = [
                    'name' => $oldUser->user_name ?? 'User',
                    'password' => $randomPassword,
                    'role' => 'user',
                ];
                
                $migratedCount++;
            } catch (\Exception $e) {
                Log::error('Error migrating user: ' . $e->getMessage(), [
                    'user_email' => $oldUser->user_email,
                    'trace' => $e->getTraceAsString()
                ]);
                $failedCount++;
            }
        }
        
        // Migrate admin users
        foreach ($oldAdmins as $oldAdmin) {
            try {
                // Check if admin already exists
                if (User::where('email', $oldAdmin->admin_email)->exists()) {
                    $failedCount++;
                    continue;
                }
                
                // Generate a random password (slightly longer and more complex for admins)
                $randomPassword = Str::random(12);
                
                User::create([
                    'name' => $oldAdmin->admin_name ?? 'Admin',
                    'email' => $oldAdmin->admin_email,
                    'password' => Hash::make($randomPassword),
                    'role' => 'admin',
                    'email_verified_at' => $oldAdmin->admin_status === 'active' ? now() : null,
                    'created_at' => Carbon::createFromTimestamp($oldAdmin->admin_date),
                    'updated_at' => Carbon::createFromTimestamp($oldAdmin->admin_date),
                ]);
                
                // Store the password data
                $passwordData[$oldAdmin->admin_email] = [
                    'name' => $oldAdmin->admin_name ?? 'Admin',
                    'password' => $randomPassword,
                    'role' => 'admin',
                ];
                
                $migratedCount++;
            } catch (\Exception $e) {
                Log::error('Error migrating admin: ' . $e->getMessage(), [
                    'admin_email' => $oldAdmin->admin_email,
                    'trace' => $e->getTraceAsString()
                ]);
                $failedCount++;
            }
        }
        
        // Save password data in the settings
        Setting::set('migration_passwords', [
            'migrated_at' => now()->toDateTimeString(),
            'total_users' => $totalUsers,
            'password_data' => $passwordData,
        ]);
        
        session([
            'migration_status.users' => 'completed',
            'migration_stats.users' => [
                'total' => $totalUsers,
                'migrated' => $migratedCount,
                'failed' => $failedCount,
            ]
        ]);
    }
    
    /**
     * Migrate hosting accounts from the old database
     * 
     * @return void
     */
    protected function migrateAccounts()
    {
        session(['migration_status.accounts' => 'in_progress']);
        
        $db = $this->getOldConnection();
        
        // Get all accounts from the old database
        $oldAccounts = $db->table('is_account')->get();
        
        $totalAccounts = $oldAccounts->count();
        $migratedCount = 0;
        $failedCount = 0;
        
        foreach ($oldAccounts as $oldAccount) {
            try {
                // Find the user - FIXED: First get the user email from old database
                $userEmail = $db->table('is_user')
                    ->where('user_key', $oldAccount->account_for)
                    ->value('user_email');
                
                if (!$userEmail) {
                    $failedCount++;
                    continue;
                }
                
                $user = User::where('email', $userEmail)->first();
                
                if (!$user) {
                    $failedCount++;
                    continue;
                }
                
                // Check if account already exists
                if (HostingAccount::where('username', $oldAccount->account_username)->exists()) {
                    $failedCount++;
                    continue;
                }
                
                HostingAccount::create([
                    'label' => $oldAccount->account_label,
                    'username' => $oldAccount->account_username,
                    'password' => $oldAccount->account_password,
                    'domain' => $oldAccount->account_domain,
                    'status' => $oldAccount->account_status,
                    'key' => $oldAccount->account_key,
                    'main_domain' => $oldAccount->account_main,
                    'sql_server' => $oldAccount->account_sql,
                    'user_id' => $user->id,
                    'created_at' => Carbon::createFromTimestamp($oldAccount->account_time),
                    'updated_at' => Carbon::createFromTimestamp($oldAccount->account_time),
                    'cpanel_verified' => $oldAccount->account_status === 'active' ? true : false,
                    'cpanel_verified_at' => $oldAccount->account_status === 'active' ? Carbon::createFromTimestamp($oldAccount->account_time) : null,
                ]);
                
                $migratedCount++;
            } catch (\Exception $e) {
                Log::error('Error migrating account: ' . $e->getMessage(), [
                    'account_username' => $oldAccount->account_username,
                    'trace' => $e->getTraceAsString()
                ]);
                $failedCount++;
            }
        }
        
        session([
            'migration_status.accounts' => 'completed',
            'migration_stats.accounts' => [
                'total' => $totalAccounts,
                'migrated' => $migratedCount,
                'failed' => $failedCount,
            ]
        ]);
    }
    
    /**
     * Migrate tickets and responses from the old database
     * Using Coderflex LaravelTicket package
     * 
     * @return void
     */
    protected function migrateTickets()
    {
        session(['migration_status.tickets' => 'in_progress']);
        
        $db = $this->getOldConnection();
        
        // Get all tickets from the old database
        $oldTickets = $db->table('is_ticket')->get();
        
        $totalTickets = $oldTickets->count();
        $migratedCount = 0;
        $failedCount = 0;
        
        // Get default category
        $defaultCategory = Category::where('name', 'General')->first();
        if (!$defaultCategory) {
            $defaultCategory = Category::create([
                'name' => 'General',
                'description' => 'General support category',
                'slug' => 'general',
                'is_visible' => true,
            ]);
        }
        
        foreach ($oldTickets as $oldTicket) {
            try {
                // Find the user - FIXED: First get the user email from old database
                $userEmail = $db->table('is_user')
                    ->where('user_key', $oldTicket->ticket_for)
                    ->value('user_email');
                
                if (!$userEmail) {
                    $failedCount++;
                    continue;
                }
                
                $user = User::where('email', $userEmail)->first();
                
                if (!$user) {
                    $failedCount++;
                    continue;
                }
                
                // Map old status to new status
                $statusMap = [
                    'open' => 'open',
                    'closed' => 'closed',
                    'customer' => 'customer-reply',
                    'answered' => 'answered',
                    'admin' => 'open',
                ];
                
                // Create the ticket
                $ticket = Ticket::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'title' => $oldTicket->ticket_subject,
                    'category_id' => $defaultCategory->id,
                    'priority' => 'medium',
                    'status' => $statusMap[$oldTicket->ticket_status] ?? 'open',
                    'is_resolved' => in_array($oldTicket->ticket_status, ['closed']) ? true : false,
                    'is_locked' => false,
                    'created_at' => Carbon::createFromTimestamp($oldTicket->ticket_time),
                    'updated_at' => Carbon::createFromTimestamp($oldTicket->ticket_time),
                ]);
                
                // Create first message
                Message::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'message' => $oldTicket->ticket_content,
                    'created_at' => Carbon::createFromTimestamp($oldTicket->ticket_time),
                    'updated_at' => Carbon::createFromTimestamp($oldTicket->ticket_time),
                ]);
                
                // Get ticket replies
                $replies = $db->table('is_reply')
                           ->where('reply_for', $oldTicket->ticket_key)
                           ->get();
                
                foreach ($replies as $reply) {
                    // Find the replier (user or admin)
                    $replier = null;
                    
                    if ($reply->reply_by === $oldTicket->ticket_for) {
                        $replier = $user;
                    } else {
                        // Try to find admin - FIXED: First get admin email from old database
                        $adminEmail = $db->table('is_admin')
                            ->where('admin_key', $reply->reply_by)
                            ->value('admin_email');
                        
                        if ($adminEmail) {
                            $replier = User::where('email', $adminEmail)->first();
                        }
                        
                        // If not found, use first admin
                        if (!$replier) {
                            $replier = User::where('role', 'admin')->first();
                        }
                    }
                    
                    if (!$replier) {
                        continue;
                    }
                    
                    // Create message
                    Message::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $replier->id,
                        'message' => $reply->reply_content,
                        'created_at' => Carbon::createFromTimestamp($reply->reply_time),
                        'updated_at' => Carbon::createFromTimestamp($reply->reply_time),
                    ]);
                }
                
                $migratedCount++;
            } catch (\Exception $e) {
                Log::error('Error migrating ticket: ' . $e->getMessage(), [
                    'ticket_id' => $oldTicket->ticket_id,
                    'trace' => $e->getTraceAsString()
                ]);
                $failedCount++;
            }
        }
        
        session([
            'migration_status.tickets' => 'completed',
            'migration_stats.tickets' => [
                'total' => $totalTickets,
                'migrated' => $migratedCount,
                'failed' => $failedCount,
            ]
        ]);
    }
    
    /**
     * Migrate SSL certificates from the old database
     * 
     * @return void
     */
    protected function migrateSSL()
    {
        session(['migration_status.ssl' => 'in_progress']);
        
        $db = $this->getOldConnection();
        
        // Get all SSL certificates from the old database
        $oldSSLs = $db->table('is_ssl')->get();
        
        $totalSSLs = $oldSSLs->count();
        $migratedCount = 0;
        $failedCount = 0;
        
        foreach ($oldSSLs as $oldSSL) {
            try {
                // Find the user - FIXED: First get the user email from old database
                $userEmail = $db->table('is_user')
                    ->where('user_key', $oldSSL->ssl_for)
                    ->value('user_email');
                
                if (!$userEmail) {
                    $failedCount++;
                    continue;
                }
                
                $user = User::where('email', $userEmail)->first();
                
                if (!$user) {
                    $failedCount++;
                    continue;
                }
                
                // Check if certificate already exists
                if (Certificate::where('domain', $oldSSL->ssl_domain)->exists()) {
                    $failedCount++;
                    continue;
                }
                
                // Create the certificate with new model fields
                Certificate::create([
                    'user_id' => $user->id,
                    'domain' => $oldSSL->ssl_domain,
                    'type' => $oldSSL->ssl_type,
                    'status' => $oldSSL->ssl_status,
                    'order_id' => $oldSSL->ssl_pid,
                    'dns_validation' => [
                        'dns_id' => $oldSSL->ssl_dnsid,
                        'dns_host' => $oldSSL->ssl_dns,
                    ],
                    'private_key' => $oldSSL->ssl_private,
                    'csr' => null, // Not available in old data
                    'certificate' => null, // Not available in old data
                    'ca_certificate' => null, // Not available in old data
                    'valid_until' => null, // Not available in old data
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $migratedCount++;
            } catch (\Exception $e) {
                Log::error('Error migrating SSL certificate: ' . $e->getMessage(), [
                    'domain' => $oldSSL->ssl_domain,
                    'trace' => $e->getTraceAsString()
                ]);
                $failedCount++;
            }
        }
        
        session([
            'migration_status.ssl' => 'completed',
            'migration_stats.ssl' => [
                'total' => $totalSSLs,
                'migrated' => $migratedCount,
                'failed' => $failedCount,
            ]
        ]);
    }
    
    /**
     * Migrate SMTP settings from the old database
     * Note: This method is disabled as SMTP needs to be configured in the new system
     * before migration.
     * 
     * @return void
     */
    /*
    protected function migrateSMTP()
    {
        session(['migration_status.smtp' => 'in_progress']);
        
        $db = $this->getOldConnection();
        
        // Get SMTP settings from the old database
        $oldSMTP = $db->table('is_smtp')->first();
        
        $totalSettings = 1;
        $migratedCount = 0;
        $failedCount = 0;
        
        try {
            if ($oldSMTP) {
                // Check if SMTP settings already exist
                if (SmtpSetting::count() == 0) {
                    SmtpSetting::create([
                        'type' => 'SMTP',
                        'hostname' => $oldSMTP->smtp_hostname,
                        'username' => $oldSMTP->smtp_username,
                        'password' => $oldSMTP->smtp_password,
                        'from_email' => $oldSMTP->smtp_from,
                        'from_name' => $oldSMTP->smtp_name,
                        'port' => (int) $oldSMTP->smtp_port,
                        'encryption' => strtolower($oldSMTP->smtp_encryption),
                        'status' => $oldSMTP->smtp_status === 'active' ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $migratedCount++;
                } else {
                    $failedCount++;
                }
            } else {
                $failedCount++;
            }
        } catch (\Exception $e) {
            Log::error('Error migrating SMTP settings: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $failedCount++;
        }
        
        session([
            'migration_status.smtp' => 'completed',
            'migration_stats.smtp' => [
                'total' => $totalSettings,
                'migrated' => $migratedCount,
                'failed' => $failedCount,
            ]
        ]);
    }
    */
    
    /**
     * Migrate general settings from the old database
     * 
     * @return void
     */
    protected function migrateSettings()
    {
        session(['migration_status.settings' => 'in_progress']);
        
        $db = $this->getOldConnection();
        
        // Get settings from the old database
        $oldBase = $db->table('is_base')->first();
        $oldBuilder = $db->table('is_builder')->first();
        $oldMofh = $db->table('is_mofh')->first();
        $oldDomains = $db->table('is_domain')->get();
        $oldOAuth = $db->table('is_oauth')->get();
        
        $totalSettings = 5; // Base, Builder, MOFH, Domains, OAuth
        $migratedCount = 0;
        $failedCount = 0;
        
        try {
            // Migrate base settings
            if ($oldBase) {
                $baseSettings = [
                    'site_name' => $oldBase->base_name,
                    'site_email' => $oldBase->base_email,
                    'site_url' => url('/'),
                    'forum_url' => $oldBase->base_fourm,
                    'site_theme' => $oldBase->base_template,
                    'site_status' => $oldBase->base_status === 'active' ? true : false,
                    'records_per_page' => $oldBase->base_rpp,
                ];
                
                Setting::set('general', $baseSettings);
                $migratedCount++;
            } else {
                $failedCount++;
            }
            
            // Migrate site builder settings
            if ($oldBuilder) {
                // Create SiteProSetting
                SiteProSetting::updateOrCreate(
                    ['id' => 1],
                    [
                        'hostname' => $oldBuilder->builder_hostname,
                        'username' => $oldBuilder->builder_username,
                        'password' => $oldBuilder->builder_password,
                        'status' => $oldBuilder->builder_status === 'active' ? 1 : 0,
                    ]
                );
                
                $migratedCount++;
            } else {
                $failedCount++;
            }
            
            // Migrate MOFH settings
            if ($oldMofh) {
                // Update MOFH API settings
                MofhApiSetting::updateOrCreate(
                    ['id' => 1],
                    [
                        'api_username' => $oldMofh->mofh_username,
                        'api_password' => $oldMofh->mofh_password,
                        'plan' => $oldMofh->mofh_package,
                        'cpanel_url' => $oldMofh->mofh_cpanel,
                    ]
                );
                
                $migratedCount++;
            } else {
                $failedCount++;
            }
            
            // Migrate domains
            if ($oldDomains->isNotEmpty()) {
                foreach ($oldDomains as $domain) {
                    if (!empty($domain->domain_name)) {
                        AllowedDomain::updateOrCreate(
                            ['domain_name' => $domain->domain_name],
                            []
                        );
                    }
                }
                $migratedCount++;
            } else {
                $failedCount++;
            }
            
            // Migrate OAuth settings
            if ($oldOAuth->isNotEmpty()) {
                $oauthTypes = ['github', 'google', 'facebook'];
                
                foreach ($oauthTypes as $type) {
                    $oauth = $oldOAuth->where('oauth_id', $type)->first();
                    
                    if ($oauth) {
                        OAuthSetting::updateOrCreate(
                            ['provider' => $type],
                            [
                                'client_id' => $oauth->oauth_client,
                                'client_secret' => $oauth->oauth_secret,
                                'is_enabled' => $oauth->oauth_status === 'active' ? 1 : 0,
                            ]
                        );
                    }
                }
                
                $migratedCount++;
            } else {
                $failedCount++;
            }
            
            // Mark migration as complete
            session(['passwords_migrated' => true]);
        } catch (\Exception $e) {
            Log::error('Error migrating settings: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $failedCount++;
        }
        
        session([
            'migration_status.settings' => 'completed',
            'migration_stats.settings' => [
                'total' => $totalSettings,
                'migrated' => $migratedCount,
                'failed' => $failedCount,
            ]
        ]);
    }
    
  /**
 * Send notification emails with new passwords to users
 * 
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function sendPasswordEmails(Request $request)
{
    // Get the password data
    $passwordData = Setting::get('migration_passwords');
    
    if (!$passwordData || empty($passwordData['password_data'])) {
        return redirect()->route('admin.migration.index')
            ->with('error', 'No password data found. Please complete the migration first.');
    }
    
    $successCount = 0;
    $failCount = 0;
    
    // Send email to each user
    foreach ($passwordData['password_data'] as $email => $userData) {
        try {
            // Sử dụng mail class mới với template migration-password-reset
            Mail::to($email)->send(new MigrationPasswordResetMail(
                $userData['name'],
                $email,
                $userData['password'],
                $userData['role']
            ));
            
            // Log thành công
            \Log::info("Đã gửi email mật khẩu tới: $email");
            
            $successCount++;
        } catch (\Exception $e) {
            Log::error('Failed to send password email: ' . $e->getMessage(), [
                'email' => $email,
                'trace' => $e->getTraceAsString()
            ]);
            $failCount++;
        }
    }
    
    // Update the setting to track email sending
    $passwordData['emails_sent'] = [
        'sent_at' => now()->toDateTimeString(),
        'success_count' => $successCount,
        'fail_count' => $failCount,
    ];
    
    Setting::set('migration_passwords', $passwordData);
    
    return redirect()->route('admin.migration.index')
        ->with('success', "Password emails sent: $successCount successful, $failCount failed.");
}
    
    /**
     * Export password list as CSV with partially masked passwords
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPasswords()
    {
        // Get the password data
        $passwordData = Setting::get('migration_passwords');
        
        if (!$passwordData || empty($passwordData['password_data'])) {
            return redirect()->route('admin.migration.index')
                ->with('error', 'No password data found. Please complete the migration first.');
        }
        
        // Create CSV file content
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="migration_passwords.csv"',
        ];
        
        $callback = function() use ($passwordData) {
            $file = fopen('php://output', 'w');
            
            // Add CSV header
            fputcsv($file, ['Name', 'Email', 'Role', 'Password']);
            
            // Add user data with masked passwords
            foreach ($passwordData['password_data'] as $email => $userData) {
                $password = $userData['password'];
                $maskedPassword = $this->maskPassword($password);
                
                fputcsv($file, [
                    $userData['name'],
                    $email,
                    $userData['role'],
                    $maskedPassword
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export complete password list as CSV (admin only)
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportFullPasswords()
    {
        // Check if the current user is an admin
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('admin.migration.index')
                ->with('error', 'Only administrators can export complete password lists.');
        }
        
        // Get the password data
        $passwordData = Setting::get('migration_passwords');
        
        if (!$passwordData || empty($passwordData['password_data'])) {
            return redirect()->route('admin.migration.index')
                ->with('error', 'No password data found. Please complete the migration first.');
        }
        
        // Create CSV file content
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="migration_full_passwords.csv"',
        ];
        
        $callback = function() use ($passwordData) {
            $file = fopen('php://output', 'w');
            
            // Add CSV header
            fputcsv($file, ['Name', 'Email', 'Role', 'Password']);
            
            // Add user data with full passwords
            foreach ($passwordData['password_data'] as $email => $userData) {
                fputcsv($file, [
                    $userData['name'],
                    $email,
                    $userData['role'],
                    $userData['password']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Mask a password for security
     * 
     * @param  string  $password
     * @return string
     */
    protected function maskPassword($password)
    {
        $length = strlen($password);
        
        if ($length <= 4) {
            // For very short passwords, show only first character
            return substr($password, 0, 1) . '***';
        } else {
            // Show first 2 and last 2 characters, mask the rest
            $firstChars = substr($password, 0, 2);
            $lastChars = substr($password, -2);
            $maskedLength = $length - 4;
            $masked = str_repeat('*', $maskedLength);
            
            return $firstChars . $masked . $lastChars;
        }
    }
}