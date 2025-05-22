<?php

namespace App\Listeners;

use App\Services\NotificationService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\TwoFactorEnabled;
use Illuminate\Auth\Events\TwoFactorDisabled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotificationEventListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Create the event listener.
     *
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle password reset events.
     *
     * @param PasswordReset $event
     * @return void
     */
    public function handlePasswordReset(PasswordReset $event)
    {
        if ($event->user) {
            try {
                $this->notificationService->createAccountNotification(
                    $event->user,
                    'password_changed'
                );
                
                Log::info('Password reset notification created', [
                    'user_id' => $event->user->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create password reset notification: ' . $e->getMessage(), [
                    'user_id' => $event->user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Handle two-factor authentication enabled events.
     *
     * @param TwoFactorEnabled $event
     * @return void
     */
    public function handleTwoFactorEnabled(TwoFactorEnabled $event)
    {
        if ($event->user) {
            try {
                $this->notificationService->createAccountNotification(
                    $event->user,
                    '2fa_enabled'
                );
                
                Log::info('2FA enabled notification created', [
                    'user_id' => $event->user->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create 2FA enabled notification: ' . $e->getMessage(), [
                    'user_id' => $event->user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Handle two-factor authentication disabled events.
     *
     * @param TwoFactorDisabled $event
     * @return void
     */
    public function handleTwoFactorDisabled(TwoFactorDisabled $event)
    {
        if ($event->user) {
            try {
                $this->notificationService->createAccountNotification(
                    $event->user,
                    '2fa_disabled'
                );
                
                Log::info('2FA disabled notification created', [
                    'user_id' => $event->user->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create 2FA disabled notification: ' . $e->getMessage(), [
                    'user_id' => $event->user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe($events)
    {
        // Note: We no longer listen to Login event here since we're now using 
        // the AuthenticationLog events to create login notifications
        $events->listen(
            PasswordReset::class,
            [NotificationEventListener::class, 'handlePasswordReset']
        );
        
        $events->listen(
            TwoFactorEnabled::class,
            [NotificationEventListener::class, 'handleTwoFactorEnabled']
        );
        
        $events->listen(
            TwoFactorDisabled::class,
            [NotificationEventListener::class, 'handleTwoFactorDisabled']
        );
    }
}