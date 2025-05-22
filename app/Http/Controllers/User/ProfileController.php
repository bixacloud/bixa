<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        return view('user.profile');
    }

    public function dashboard()
    {
        $user = Auth::user();
        return view('user.dashboard', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'signature' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        
        // Update signature if user is admin or support
        if (in_array($user->role, ['admin', 'support']) && $request->has('signature')) {
            $user->signature = $request->signature;
        }
        
        $user->save();

        // Add profile updated notification
        $this->notificationService->createAccountNotification(
            auth()->user(), 
            'profile_updated'
        );

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Add password changed notification
        $this->notificationService->createAccountNotification(
            auth()->user(), 
            'password_changed'
        );

        return redirect()->back()->with('success', 'Password changed successfully');
    }
    
    // If you have 2FA functionality, add these methods:
    
    public function enable2FA(Request $request)
    {
        // Your existing 2FA enabling logic
        
        // Add 2FA enabled notification
        $this->notificationService->createAccountNotification(
            auth()->user(), 
            '2fa_enabled'
        );
        
        return redirect()->back()->with('success', 'Two-factor authentication has been enabled');
    }
    
    public function disable2FA(Request $request)
    {
        // Your existing 2FA disabling logic
        
        // Add 2FA disabled notification
        $this->notificationService->createAccountNotification(
            auth()->user(), 
            '2fa_disabled'
        );
        
        return redirect()->back()->with('success', 'Two-factor authentication has been disabled');
    }
}