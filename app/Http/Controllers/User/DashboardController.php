<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HostingAccount;
use App\Models\Certificate;
use Coderflex\LaravelTicket\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get statistics counts
        $stats = [
            'hosting' => [
                'total' => HostingAccount::where('user_id', Auth::id())->count(),
                'active' => HostingAccount::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->count(),
                'pending' => HostingAccount::where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'deactivating', 'reactivating'])
                    ->count(),
                'deactivated' => HostingAccount::where('user_id', Auth::id())
                    ->where('status', 'deactivated')
                    ->count()
            ],
            'ssl' => [
                'total' => Certificate::where('user_id', Auth::id())->count(),
                'active' => Certificate::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->count(),
                'pending' => Certificate::where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->count()
            ],
            'tickets' => [
                'total' => Ticket::where('user_id', Auth::id())->count(),
                'open' => Ticket::where('user_id', Auth::id())
                    ->where('status', 'open')
                    ->count(),
                'pending' => Ticket::where('user_id', Auth::id())
                    ->where('status', 'pending') 
                    ->count(),
                'closed' => Ticket::where('user_id', Auth::id())
                    ->where('status', 'closed')
                    ->count()
            ]
        ];

        // Get latest hosting accounts
        $accounts = HostingAccount::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get announcements (you'll need to create Announcement model)
        $announcements = []; // Add announcement logic if needed

        return view('user.dashboard', compact('stats', 'accounts', 'announcements'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            // Thêm các validation rules khác
        ]);

        $user = Auth::user();
        $user->update($request->only(['name', 'email']));

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}