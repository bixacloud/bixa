<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Custom2FAMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        
        if (empty($user->google2fa_secret)) {
            return $next($request);
        }
        
       
        if (session('2fa:authenticated')) {
            return $next($request);
        }
        
        
        \Log::info('2FA required - User: ' . $user->email . ', Session status: ' . json_encode(session()->all()));
        
        
        session(['url.intended' => $request->url()]);
        
        
        return redirect()->route('2fa.validate');
    }
}