<?php

namespace App\Listeners;

use Rappasoft\LaravelAuthenticationLog\Events\Login as AuthLogLoginEvent;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateLoginNotification implements ShouldQueue
{
    use InteractsWithQueue;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;
    
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
     * Handle the event.
     *
     * @param AuthLogLoginEvent $event
     * @return void
     */
    public function handle(AuthLogLoginEvent $event)
    {
        // Only create notifications for successful logins
        if (!$event->user || !$event->log || !$event->log->login_successful) {
            return;
        }

        try {
            // Extract the data from the authentication log
            $log = $event->log;
            
            // Extract device information
            $browser = $log->browser ?? null;
            $platform = null;
            $deviceType = null;
            
            // Parse device information from the device field
            if (!empty($log->device)) {
                // The device field typically contains platform and device type
                // Format is often like "OS X (Desktop)" or "Android (Mobile)"
                if (preg_match('/^(.+?) \((.+?)\)$/', $log->device, $matches)) {
                    $platform = $matches[1];
                    $deviceType = $matches[2];
                } else {
                    // If format doesn't match, just use the whole string as platform
                    $platform = $log->device;
                }
            }
            
            // Extract location information
            $location = null;
            if (!empty($log->location) && is_array($log->location)) {
                $cityParts = [];
                
                if (!empty($log->location['city'])) {
                    $cityParts[] = $log->location['city'];
                }
                
                if (!empty($log->location['region_name'])) {
                    $cityParts[] = $log->location['region_name'];
                }
                
                if (!empty($log->location['country'])) {
                    $cityParts[] = $log->location['country'];
                }
                
                if (!empty($cityParts)) {
                    $location = implode(', ', $cityParts);
                }
            }
            
            // Create a login notification using the data from the auth log
            $this->notificationService->createLoginNotification(
                $event->user,
                $log->ip_address,
                $location,
                $browser,
                $platform,
                $deviceType
            );
            
            Log::info('Login notification created from auth log', [
                'user_id' => $event->user->id,
                'auth_log_id' => $log->id,
                'ip' => $log->ip_address,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create login notification from auth log: ' . $e->getMessage(), [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Retry up to the maximum attempts
            if ($this->attempts() < $this->tries) {
                $this->release(now()->addSeconds(5 * $this->attempts()));
            }
        }
    }
}