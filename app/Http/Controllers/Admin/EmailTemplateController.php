<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MailTemplates\Models\MailTemplate;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = MailTemplate::all()->map(function($template) {
            return [
                'id' => $template->id,
                'subject' => $template->subject,
                'mailable' => class_basename($template->mailable),
                'used_for' => $this->getUsageDescription(class_basename($template->mailable))
            ];
        });

        return view('admin.email.index', compact('templates'));
    }
	
    private function getUsageDescription($classBasename)
    {
        return match($classBasename) {
            'VerifyEmailMail' => 'Email Verification',
            'ResetPasswordMail' => 'Password Reset',
            'AccountCreatedMail' => 'Hosting Account Created',
            'AccountDeactivatedMail' => 'Hosting Account Deactivated',
            'AccountReactivatedMail' => 'Hosting Account Reactivated',
            'NewTicketMail' => 'New Support Ticket',
            'TicketReplyMail' => 'Ticket Reply',
            'TicketStatusChangedMail' => 'Ticket Status Update',
            'NewDeviceLoginMail' => 'New Device Login Alert',
            'FailedLoginMail' => 'Failed Login Attempt Alert',
            'BulkNotification' => 'Bulk Email Notification',
            'MigrationNotification' => 'Migration Notification',
            'MigrationPasswordResetMail' => 'Migration Password Reset',
            default => ucwords(str_replace('Mail', '', $classBasename))
        };
    }
	
    public function edit($id)
    {
        $template = MailTemplate::findOrFail($id);

        // Map trực tiếp từ tên class
        $variables = [
            'App\Mail\Auth\VerifyEmailMail' => ['name', 'verification_url'],
            'App\Mail\Auth\ResetPasswordMail' => ['name', 'reset_url'],
            'App\Mail\Hosting\AccountCreatedMail' => ['username', 'password', 'domain', 'cpanel_url', 'label'],
            'App\Mail\Hosting\AccountDeactivatedMail' => ['username', 'domain', 'label', 'reason'],
            'App\Mail\Hosting\AccountReactivatedMail' => ['username', 'domain', 'label', 'cpanel_url'],
            'App\Mail\Hosting\PasswordChangedMail' => ['username', 'domain', 'new_password', 'cpanel_url'],
            'App\Mail\Ticket\NewTicketMail' => ['ticket_id', 'title', 'message', 'category', 'priority', 'service_type', 'service_info'],
            'App\Mail\Ticket\TicketReplyMail' => ['ticket_id', 'title', 'reply', 'replier'],
            'App\Mail\Ticket\TicketStatusChangedMail' => ['ticket_id', 'title', 'old_status', 'new_status'],
            'App\Mail\Auth\NewDeviceLoginMail' => ['name', 'time', 'ip_address', 'browser', 'device', 'location'],
            'App\Mail\Auth\FailedLoginMail' => ['name', 'time', 'ip_address', 'browser', 'device', 'location'],
            'App\Mail\Admin\BulkNotification' => ['content'],
            'App\Mail\Admin\MigrationNotification' => ['content'],
            'App\Mail\Admin\MigrationPasswordResetMail' => ['site_name', 'name', 'email', 'password', 'role', 'login_url'],
        ];

        // Lấy variables dựa trên mailable class
        $templateVariables = $variables[$template->mailable] ?? [];

        return view('admin.email.edit', compact('template', 'templateVariables'));
    }

    public function update(Request $request, $id)
    {
        $template = MailTemplate::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'html_template' => 'required|string'
        ]);

        $template->update([
            'subject' => $validated['subject'],
            'html_template' => $validated['html_template']
        ]);

        return redirect()
            ->route('admin.email.index')
            ->with('success', 'Email template updated successfully');
    }
}