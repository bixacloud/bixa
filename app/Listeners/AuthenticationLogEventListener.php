<?php

namespace App\Listeners;

use App\Services\NotificationService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class AuthenticationLogEventListener implements ShouldQueue
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
     * Handle login events and create notifications.
     *
     * @param Login $event
     * @return void
     */
    public function handleLogin(Login $event)
    {
        try {
            if (!$event->user) {
                return;
            }

            // Wait a short time to allow the authentication log to be created
            sleep(1);
            
            // Get the latest successful login log for this user
            $log = AuthenticationLog::where('authenticatable_type', get_class($event->user))
                ->where('authenticatable_id', $event->user->id)
                ->where('login_successful', true)
                ->orderBy('login_at', 'desc')
                ->first();
            
            if (!$log) {
                Log::warning('No authentication log found for login', [
                    'user_id' => $event->user->id
                ]);
                return;
            }
            
            // Parse user agent to get browser and platform info
            $agent = new Agent();
            $agent->setUserAgent($log->user_agent);
            
            $browser = $agent->browser() . ' ' . $agent->version($agent->browser());
            $platform = $agent->platform() . ' ' . $agent->version($agent->platform());
            $deviceType = $agent->isDesktop() ? 'Desktop' : ($agent->isTablet() ? 'Tablet' : 'Mobile');
            
            // Extract location information
            $location = null;
            if (!empty($log->location) && is_array($log->location)) {
                $cityParts = [];
                
                if (!empty($log->location['city'])) {
                    $cityParts[] = $log->location['city'];
                }
                
                if (!empty($log->location['state_name'])) {
                    $cityParts[] = $log->location['state_name'];
                } elseif (!empty($log->location['state'])) {
                    $cityParts[] = $log->location['state'];
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
                'user_id' => $event->user->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Retry up to the maximum attempts
            if ($this->attempts() < $this->tries) {
                $this->release(now()->addSeconds(5 * $this->attempts()));
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
        $events->listen(
            Login::class,
            [AuthenticationLogEventListener::class, 'handleLogin']
        );
    }
}