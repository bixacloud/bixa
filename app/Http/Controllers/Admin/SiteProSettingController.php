<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteProSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SiteProSettingController extends Controller
{
    public function index()
    {
        $settings = SiteProSetting::firstOrCreate(
            [], 
            [
                'hostname' => '',
                'username' => '',
                'password' => '',
                'status' => false
            ]
        );

        return view('admin.sitepro.index', compact('settings'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'hostname' => 'required|url',
                'username' => 'required|string',
                'password' => 'required|string',
                'status' => 'required|boolean'
            ]);

            $settings = SiteProSetting::first();
            
            if (!$settings) {
                $settings = new SiteProSetting();
            }

            $settings->fill($request->only([
                'hostname',
                'username',
                'password',
                'status'
            ]));

            $settings->save();

            Log::info('Site.pro settings updated', [
                'hostname' => $settings->hostname
            ]);

            return redirect()->back()->with('success', 'Settings updated successfully');

        } catch (\Exception $e) {
            Log::error('Error updating Site.pro settings', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update settings'])
                ->withInput();
        }
    }
}
