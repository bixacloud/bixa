<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Services\NotificationService;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['lang']);
    }

    public function root()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard');
        }
        
        // Redirect về trang login nếu chưa đăng nhập
        return redirect()->route('login');
    }

}