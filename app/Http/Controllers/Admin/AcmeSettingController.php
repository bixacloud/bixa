<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcmeSetting;
use Illuminate\Http\Request;

class AcmeSettingController extends Controller
{
    public function edit()
    {
        $settings = AcmeSetting::first() ?? new AcmeSetting();
        return view('admin.ssl.acme', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
            'letsencrypt_url' => 'nullable|url',
            'zerossl_url' => 'nullable|url',
            'zerossl_kid' => 'nullable|string',
            'zerossl_hmac' => 'nullable|string',
            'googletrust_url' => 'nullable|url',
            'googletrust_kid' => 'nullable|string',
            'googletrust_hmac' => 'nullable|string',
            'dns_resolver' => 'nullable|string',
            'dns_doh' => 'nullable|in:active,inactive'
        ]);

        $settings = AcmeSetting::first() ?? new AcmeSetting();
        
        $settings->acme_status = $request->status;
        $settings->acme_letsencrypt = $request->letsencrypt_url;
        $settings->acme_zerossl = [
            'url' => $request->zerossl_url,
            'eab_kid' => $request->zerossl_kid,
            'eab_hmac_key' => $request->zerossl_hmac
        ];
        $settings->acme_googletrust = [
            'url' => $request->googletrust_url,
            'eab_kid' => $request->googletrust_kid,
            'eab_hmac_key' => $request->googletrust_hmac
        ];
        $settings->acme_dns = [
            'resolver' => $request->dns_resolver,
            'doh' => $request->dns_doh
        ];

        $settings->save();

        return back()->with('success', 'ACME settings updated successfully');
    }
}