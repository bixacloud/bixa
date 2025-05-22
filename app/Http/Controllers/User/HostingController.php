<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MofhApiSetting;
use App\Models\HostingAccount;
use App\Models\SiteProSetting;
use App\Models\IconCaptchaSetting;
use App\Services\MofhService;
use App\Services\NotificationService;
use App\Libraries\VistapanelApi;
use App\Models\AllowedDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Hosting\AccountCreatedMail;
use App\Mail\Hosting\AccountDeactivatedMail;
use App\Mail\Hosting\AccountReactivatedMail;
use App\Mail\Hosting\PasswordChangedMail;

class HostingController extends Controller
{
    protected $mofhService;
    protected $notificationService;

    public function __construct(MofhService $mofhService, NotificationService $notificationService)
    {
        $this->mofhService = $mofhService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display list of user's hosting accounts
     */
    public function index()
    {
        $accounts = HostingAccount::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hosting.index', compact('accounts'));
    }

    /**
     * Show create hosting form
     */
    public function create()
    {
        if ($this->getActiveAccountsCount() >= 3) {
            return redirect()->route('hosting.index')
                ->with('error', 'You have reached the maximum number of allowed hosting accounts (3).');
        }

        $allowedDomains = AllowedDomain::pluck('domain_name');
        return view('hosting.create', compact('allowedDomains'));
    }

    /**
     * Check domain availability
     */
    public function checkDomain(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
            'ext' => 'required|string|max:255'
        ]);

        $domain = $request->domain . $request->ext;
        
        $result = $this->mofhService->checkDomain($domain);

        if ($result === true) {
            Session::put('domain', $domain);
            return back()->with('success', 'Domain is available and has been selected.');
        }
        
        if (is_string($result)) {
            return back()->withErrors(['domain' => $result])->withInput();
        }

        return back()->withErrors(['domain' => 'Domain is not available.'])->withInput();
    }

    /**
     * Store new hosting account
     */
    public function store(Request $request)
    {
        try {
            if ($this->getActiveAccountsCount() >= 3) {
                return redirect()->route('hosting.index')
                    ->with('error', 'You have reached the maximum number of allowed hosting accounts (3).');
            }

            if (!Session::has('domain')) {
                return redirect()->route('hosting.create')
                    ->withErrors(['error' => 'Please check domain availability first.']);
            }

            // Verify captcha if enabled
            if (IconCaptchaSetting::isEnabled('enabled', true)) {
                // Check for hp flag (client-side validation marker)
                if ($request->input('ic-hp') !== '1') {
                    return back()->withErrors(['error' => 'Please complete the CAPTCHA verification first.'])
                                ->withInput($request->except(['password']));
                }
                
                \Log::info('Captcha verification passed for account creation', [
                    'user_id' => auth()->id(),
                    'domain' => Session::get('domain'),
                    'captcha_data' => [
                        'hp' => $request->input('ic-hp'),
                        'wid' => $request->input('ic-wid')
                    ]
                ]);
            }

            $request->validate([
                'label' => 'required|string|max:255'
            ]);

            DB::beginTransaction();

            $result = $this->mofhService->createAccount(
                $request->label,
                Session::get('domain'),
                auth()->user()->email
            );

            if ($result['success']) {
                try {
                    Mail::to(auth()->user()->email)
                        ->send(new AccountCreatedMail($result['account']));

                    \Log::info('Hosting account creation email sent', [
                        'account_id' => $result['account']->id,
                        'user_email' => auth()->user()->email
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send hosting creation email', [
                        'error' => $e->getMessage()
                    ]);
                }

                // Add hosting notification
                $this->notificationService->createHostingNotification(
                    auth()->user(), 
                    'created', 
                    [
                        'domain' => $result['account']->domain,
                        'username' => $result['account']->username
                    ]
                );

                Session::forget('domain');
                DB::commit();
                
                return redirect()->route('hosting.view', $result['account']->username)
                    ->with('success', $result['message']);
            }

            DB::rollback();
            return back()
                ->withErrors(['error' => $result['message']])
                ->withInput();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating hosting account: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error creating account. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show settings page
     */
    public function settings($username)
    {
        try {
            $account = HostingAccount::where('user_id', auth()->id())
                ->where('username', $username)
                ->first();

            if (!$account) {
                \Log::error('Account not found in settings page', [
                    'username' => $username,
                    'user_id' => auth()->id()
                ]);
                return redirect()->route('hosting.index')
                    ->withErrors(['error' => 'Hosting account not found.']);
            }

            if ($account->status !== 'active' && $account->status !== 'deactivated') {
                return redirect()->route('hosting.view', $username)
                    ->with('error', 'You can only modify active or deactivated accounts.');
            }

            return view('hosting.settings', compact('account'));
        } catch (\Exception $e) {
            \Log::error('Error loading settings page', [
                'username' => $username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('hosting.index')
                ->withErrors(['error' => 'Error loading account settings.']);
        }
    }


  
    /**
     * Update account settings
     */  
    public function updateSettings(Request $request, $identifier)
    {
        \Log::info('Update settings request received', [
            'identifier' => $identifier,
            'user_id' => auth()->id(),
            'request_method' => $request->method(),
            'request_path' => $request->path(),
            'request_data' => $request->except(['password', 'old_password']),
        ]);

        try {
            // Simple captcha validation similar to admin implementation
            if (IconCaptchaSetting::isEnabled('enabled', true)) {
                // Check for basic HP field first (client-side validation marker)
                if ($request->input('ic-hp') !== '1') {
                    return back()->withErrors(['error' => 'Please complete the CAPTCHA verification first.'])
                                ->withInput($request->except(['password', 'old_password']));
                }
                
                // Log custom validation
                \Log::info('Using simplified captcha validation', [
                    'hp' => $request->input('ic-hp'),
                    'widget_id' => $request->input('ic-wid')
                ]);
            }
            
            // Find account by either username or ID
            $query = HostingAccount::where('user_id', auth()->id())
                ->where(function($q) use ($identifier) {
                    $q->where('username', $identifier)
                    ->orWhere('id', $identifier);
                });
                
            $account = $query->first();
            
            if (!$account) {
                return back()->withErrors(['error' => 'Hosting account not found.']);
            }

            DB::beginTransaction();

            // Determine which action to take based on the 'action' field
            $action = $request->input('action');
            
            \Log::info('Processing action', [
                'action' => $action,
                'account_id' => $account->id
            ]);
            
            if (!$action) {
                DB::rollback();
                return back()->withErrors(['error' => 'No action specified. Please try again.'])
                            ->withInput($request->except(['password', 'old_password']));
            }

            // Handle label update
            if ($action === 'update_label') {
                $request->validate([
                    'label' => 'required|string|max:255'
                ]);

                try {
                    $account->label = $request->label;
                    $account->save();
                    
                    // Add label changed notification
                    $this->notificationService->createHostingNotification(
                        auth()->user(), 
                        'label_changed', 
                        [
                            'domain' => $account->domain,
                            'username' => $account->username,
                            'label' => $request->label
                        ]
                    );
                    
                    DB::commit();
                    \Log::info('Account label updated', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'new_label' => $request->label
                    ]);
                    
                    return back()->with('success', 'Account label updated successfully.');
                        
                } catch (\Exception $e) {
                    DB::rollback();
                    \Log::error('Error updating label', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['error' => 'Failed to update label. Please try again.']);
                }
            }
            
            // Handle password update
            if ($action === 'update_password') {
                $request->validate([
                    'password' => 'required|string|min:8',
                    'old_password' => 'required|string'
                ]);

                try {
                    // Implement your password change logic here
                    $result = $this->mofhService->changePassword(
                        $account->username,
                        $request->old_password,
                        $request->password
                    );
                    
                    if (!isset($result['success']) || !$result['success']) {
                        DB::rollback();
                        return back()->withErrors(['error' => $result['message'] ?? 'Failed to change password. Please try again.']);
                    }
                    
                    // Update the stored password
                    $account->password = $request->password;
                    $account->save();
                    
                    // Add password changed notification
                    $this->notificationService->createHostingNotification(
                        auth()->user(), 
                        'password_changed', 
                        [
                            'domain' => $account->domain,
                            'username' => $account->username
                        ]
                    );
                    
                    // Send email notification
                    try {
                        Mail::to(auth()->user()->email)
                                ->send(new PasswordChangedMail($account, $request->password));
                            
                        \Log::info('Password change email sent', [
                            'account_id' => $account->id
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send password change email', [
                            'account_id' => $account->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    DB::commit();
                    return back()->with('success', 'Password changed successfully.');
                    
                } catch (\Exception $e) {
                    DB::rollback();
                    \Log::error('Error changing password', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['error' => 'Failed to change password. Please try again.']);
                }
            }

            // Handle deactivation
            if ($action === 'deactivate') {
                $request->validate([
                    'reason' => 'required|string|min:20'
                ]);

                if ($account->status !== 'active') {
                    DB::rollback();
                    return back()->withErrors(['error' => 'Only active accounts can be deactivated.']);
                }

                try {
                    $result = $this->mofhService->suspendAccount(
                        $account->username,
                        $request->reason
                    );

                    if (!isset($result['success']) || !$result['success']) {
                        DB::rollback();
                        return back()->withErrors(['error' => $result['message'] ?? 'Failed to deactivate account. Please try again.']);
                    }

                    $account->status = 'deactivating';
                    $account->save();

                    // Add suspended notification
                    $this->notificationService->createHostingNotification(
                        auth()->user(), 
                        'suspended', 
                        [
                            'domain' => $account->domain,
                            'username' => $account->username
                        ]
                    );

                    try {
                        Mail::to(auth()->user()->email)
                            ->send(new AccountDeactivatedMail($account, $request->reason));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send deactivation email', [
                            'account_id' => $account->id,
                            'error' => $e->getMessage()
                        ]);
                    }

                    DB::commit();
                    \Log::info('Account deactivation initiated', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'reason' => $request->reason
                    ]);
                    
                    return redirect()->route('hosting.view', ['username' => $account->username])
                        ->with('success', 'Account deactivation initiated. Please wait for confirmation.');
                        
                } catch (\Exception $e) {
                    DB::rollback();
                    \Log::error('Error deactivating account', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['error' => 'Failed to deactivate account. Please try again.']);
                }
            }

            // If we got here, the action is unknown
            DB::rollback();
            \Log::warning('Unknown action type', [
                'action' => $action,
                'account_id' => $account->id,
                'request_data' => $request->except(['password', 'old_password'])
            ]);
            return back()->withErrors(['error' => 'Invalid action type.']);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in updateSettings', [
                'identifier' => $identifier,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error updating settings. Please try again.']);
        }
    }

    /**
     * Reactivate deactivated account
     */
    public function reactivate($username)
    {
        try {
            $account = HostingAccount::where('user_id', auth()->id())
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'deactivated') {
                return redirect()->back()
                    ->withErrors(['error' => 'Only deactivated accounts can be reactivated.']);
            }

            // Check if admin deactivated
            if ($account->admin_deactivated) {
                // Check if there's an existing support ticket for this account
                $existingTicket = DB::table('tickets')
                    ->where('user_id', auth()->id())
                    ->where('service_type', 'hosting')
                    ->where('service_id', $account->id)
                    ->whereIn('status', ['open', 'answered', 'customer-reply', 'pending'])
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                // If there's an existing support ticket, redirect to it
                if ($existingTicket) {
                    return redirect()->route('user.tickets.show', $existingTicket->id)
                        ->with('info', "This account was suspended by an administrator. You already have an open support ticket for this account.");
                }
                
                // If there's no ticket, save information in session and redirect to ticket creation form
                session()->flash('admin_deactivated_account', [
                    'username' => $account->username,
                    'domain' => $account->domain,
                    'deactivation_reason' => $account->admin_deactivation_reason,
                    'deactivated_at' => $account->admin_deactivated_at
                ]);
                
                return redirect()->route('user.tickets.create')
                    ->with('info', "This account was suspended by an administrator. Please submit a support ticket to request reactivation.");
            }

            // Handle normal reactivation (not admin suspended)
            $activeCount = HostingAccount::where('user_id', auth()->id())
                ->whereIn('status', ['active', 'reactivating'])
                ->count();

            if ($activeCount >= 3) {
                return redirect()->back()
                    ->withErrors(['error' => 'You have reached the maximum number of active accounts (3).']);
            }

            DB::beginTransaction();

            $result = $this->mofhService->reactivateAccount($account->username);

            if (!$result) {
                DB::rollback();
                return redirect()->back()
                    ->withErrors(['error' => 'Failed to reactivate account. Please try again.']);
            }

            $account->status = 'reactivating';
            $account->save();

            // Add reactivated notification
            $this->notificationService->createHostingNotification(
                auth()->user(), 
                'reactivated', 
                [
                    'domain' => $account->domain,
                    'username' => $account->username
                ]
            );

            try {
                Mail::to(auth()->user()->email)
                    ->send(new AccountReactivatedMail($account));

                \Log::info('Account reactivation email sent', [
                    'account_id' => $account->id
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send reactivation email', [
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();
            return redirect()->route('hosting.view', $username)
                ->with('success', 'Account reactivation initiated. Please wait for confirmation.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error reactivating account: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error reactivating account. Please try again.']);
        }
    }

    /**
     * Show cPanel login page
     */
    public function cpanel($username)
    {
        $account = HostingAccount::where('user_id', auth()->id())
            ->where('username', $username)
            ->firstOrFail();

        if ($account->status !== 'active') {
            return back()->with('error', 'Account must be active to access cPanel.');
        }

        $settings = MofhApiSetting::first();
        if (!$settings) {
            return back()->with('error', 'MOFH API settings not configured.');
        }

        $cpanel_url = str_replace(['https://', 'http://'], '', $settings->cpanel_url);

        return view('hosting.cpanel', [
            'username' => $account->username,
            'password' => $account->password,
            'cpanel_url' => $cpanel_url
        ]);
    }

    /**
     * Show file manager login page
     */
    public function fileManager($username)
    {
        $account = HostingAccount::where('user_id', auth()->id())
            ->where('username', $username)
            ->firstOrFail();

        if ($account->status !== 'active') {
            return back()->with('error', 'Account must be active to access file manager.');
        }

        return view('hosting.filemanager', [
            'username' => $account->username,
            'password' => $account->password,
            'dir' => '/htdocs/'
        ]);
    }

    /**
     * Get active accounts count
     */
    protected function getActiveAccountsCount()
    {
        return HostingAccount::where('user_id', auth()->id())
            ->whereIn('status', ['active', 'reactivating'])
            ->count();
    }

    /**
     * Clear domain session and redirect back
     */
    public function cancel()
    {
        Session::forget('domain');
        return redirect()->route('hosting.create');
    }

    /**
     * Verify cPanel access
     */
    public function verifyCpanel($username)
    {
        \Log::info('Verifying cPanel access', [
            'username' => $username,
            'user_id' => auth()->id()
        ]);

        try {
            $account = HostingAccount::where('user_id', auth()->id())
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Account must be active to verify cPanel.'
                ]);
            }

            $account->cpanel_verified = true;
            $account->cpanel_verified_at = now();
            $account->save();

            \Log::info('cPanel verification successful', [
                'username' => $username
            ]);

            return response()->json([
                'success' => true,
                'message' => 'cPanel access verified successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('cPanel verification failed', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Access Softaculous installer
     */
    public function softaculous($username)
    {
        $account = HostingAccount::where('user_id', auth()->id())
            ->where('username', $username)
            ->firstOrFail();

        if ($account->status !== 'active') {
            return back()->with('error', 'Account must be active to access Softaculous.');
        }

        try {
            $settings = MofhApiSetting::first();
            if (!$settings) {
                throw new Exception('MOFH API settings not configured.');
            }

            $api = new VistapanelApi();
            $api->setCpanelUrl($settings->cpanel_url);
            
            if($api->login($account->username, $account->password)) {
                $url = $api->getSoftaculousLink();
                
                if(!$url) {
                    throw new Exception('Could not get Softaculous link');
                }
                return redirect()->away($url);
            }

            throw new Exception('Could not login to cPanel');

        } catch (\Exception $e) {
            \Log::error('Softaculous access failed', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to access Softaculous: ' . $e->getMessage());
        }
    }

    /**
     * Get chart statistics
     */
    public function getChartStats($username)
    {
        try {
            $account = HostingAccount::where('user_id', auth()->id())
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Stats are only available for active accounts.'
                ]);
            }

            // Get data from vistapanel (30 days)
            $stats = $this->vistapanel->get_usage_stats(
                $account->username,
                $account->password,
                30
            );

            // Format chart statistics data
            $chartData = [
                'hits' => [
                    'history' => $stats['hits']['history'] ?? [],
                    'limit' => $stats['hits']['limit'] ?? 50000 
                ],
                'bandwidth' => [
                    'history' => $stats['bandwidth']['history'] ?? [],
                    'limit' => 'Unlimited'
                ],
                'inodes' => [
                    'history' => $stats['inodes']['history'] ?? [],  
                    'limit' => $stats['inodes']['total'] ?? 50000
                ],
                'diskspace' => [
                    'history' => $stats['diskspace']['history'] ?? [],
                    'limit' => $stats['diskspace']['total'] ?? 10240
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data'
            ]);
        }
    }

    /**
     * Get current account statistics
     */
    public function getStats($username)
    {
        try {
            $account = HostingAccount::where('user_id', auth()->id())
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Stats are only available for active accounts.'
                ]);
            }

            // Get MOFH settings
            $settings = MofhApiSetting::first();
            if (!$settings) {
                throw new \Exception('MOFH API settings not configured.');
            }

            // Get stats from Vistapanel API
            $api = new VistapanelApi();
            $api->setCpanelUrl($settings->cpanel_url);

            if (!$api->login($account->username, $account->password)) {
                throw new \Exception('Failed to login to cPanel');
            }

            // Get current stats
            $currentStats = $api->getDetailedStats();
            $api->logout();

            // Format stats for response
            $stats = [
                'disk' => [
                    'used' => $currentStats['Disk Space Used']['value'] ?? 0,
                    'total' => $currentStats['Disk Quota']['value'] ?? 10240, // 10GB default
                    'unit' => $currentStats['Disk Space Used']['unit'] ?? 'MB',
                    'percent' => 0
                ],
                'bandwidth' => [
                    'used' => $currentStats['Bandwidth used']['value'] ?? 0,
                    'total' => 'Unlimited',
                    'unit' => $currentStats['Bandwidth used']['unit'] ?? 'MB',
                    'percent' => 0
                ],
                'inodes' => [
                    'used' => $currentStats['Inodes Used']['used'] ?? 0,
                    'total' => $currentStats['Inodes Used']['total'] ?? 50000,
                    'percent' => $currentStats['Inodes Used']['percent'] ?? 0
                ]
            ];

            // Calculate percentages
            if ($stats['disk']['total'] > 0) {
                $stats['disk']['percent'] = round(($stats['disk']['used'] / $stats['disk']['total']) * 100, 1);
            }

            // Convert units if needed
            if (isset($stats['disk']['unit']) && $stats['disk']['unit'] === 'GB') {
                $stats['disk']['used'] *= 1024;
                $stats['disk']['total'] *= 1024;
                $stats['disk']['unit'] = 'MB';
            }

            if (isset($stats['bandwidth']['unit']) && $stats['bandwidth']['unit'] === 'GB') {
                $stats['bandwidth']['used'] *= 1024;
                $stats['bandwidth']['unit'] = 'MB';
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting stats: ' . $e->getMessage(), [
                'username' => $username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    /**
     * Show account details
     */
    public function view($username)
    {
        \Log::info('View request received', [
            'username' => $username,
            'user_id' => auth()->id()
        ]);

        try {
            $account = HostingAccount::where('user_id', auth()->id())
                ->where('username', $username)
                ->first();

            if (!$account) {
                \Log::error('Account not found in view', [
                    'username' => $username,
                    'user_id' => auth()->id()
                ]);
                return redirect()->route('hosting.index')
                    ->withErrors(['error' => 'Hosting account not found.']);
            }

            // Update status if pending
            if ($account->status === 'pending') {
                $this->mofhService->updateAccountStatus($account);
                $account->refresh();
            }

            // Get domains if active
            $domains = $account->status === 'active' ?
                $this->mofhService->getDomains($account->username) : [];

            // Get Server IP
            try {
                $domain = $account->domain;
                $ip = gethostbyname($domain);
                $serverIp = ($ip !== $domain) ? $ip : false;
            } catch (\Exception $e) {
                \Log::error('Error getting server IP', [
                    'account_id' => $account->id,
                    'domain' => $account->domain,
                    'error' => $e->getMessage()
                ]);
                $serverIp = false;
            }

            return view('hosting.view', compact('account', 'domains', 'serverIp'));

        } catch (\Exception $e) {
            \Log::error('Error in view method', [
                'username' => $username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('hosting.index')
                ->withErrors(['error' => 'Error loading account details.']);
        }
    }

    /**
     * Get MySQL databases
     */
    public function databases($username)
    {
        try {
            $account = HostingAccount::where('user_id', auth()->id())
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Databases are only available for active accounts.'
                ]);
            }

            $settings = MofhApiSetting::first();
            if (!$settings) {
                throw new \Exception('MOFH API settings not configured.');
            }

            // Get databases via VistaPanel API
            $api = new VistapanelApi();
            $api->setCpanelUrl($settings->cpanel_url);

            if (!$api->login($account->username, $account->password)) {
                throw new \Exception('Failed to login to cPanel');
            }

            $databases = $api->getDatabases();
            $api->logout();

            return response()->json([
                'success' => true,
                'data' => $databases
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting databases: ' . $e->getMessage(), [
                'username' => $username,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch databases: ' . $e->getMessage()
            ]);
        }
    }
    
    public function builder($username, $domain)
    {
        try {
            $account = HostingAccount::where('user_id', auth()->id())
                ->where('username', $username)
                ->firstOrFail();

            if ($account->status !== 'active') {
                return redirect()->back()
                    ->with('error', 'Account must be active to use Site.pro builder');
            }

            if (!$account->cpanel_verified) {
                return redirect()->back()
                    ->with('error', 'Please verify your cPanel access first');
            }

            // Get Site.pro settings
            $settings = SiteProSetting::first();
            if (!$settings || !$settings->isActive()) {
                return redirect()->back()
                    ->with('error', 'Site.pro builder is not available');
            }

            // Load builder URL
            $result = $settings->loadBuilderUrl(
                $account->username,
                $account->password, 
                $domain,
                '/htdocs/'
            );

            if (!$result['success']) {
                return redirect()->back()
                    ->with('error', $result['message']);
            }

            // Redirect to builder
            return redirect()->away($result['url']);

        } catch (\Exception $e) {
            \Log::error('Error loading Site.pro builder', [
                'username' => $username,
                'domain' => $domain,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to load website builder');
        }
    }
}