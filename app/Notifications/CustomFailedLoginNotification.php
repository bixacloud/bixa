<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;
use App\Mail\Auth\FailedLoginMail;

class CustomFailedLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The authentication log.
     *
     * @var \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog
     */
    public $authenticationLog;

    /**
     * Create a new notification instance.
     *
     * @param  \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog  $authenticationLog
     * @return void
     */
    public function __construct($authenticationLog)
    {
        $this->authenticationLog = $authenticationLog;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = $this->formatLogData($this->authenticationLog);
        
        return (new FailedLoginMail($notifiable, $data))
            ->to($notifiable->email);
    }

    /**
     * Format authentication log data for email
     * 
     * @param \Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog $log
     * @return array
     */
    private function formatLogData($log)
    {
        $agent = new Agent();
        $agent->setUserAgent($log->user_agent);

        $location = 'Unknown';
        if (isset($log->location) && is_array($log->location) && isset($log->location['default']) && $log->location['default'] === false) {
            $locationParts = [];
            if (!empty($log->location['city'])) $locationParts[] = $log->location['city'];
            if (!empty($log->location['state'])) $locationParts[] = $log->location['state'];
            if (!empty($log->location['country'])) $locationParts[] = $log->location['country'];
            
            $location = !empty($locationParts) ? implode(', ', $locationParts) : 'Unknown';
        }

        return [
            'time' => $log->login_at->format('Y-m-d H:i:s'),
            'ip_address' => $log->ip_address,
            'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
            'device' => $agent->platform() . ' ' . $agent->version($agent->platform()),
            'location' => $location
        ];
    }
}