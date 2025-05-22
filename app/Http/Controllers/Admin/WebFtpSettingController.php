<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebFtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebFtpSettingController extends Controller
{
    /**
     * Show WebFTP settings page
     */
    public function index()
    {
        $settings = WebFtpSetting::getSettings();
        
        return view('admin.webftp.index', compact('settings'));
    }

    /**
     * Update WebFTP settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'editor_theme' => 'required|string|max:255',
            'max_upload_size' => 'required|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Prepare the data with boolean values for checkboxes
        $data = [
            'enabled' => $request->has('enabled'),
            'use_external_service' => $request->has('use_external_service'),
            'editor_theme' => $request->editor_theme,
            'code_beautify' => $request->has('code_beautify'),
            'code_suggestion' => $request->has('code_suggestion'),
            'auto_complete' => $request->has('auto_complete'),
            'max_upload_size' => $request->max_upload_size,
            'allow_zip_operations' => $request->has('allow_zip_operations')
        ];

        // Save settings
        WebFtpSetting::updateOrCreate(
            ['id' => 1],
            $data
        );

        return back()->with('success', 'WebFTP settings updated successfully.');
    }
}