<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a new notification for a user.
     *
     * @param mixed $user User model or user ID
     * @param string $type Notification type (e.g., 'login', 'hosting', 'ticket')
     * @param string $titleKey Translation key for the title
     * @param string $contentKey Translation key for the content
     * @param array $titleParams Parameters for title translation
     * @param array $contentParams Parameters for content translation
     * @param array $options Additional options (icon_class, color_class, etc.)
     * @return Notification|null
     */
    public function create(
        $user, 
        string $type, 
        string $titleKey, 
        string $contentKey, 
        array $titleParams = [], 
        array $contentParams = [], 
        array $options = []
    ) {
        try {
            $userId = $user instanceof User ? $user->id : $user;
            
            // Store translation keys directly, not translated text
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title_key' => $titleKey,
                'content_key' => $contentKey,
                'title_params' => $titleParams,
                'content_params' => $contentParams,
                'icon_class' => $options['icon_class'] ?? null,
                'color_class' => $options['color_class'] ?? null,
                'image' => $options['image'] ?? null,
                'action_text_key' => $options['action_text_key'] ?? null,
                'action_url' => $options['action_url'] ?? null,
                'meta_data' => $options['meta_data'] ?? null,
                'is_read' => false,
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage(), [
                'user' => $user instanceof User ? $user->id : $user,
                'type' => $type,
                'title_key' => $titleKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }

    /**
     * Create a login notification with device information.
     * This is now primarily called by the CreateLoginNotification listener
     * which gets data from the AuthenticationLog.
     *
     * @param User $user The user who logged in
     * @param string $ipAddress IP address of the login
     * @param string|null $location Location information (city, country)
     * @param string|null $browser Browser information (e.g., "Chrome 99.0.4844.51")
     * @param string|null $platform Platform information (e.g., "Windows 10", "Android 11")
     * @param string|null $deviceType Device type (e.g., "Desktop", "Mobile", "Tablet")
     * @return Notification|null
     */
    public function createLoginNotification(
        User $user, 
        string $ipAddress, 
        ?string $location = null,
        ?string $browser = null,
        ?string $platform = null,
        ?string $deviceType = null
    ) {
        // Use translation keys for login notifications
        $titleKey = 'notifications.login.title';
        
        // Determine which content key to use based on available information
        if ($location && ($browser || $platform)) {
            $contentKey = 'notifications.login.content_with_location_and_device';
            $contentParams = [
                'ip' => $ipAddress,
                'location' => $location,
                'browser' => $browser ?: 'Unknown browser',
                'platform' => $platform ?: 'Unknown platform',
                'device_type' => $deviceType ?: 'Device',
            ];
        } elseif ($location) {
            $contentKey = 'notifications.login.content_with_location';
            $contentParams = [
                'ip' => $ipAddress,
                'location' => $location,
            ];
        } else {
            $contentKey = 'notifications.login.content';
            $contentParams = [
                'ip' => $ipAddress,
            ];
        }
        
        return $this->create(
            $user,
            'login',
            $titleKey,
            $contentKey,
            [],
            $contentParams,
            [
                'icon_class' => 'bx bx-log-in',
                'action_text_key' => 'notifications.actions.view_login_history',
                'action_url' => route('user.authentication-logs'),
                'meta_data' => [
                    'ip_address' => $ipAddress,
                    'location' => $location,
                    'browser' => $browser,
                    'platform' => $platform,
                    'device_type' => $deviceType,
                ]
            ]
        );
    }

    /**
     * Create a hosting account notification.
     *
     * @param User $user The user
     * @param string $action The action type (created, suspended, etc.)
     * @param array $data Additional data for the notification
     * @return Notification|null
     */
    public function createHostingNotification(User $user, string $action, array $data)
    {
        $titleKey = 'notifications.hosting.' . $action . '.title';
        $contentKey = 'notifications.hosting.' . $action . '.content';
        
        $actionUrl = isset($data['username']) 
            ? route('hosting.view', ['username' => $data['username']])
            : route('hosting.index');
        
        return $this->create(
            $user,
            'hosting',
            $titleKey,
            $contentKey,
            [],
            $data,
            [
                'icon_class' => 'bx bx-server',
                'action_text_key' => 'notifications.actions.view_account',
                'action_url' => $actionUrl,
                'meta_data' => $data
            ]
        );
    }

    /**
     * Create a ticket notification.
     *
     * @param User $user The user
     * @param string $action The action type (created, replied, etc.)
     * @param array $data Additional data for the notification
     * @return Notification|null
     */
    public function createTicketNotification(User $user, string $action, array $data)
    {
        $titleKey = 'notifications.ticket.' . $action . '.title';
        $contentKey = 'notifications.ticket.' . $action . '.content';
        
        return $this->create(
            $user,
            'ticket',
            $titleKey,
            $contentKey,
            [],
            $data,
            [
                'icon_class' => 'bx bx-support',
                'action_text_key' => 'notifications.actions.view_ticket',
                'action_url' => route('user.tickets.show', $data['ticket_id']),
                'meta_data' => $data
            ]
        );
    }

    /**
     * Create an SSL certificate notification.
     *
     * @param User $user The user
     * @param string $action The action type (created, activated, etc.)
     * @param array $data Additional data for the notification
     * @return Notification|null
     */
    public function createSSLNotification(User $user, string $action, array $data)
    {
        $titleKey = 'notifications.ssl.' . $action . '.title';
        $contentKey = 'notifications.ssl.' . $action . '.content';
        
        return $this->create(
            $user,
            'ssl',
            $titleKey,
            $contentKey,
            [],
            $data,
            [
                'icon_class' => 'bx bx-lock-alt',
                'action_text_key' => 'notifications.actions.view_certificate',
                'action_url' => route('ssl.show', $data['certificate_id']),
                'meta_data' => $data
            ]
        );
    }

    /**
     * Create a user account notification.
     *
     * @param User $user The user
     * @param string $action The action type (2fa_enabled, password_changed, etc.)
     * @param array $data Additional data for the notification
     * @return Notification|null
     */
    public function createAccountNotification(User $user, string $action, array $data = [])
    {
        $titleKey = 'notifications.account.' . $action . '.title';
        $contentKey = 'notifications.account.' . $action . '.content';
        
        return $this->create(
            $user,
            'account',
            $titleKey,
            $contentKey,
            [],
            $data,
            [
                'icon_class' => 'bx bx-user',
                'action_text_key' => 'notifications.actions.view_profile',
                'action_url' => route('profile'),
                'meta_data' => $data
            ]
        );
    }

    /**
     * Mark a notification as read.
     *
     * @param User $user The user
     * @param int $notificationId The notification ID
     * @return bool Success or failure
     */
    public function markAsRead(User $user, $notificationId)
    {
        try {
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $notificationId)
                ->where('is_read', false)
                ->first();
            
            if ($notification) {
                return $notification->markAsRead();
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param User $user The user
     * @return int|bool Number of notifications marked as read or false on failure
     */
    public function markAllAsRead(User $user)
    {
        try {
            return Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => Carbon::now(),
                ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return false;
        }
    }

    /**
     * Get unread notification count for a user.
     *
     * @param User $user The user
     * @return int Unread notification count
     */
    public function getUnreadCount(User $user)
    {
        try {
            return Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            Log::error('Failed to get unread notification count: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return 0;
        }
    }

    /**
     * Get recent notifications for a user.
     *
     * @param User $user The user
     * @param int $limit Maximum number of notifications to retrieve
     * @return \Illuminate\Support\Collection Collection of notifications
     */
    public function getRecent(User $user, int $limit = 5)
    {
        try {
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
            
            return $notifications->map(function ($notification) {
                return $notification->formatForApi();
            });
        } catch (\Exception $e) {
            Log::error('Failed to get recent notifications: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'limit' => $limit,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return collect([]);
        }
    }
    
    /**
     * Clean up old notifications.
     * This method is intended to be called during normal application traffic,
     * not via cron or console commands.
     *
     * @param int $days Number of days to keep notifications
     * @param int $probability Probability percentage to run cleanup (0-100)
     * @return int Number of notifications deleted
     */
    public function cleanupOldNotifications($days = 30, $probability = 5)
    {
        // Only run cleanup based on probability (e.g., 5% chance)
        // This prevents the cleanup from running on every request
        if (rand(1, 100) > $probability) {
            return 0;
        }
        
        try {
            return Notification::deleteOldNotifications($days);
        } catch (\Exception $e) {
            Log::error('Error cleaning up old notifications: ' . $e->getMessage(), [
                'days' => $days,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return 0;
        }
    }
}