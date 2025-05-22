<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SmtpSettingController extends Controller
{
    public function index()
    {
        $smtp = SmtpSetting::first() ?? new SmtpSetting();
        return view('admin.settings.smtp', compact('smtp'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:SMTP',
            'hostname' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string',
            'port' => 'required|numeric',
            'encryption' => 'required|in:ssl,tls,none',
            'status' => 'required|boolean'
        ]);

        try {
            $smtp = SmtpSetting::firstOrNew();
            $smtp->fill($validated);
            $smtp->save();

            // Update config at runtime
            config([
                'mail.mailers.smtp.host' => $smtp->hostname,
                'mail.mailers.smtp.port' => $smtp->port,
                'mail.mailers.smtp.username' => $smtp->username,
                'mail.mailers.smtp.password' => $smtp->password,
                'mail.mailers.smtp.encryption' => $smtp->encryption === 'none' ? null : $smtp->encryption,
                'mail.from.address' => $smtp->from_email,
                'mail.from.name' => $smtp->from_name
            ]);

            return redirect()->back()->with('success', 'SMTP settings updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating SMTP settings: ' . $e->getMessage());
        }
    }

    public function test()
    {
        try {
            $smtp = SmtpSetting::firstOrFail();

            if (!$smtp->status) {
                throw new \Exception('SMTP is not active');
            }

            // Test email
            Mail::raw('Test email from ' . config('app.name'), function($message) {
                $message->to(auth()->user()->email)
                    ->subject('SMTP Test Email');
            });

            return redirect()->back()
                ->with('success', 'Test email sent successfully to ' . auth()->user()->email);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'SMTP test failed: ' . $e->getMessage());
        }
    }
}