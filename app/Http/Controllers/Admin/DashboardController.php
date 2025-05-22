<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HostingAccount;
use Coderflex\LaravelTicket\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // AdminMiddleware đã được cập nhật để cho phép cả admin và support
    }

    public function index()
    {
        $isAdmin = Auth::user()->role === 'admin';
        $isSupport = Auth::user()->role === 'support';

        // Get hosting statistics - hiển thị cho cả admin và support
        $hostingStats = [
            'total' => HostingAccount::count(),
            'active' => HostingAccount::where('status', 'active')->count(),
            'pending' => HostingAccount::where('status', 'pending')->count(),
            'suspended' => HostingAccount::whereIn('status', ['suspended', 'deactivating'])->count(),
            'deactivated' => HostingAccount::where('status', 'deactivated')->count(),
        ];

        // Get ticket statistics - hiển thị cho cả admin và support
        $ticketStats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'pending' => Ticket::where('status', 'pending')->count(),
            'closed' => Ticket::where('status', 'closed')->count()
        ];

        // Get user statistics - chỉ hiển thị cho admin
        $userStats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'support' => User::where('role', 'support')->count(),
            'user' => User::where('role', 'user')->count()
        ];

        // System information
        $systemInfo = [
            'version' => config('app.version', '1.0.0'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.connections.'.config('database.default').'.driver')
        ];

        // Combine all stats
        $stats = [
            'hosting' => $hostingStats,
            'tickets' => $ticketStats,
            'users' => $userStats
        ];

        return view('admin.dashboard', compact('stats', 'systemInfo', 'isAdmin', 'isSupport'));
    }
}