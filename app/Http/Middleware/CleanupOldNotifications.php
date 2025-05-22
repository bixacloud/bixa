<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CleanupOldNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only run cleanup randomly (5% chance) to avoid impacting performance
        if (Auth::check() && rand(1, 100) <= 5) {
            // Run cleanup after the response has been sent to the browser
            if (function_exists('fastcgi_finish_request')) {
                // If PHP is running with FastCGI, send the response and continue execution
                $response->send();
                fastcgi_finish_request();
                $this->runCleanup();
            } else {
                // Otherwise, run cleanup in a separate process if possible
                register_shutdown_function([$this, 'runCleanup']);
            }
        }
        
        return $response;
    }
    
    /**
     * Run the cleanup process
     */
    protected function runCleanup()
    {
        try {
            // Delete notifications older than 30 days
            $days = config('notifications.cleanup_days', 30);
            $count = Notification::deleteOldNotifications($days);
            
            if ($count > 0) {
                Log::info("Automatically cleaned up {$count} old notifications.");
            }
        } catch (\Exception $e) {
            Log::error('Error in automatic notification cleanup: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}