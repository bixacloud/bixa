<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }
    
    public function index()
    {
        $settings = [
            'site_title' => Setting::get('site_title', config('app.name')),
            'footer_text' => Setting::get('footer_text', '© ' . date('Y') . ' By bixa'),
            'imgur_client_id' => Setting::get('imgur_client_id', ''),
            'imgur_client_secret' => Setting::get('imgur_client_secret', ''),
            'enable_image_upload' => Setting::get('enable_image_upload', '0'),
            'affiliate_id' => Setting::get('affiliate_id', '12345'), // Default affiliate ID
            
            // PageSpeed Insights settings
            'pagespeed_api_key' => Setting::get('pagespeed_api_key', ''),
            'enable_pagespeed' => Setting::get('enable_pagespeed', '0'),
            'pagespeed_default_strategy' => Setting::get('pagespeed_default_strategy', 'mobile'),
        ];
        return view('admin.settings', compact('settings'));
    }
    
    public function update(Request $request)
    {
        try {
            $request->validate([
                'site_title' => 'required|string|max:255',
                'footer_text' => 'required|string|max:1000',
                'imgur_client_id' => 'nullable|string|max:255',
                'imgur_client_secret' => 'nullable|string|max:255',
                'enable_image_upload' => 'nullable|in:0,1',
                'affiliate_id' => 'nullable|string|max:255',
                
                // PageSpeed Insights validation
                'pagespeed_api_key' => 'nullable|string|max:255',
                'enable_pagespeed' => 'nullable|in:0,1',
                'pagespeed_default_strategy' => 'nullable|in:mobile,desktop',
            ]);
            // Update settings
            Setting::set('site_title', $request->site_title);
            Setting::set('footer_text', $request->footer_text);
            
            // Update Imgur settings
            Setting::set('imgur_client_id', $request->imgur_client_id);
            Setting::set('imgur_client_secret', $request->imgur_client_secret);
            Setting::set('enable_image_upload', $request->enable_image_upload ?? '0');
            
            // Update affiliate ID
            Setting::set('affiliate_id', $request->affiliate_id);
            
            // Update PageSpeed Insights settings
            Setting::set('pagespeed_api_key', $request->pagespeed_api_key);
            Setting::set('enable_pagespeed', $request->enable_pagespeed ?? '0');
            Setting::set('pagespeed_default_strategy', $request->pagespeed_default_strategy);
            
            // Clear cache if you're caching settings
            Cache::forget('site_settings');
            return redirect()->back()->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating settings: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating settings. Please try again.');
        }
    }
    
    public function documentation()
    {
        return view('admin.documentation');
    }
    
    public function tos() 
    {
        return view('admin.tos');
    }
    
    public function about()
    {
        return view('admin.about'); 
    }
    
    public function license()
    {
        return view('admin.license');
    }  
    
    public function donate()
    {
        return view('admin.donate');
    }
}