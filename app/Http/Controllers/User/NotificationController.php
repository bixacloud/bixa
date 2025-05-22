<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }
    
    /**
     * Display user's notifications.
     */
    public function index()
    {
        // Trigger cleanup of old notifications with a 5% chance
        $this->notificationService->cleanupOldNotifications(30, 5);
        
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        // Group notifications by type
        $loginNotifications = $notifications->filter(fn($n) => $n->type === 'login');
        $hostingNotifications = $notifications->filter(fn($n) => $n->type === 'hosting');
        $ticketNotifications = $notifications->filter(fn($n) => $n->type === 'ticket');
        $sslNotifications = $notifications->filter(fn($n) => $n->type === 'ssl');
        $accountNotifications = $notifications->filter(fn($n) => $n->type === 'account');
            
        return view('notifications.index', compact(
            'notifications',
            'loginNotifications',
            'hostingNotifications',
            'ticketNotifications',
            'sslNotifications',
            'accountNotifications'
        ));
    }
    
    /**
     * Get user's unread notification count (for AJAX requests).
     */
    public function getUnreadCount()
    {
        try {
            $count = $this->notificationService->getUnreadCount(Auth::user());
                
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting unread count: " . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => trans('notifications.errors.failed_to_get_count'),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get user's recent notifications for dropdown (for AJAX requests).
     */
    public function getRecent()
    {
        try {
            // Trigger cleanup of old notifications with a 5% chance
            $this->notificationService->cleanupOldNotifications(30, 5);
            
            $notifications = $this->notificationService->getRecent(Auth::user(), 5);
            $unreadCount = $this->notificationService->getUnreadCount(Auth::user());
                
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting recent notifications: " . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => trans('notifications.errors.failed_to_get_recent'),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Mark a notification as read.
     * 
     * @param Request $request - The HTTP request
     * @param int $id - The notification ID
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $success = $this->notificationService->markAsRead(Auth::user(), $id);
            
            // Find the notification to access its action URL if needed
            $notification = Notification::find($id);
            
            // For AJAX requests, return JSON response
            if ($request->ajax()) {
                return response()->json([
                    'success' => $success
                ]);
            }
            
            // For redirect requests, redirect to the notification's action URL or back
            if ($request->has('redirect') && $notification && $notification->action_url) {
                return redirect($notification->action_url);
            }
            
            return back()->with('success', trans('notifications.messages.marked_as_read'));
        } catch (\Exception $e) {
            Log::error("Error marking notification as read: " . $e->getMessage(), [
                'notification_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('notifications.errors.failed_to_mark_read'),
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            
            return back()->with('error', trans('notifications.errors.failed_to_mark_read'));
        }
    }
    
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $success = $this->notificationService->markAllAsRead(Auth::user());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => $success
                ]);
            }
            
            return back()->with('success', trans('notifications.messages.all_marked_as_read'));
        } catch (\Exception $e) {
            Log::error("Error marking all notifications as read: " . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('notifications.errors.failed_to_mark_all_read'),
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            
            return back()->with('error', trans('notifications.errors.failed_to_mark_all_read'));
        }
    }
    
    /**
     * Change the locale for notifications.
     */
    public function refreshLocale(Request $request)
    {
        try {
            $locale = $request->input('locale');
            
            if ($locale && in_array($locale, config('app.available_locales', ['en']))) {
                // Store the locale in the session
                Session::put('locale', $locale);
                App::setLocale($locale);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Locale updated successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid locale'
            ], 422);
            
        } catch (\Exception $e) {
            Log::error("Error refreshing locale: " . $e->getMessage(), [
                'user_id' => Auth::id(),
                'locale' => $request->input('locale'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update locale',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Manually trigger cleanup of old notifications.
     * This can be linked from the admin interface.
     */
    public function cleanup(Request $request)
    {
        // Only allow admin users
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('notifications.index')
                ->with('error', 'Unauthorized access');
        }
        
        try {
            // Force cleanup with 100% probability
            $days = $request->input('days', 30);
            $count = $this->notificationService->cleanupOldNotifications($days, 100);
            
            return back()->with('success', "Successfully cleaned up $count old notifications.");
        } catch (\Exception $e) {
            Log::error("Error during manual notification cleanup: " . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error cleaning up notifications: ' . $e->getMessage());
        }
    }
    
    /**
     * Health check endpoint for debugging
     */
    public function healthCheck()
    {
        return response()->json([
            'status' => 'ok',
            'time' => now()->toDateTimeString(),
            'auth' => Auth::check() ? 'authenticated' : 'unauthenticated',
            'user_id' => Auth::id(),
            'locale' => App::getLocale(),
            'available_locales' => config('app.available_locales', ['en']),
        ]);
    }
}