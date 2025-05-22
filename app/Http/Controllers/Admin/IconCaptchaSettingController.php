<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IconCaptchaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IconCaptchaSettingController extends Controller
{
    /**
     * Display the captcha settings and test page.
     */
    public function index()
    {
        $enabled = IconCaptchaSetting::isEnabled('enabled', true);
        $theme = IconCaptchaSetting::get('theme', 'light');
        
        return view('admin.captcha.index', compact('enabled', 'theme'));
    }

    /**
     * Update the captcha settings.
     */
    public function update(Request $request)
    {
        // Validate the request
        $request->validate([
            'enabled' => 'nullable',
            'theme' => 'required|in:light,dark'
        ]);

        // Update enabled setting (checkbox comes as "on" or null)
        IconCaptchaSetting::set('enabled', $request->has('enabled') ? '1' : '0');
        
        // Update theme setting
        IconCaptchaSetting::set('theme', $request->theme);

        // Clear all captcha settings cache
        Cache::flush();

        return redirect()->route('admin.captcha.index')
            ->with('success', 'Captcha settings updated successfully.');
    }

    /**
     * Validate a test captcha submission.
     */
    public function validateTestCaptcha(Request $request)
    {
        $captcha = new \IconCaptcha\IconCaptcha(config('iconcaptcha'));
        $validation = $captcha->validate($request->all());

        if ($validation->success()) {
            return redirect()->route('admin.captcha.index')
                ->with('success', 'Captcha validated successfully!');
        } else {
            return redirect()->route('admin.captcha.index')
                ->withErrors(['captcha' => 'Captcha validation failed: ' . $validation->getErrorCode()]);
        }
    }
}