<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OAuthSetting;

class OAuthSettingsController extends Controller
{
    public function index()
    {
        $oauthSettings = OAuthSetting::all();
        return view('admin.settings.oauth', compact('oauthSettings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        OAuthSetting::create([
            'provider' => $request->provider,
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
        ]);

        return redirect()->back()->with('success', 'OAuth provider added successfully');
    }

    public function toggle($id)
    {
        $setting = OAuthSetting::findOrFail($id);
        $setting->is_enabled = !$setting->is_enabled;
        $setting->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        OAuthSetting::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'OAuth provider deleted successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        $setting = OAuthSetting::findOrFail($id);
        $setting->update([
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
        ]);

        return redirect()->back()->with('success', 'OAuth provider updated successfully');
    }
}
