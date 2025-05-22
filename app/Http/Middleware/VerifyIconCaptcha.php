<?php

namespace App\Http\Middleware;

use Closure;
use IconCaptcha\IconCaptcha;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyIconCaptcha
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip validation for non-POST requests
        if ($request->method() !== 'POST') {
            return $next($request);
        }

        // Create an instance of IconCaptcha
        $captcha = new IconCaptcha(config('iconcaptcha'));

        // Validate the captcha
        $validation = $captcha->validate($request->all());

        // If validation fails, redirect back with error
        if (!$validation->success()) {
            return back()
                ->withInput()
                ->withErrors(['iconcaptcha' => 'Please complete the captcha correctly.']);
        }

        return $next($request);
    }
}