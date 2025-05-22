<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingAccount;
use App\Models\User;
use App\Services\MofhService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Hosting\AccountDeactivatedMail;
use App\Models\MofhApiSetting;

class AdminHostingController extends Controller
{
    protected $mofhService;

    public function __construct(MofhService $mofhService)
    {
        $this->mofhService = $mofhService;
    }

    /**
     * Display list of all hosting accounts
     */
    public function index(Request $request)
    {
        $query = HostingAccount::with('user');

        // Status filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Search by username, domain, or owner email
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('domain', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Get accounts with pagination
        $accounts = $query->orderBy('created_at', 'desc')
                          ->paginate(20)
                          ->withQueryString();

        // Get status counts for filtering
        $statusCounts = HostingAccount::selectRaw('status, COUNT(*) as count')
                                      ->groupBy('status')
                                      ->pluck('count', 'status')
                                      ->toArray();
        
        $totalAccounts = HostingAccount::count();

        return view('admin.hosting.index', compact('accounts', 'statusCounts', 'totalAccounts'));
    }

    /**
     * Show account details 
     */
    public function view($identifier)
    {
        try {
            // Find account by username or ID
            $account = HostingAccount::with('user')
                ->where('username', $identifier)
                ->orWhere('id', $identifier)
                ->firstOrFail();

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
                Log::error('Error getting server IP', [
                    'account_id' => $account->id,
                    'domain' => $account->domain,
                    'error' => $e->getMessage()
                ]);
                $serverIp = false;
            }

            // Get callback logs
            $callbackLogs = $this->mofhService->getCallbackLogs($account->username);

            return view('admin.hosting.view', compact('account', 'domains', 'serverIp', 'callbackLogs'));

        } catch (\Exception $e) {
            Log::error('Error in admin view method', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.hosting.index')
                ->withErrors(['error' => 'Error loading account details: ' . $e->getMessage()]);
        }
    }

    /**
     * Show account settings
     */
    public function settings($identifier)
    {
        try {
            // Find account by username or ID
            $account = HostingAccount::with('user')
                ->where('username', $identifier)
                ->orWhere('id', $identifier)
                ->firstOrFail();

            return view('admin.hosting.settings', compact('account'));
        } catch (\Exception $e) {
            Log::error('Error loading admin settings page', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.hosting.index')
                ->withErrors(['error' => 'Error loading account settings: ' . $e->getMessage()]);
        }
    }

    /**
     * Update account settings
     */  
    public function updateSettings(Request $request, $identifier)
    {
        Log::info('Admin update settings request received', [
            'identifier' => $identifier,
            'request_method' => $request->method(),
            'request_path' => $request->path(),
            'request_data' => $request->except(['password']),
        ]);

        try {
            // Find account by username or ID
            $account = HostingAccount::with('user')
                ->where('username', $identifier)
                ->orWhere('id', $identifier)
                ->firstOrFail();

            DB::beginTransaction();

            // Determine which action to take
            $action = $request->input('action');
            
            if (!$action) {
                DB::rollback();
                return back()->withErrors(['error' => 'No action specified.'])->withInput();
            }

            // Handle label update
            if ($action === 'update_label') {
                $request->validate([
                    'label' => 'required|string|max:255'
                ]);

                try {
                    $account->label = $request->label;
                    $account->save();
                    
                    DB::commit();
                    Log::info('Admin updated account label', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'new_label' => $request->label
                    ]);
                    
                    return back()->with('success', 'Account label updated successfully.');
                        
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Error updating label by admin', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['error' => 'Failed to update label: ' . $e->getMessage()]);
                }
            }
            
            // Handle admin deactivation
            if ($action === 'admin_deactivate') {
                $request->validate([
                    'reason' => 'required|string|min:10'
                ]);

                if ($account->status !== 'active') {
                    DB::rollback();
                    return back()->withErrors(['error' => 'Only active accounts can be deactivated.']);
                }

                try {
                    // Call MOFH API to suspend account
                    $result = $this->mofhService->suspendAccount(
                        $account->username,
                        "ADMIN: " . $request->reason
                    );

                    if (!isset($result['success']) || !$result['success']) {
                        DB::rollback();
                        return back()->withErrors(['error' => $result['message'] ?? 'Failed to deactivate account.']);
                    }

                    // Mark as admin deactivated
                    $account->status = 'deactivating';
                    $account->admin_deactivated = true;
                    $account->admin_deactivation_reason = $request->reason;
                    $account->admin_deactivated_at = now();
                    $account->save();

                    // Send email to user
                    try {
                        Mail::to($account->user->email)
                            ->send(new AccountDeactivatedMail($account, $request->reason));
                            
                        Log::info('Admin deactivation email sent', [
                            'account_id' => $account->id,
                            'user_email' => $account->user->email
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send admin deactivation email', [
                            'account_id' => $account->id,
                            'error' => $e->getMessage()
                        ]);
                    }

                    DB::commit();
                    Log::info('Account admin deactivation initiated', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'reason' => $request->reason,
                        'admin_id' => auth()->id()
                    ]);
                    
                    return redirect()->route('admin.hosting.view', ['identifier' => $account->username])
                        ->with('success', 'Account deactivation initiated. Account has been flagged as admin-deactivated.');
                        
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Error admin-deactivating account', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['error' => 'Failed to deactivate account: ' . $e->getMessage()]);
                }
            }
            
            // Handle reactivate admin-deactivated account
            if ($action === 'admin_reactivate') {
                if ($account->status !== 'deactivated') {
                    DB::rollback();
                    return back()->withErrors(['error' => 'Only deactivated accounts can be reactivated.']);
                }

                if (!$account->admin_deactivated) {
                    DB::rollback();
                    return back()->withErrors(['error' => 'This account was not deactivated by an admin.']);
                }

                try {
                    // Call MOFH API to reactivate account
                    $result = $this->mofhService->reactivateAccount($account->username);

                    if (!$result) {
                        DB::rollback();
                        return back()->withErrors(['error' => 'Failed to reactivate account.']);
                    }

                    // Remove admin deactivation flags
                    $account->status = 'reactivating';
                    $account->admin_deactivated = false;
                    $account->admin_deactivation_reason = null;
                    $account->admin_deactivated_at = null;
                    $account->save();

                    DB::commit();
                    Log::info('Admin reactivated account', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'admin_id' => auth()->id()
                    ]);
                    
                    return redirect()->route('admin.hosting.view', ['identifier' => $account->username])
                        ->with('success', 'Account reactivation initiated. Admin restrictions have been removed.');
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Error admin-reactivating account', [
                        'account_id' => $account->id,
                        'username' => $account->username,
                        'error' => $e->getMessage()
                    ]);
                    return back()->withErrors(['error' => 'Failed to reactivate account: ' . $e->getMessage()]);
                }
            }

            // If we got here, the action is unknown
            DB::rollback();
            Log::warning('Unknown admin action type', [
                'action' => $action,
                'account_id' => $account->id,
                'request_data' => $request->except(['password'])
            ]);
            return back()->withErrors(['error' => 'Invalid action type.']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in admin updateSettings', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error updating settings: ' . $e->getMessage()]);
        }
    }
	
	/**
 * Show cPanel login page
 */
public function cpanel($identifier)
{
    $account = HostingAccount::where('username', $identifier)
        ->orWhere('id', $identifier)
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
public function fileManager($identifier)
{
    $account = HostingAccount::where('username', $identifier)
        ->orWhere('id', $identifier)
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
 * Verify cPanel access
 */
public function verifyCpanel($identifier)
{
    Log::info('Admin verifying cPanel access', [
        'identifier' => $identifier,
        'admin_id' => auth()->id()
    ]);

    try {
        $account = HostingAccount::where('username', $identifier)
            ->orWhere('id', $identifier)
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

        Log::info('Admin cPanel verification successful', [
            'identifier' => $identifier,
            'admin_id' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'cPanel access verified successfully'
        ]);

    } catch (\Exception $e) {
        Log::error('Admin cPanel verification failed', [
            'identifier' => $identifier,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}