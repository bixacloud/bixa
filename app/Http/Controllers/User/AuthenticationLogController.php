<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuthLogSettings;
use Illuminate\Http\Request;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Illuminate\Support\Facades\Auth;

class AuthenticationLogController extends Controller
{
    /**
     * Display the current user's authentication log
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $logs = AuthenticationLog::where('authenticatable_type', User::class)
            ->where('authenticatable_id', Auth::id())
            ->orderBy('login_at', 'desc')
            ->paginate(10);
            
        // Lấy cài đặt location tracking
        $settings = AuthLogSettings::getSettings();
        $locationEnabled = $settings->location_tracking;

        return view('user.authentication-log', compact('logs', 'locationEnabled'));
    }

    /**
     * Display authentication log for admin (all users)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function adminIndex(Request $request)
    {
        $query = AuthenticationLog::where('authenticatable_type', User::class)
            ->orderBy('login_at', 'desc');
            
        // Filter by user if requested
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('authenticatable_id', $request->user_id);
        }
        
        $logs = $query->paginate(15);
        $users = User::all();
        
        // Lấy cài đặt location tracking
        $settings = AuthLogSettings::getSettings();
        $locationEnabled = $settings->location_tracking;

        return view('admin.authentication-log', compact('logs', 'users', 'locationEnabled'));
    }

    /**
     * Display authentication log for a specific user (admin only)
     *
     * @param int $userId
     * @return \Illuminate\View\View
     */
    public function userLogs($userId)
    {
        $user = User::findOrFail($userId);
        
        $logs = AuthenticationLog::where('authenticatable_type', User::class)
            ->where('authenticatable_id', $userId)
            ->orderBy('login_at', 'desc')
            ->paginate(15);
            
        // Lấy cài đặt location tracking
        $settings = AuthLogSettings::getSettings();
        $locationEnabled = $settings->location_tracking;

        return view('admin.user-authentication-log', compact('logs', 'user', 'locationEnabled'));
    }
}