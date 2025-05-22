<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MofhApiSetting;
use App\Services\MofhService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MofhApiSettingController extends Controller
{
    protected $mofhService;

    public function __construct(MofhService $mofhService)
    {
        $this->mofhService = $mofhService;
    }

    public function index()
    {
        $settings = MofhApiSetting::first() ?? new MofhApiSetting();
        $server_ip = MofhApiSetting::getServerIp();
        $callback_url = MofhApiSetting::getCallbackUrl();
        
        return view('admin.mofh.index', compact('settings', 'server_ip', 'callback_url'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_username' => 'required|string|max:255',
            'api_password' => 'required|string|max:255',
            'plan' => 'required|string|max:255',
            'cpanel_url' => 'required|url|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Save settings
        MofhApiSetting::updateOrCreate(
            ['id' => 1],
            [
                'api_username' => $request->api_username,
                'api_password' => $request->api_password,
                'plan' => $request->plan,
                'cpanel_url' => $request->cpanel_url,
            ]
        );

        return back()->with('success', 'MOFH API settings updated successfully.');
    }

   public function testConnection()
{
    $settings = MofhApiSetting::first();

    if (!$settings) {
        return back()->withErrors(['error' => 'Please configure API settings first.']);
    }

    $result = $this->mofhService->test();

    if ($result['success']) {
        return back()->with('success', $result['message']);
    } else {
        return back()->withErrors(['error' => $result['message']]);
    }
}
}