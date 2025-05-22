<?php

return [
    // Common labels and buttons
    'all' => 'All Notifications',
    'mark_all_read' => 'Mark All Read',
    'view_all' => 'View All',
    'no_notifications' => 'No notifications yet',
    'no_notifications_message' => 'You will see your notifications here when they arrive.',
    'notifications' => 'Notifications',
    'loading' => 'Loading...',
    'retry' => 'Retry',
    
    // Categories
    'categories' => [
        'all' => 'All',
        'login' => 'Login',
        'hosting' => 'Hosting',
        'tickets' => 'Tickets',
        'ssl' => 'SSL',
        'account' => 'Account',
    ],
    
    // Action buttons
    'actions' => [
        'view' => 'View',
        'view_login_history' => 'View Login History',
        'view_account' => 'View Account',
        'view_ticket' => 'View Ticket',
        'view_certificate' => 'View Certificate',
        'view_profile' => 'View Profile',
    ],
    
    // Empty states
    'empty_states' => [
        'login' => 'No login notifications',
        'login_message' => 'Login notifications will appear here.',
        'hosting' => 'No hosting notifications',
        'hosting_message' => 'Hosting notifications will appear here.',
        'ticket' => 'No ticket notifications',
        'ticket_message' => 'Ticket notifications will appear here.',
        'ssl' => 'No SSL notifications',
        'ssl_message' => 'SSL notifications will appear here.',
        'account' => 'No account notifications',
        'account_message' => 'Account notifications will appear here.',
    ],
    
    // Error messages
    'errors' => [
        'failed_to_get_count' => 'Failed to get unread count',
        'failed_to_get_recent' => 'Failed to get recent notifications',
        'failed_to_mark_read' => 'Failed to mark notification as read',
        'failed_to_mark_all_read' => 'Failed to mark all notifications as read',
        'failed_to_load' => 'Failed to load notifications',
    ],
    
    // Success messages
    'messages' => [
        'marked_as_read' => 'Notification marked as read',
        'all_marked_as_read' => 'All notifications marked as read',
    ],
    
    // Time labels
    'time' => [
        'just_now' => 'Just now',
        'seconds_ago' => ':count seconds ago',
        'minute_ago' => '1 minute ago',
        'minutes_ago' => ':count minutes ago',
        'hour_ago' => '1 hour ago',
        'hours_ago' => ':count hours ago',
        'day_ago' => '1 day ago',
        'days_ago' => ':count days ago',
        'week_ago' => '1 week ago',
        'weeks_ago' => ':count weeks ago',
    ],
    
    // Login Notifications
    'login' => [
        'title' => 'Successful Login',
        'content' => 'You logged in successfully from IP address :ip',
        'content_with_location' => 'You logged in successfully from IP address :ip in :location',
        'content_with_location_and_device' => 'You logged in successfully from IP address :ip in :location using :browser on :platform (:device_type)',
    ],
    
    // Hosting Notifications
    'hosting' => [
        'created' => [
            'title' => 'Hosting Account Created',
            'content' => 'Your hosting account for :domain has been created successfully.',
        ],
        'suspended' => [
            'title' => 'Hosting Account Suspended',
            'content' => 'Your hosting account for :domain has been suspended.',
        ],
        'reactivated' => [
            'title' => 'Hosting Account Reactivated',
            'content' => 'Your hosting account for :domain has been reactivated.',
        ],
        'password_changed' => [
            'title' => 'Hosting Password Changed',
            'content' => 'The password for your hosting account :domain has been changed.',
        ],
        'label_changed' => [
            'title' => 'Hosting Label Updated',
            'content' => 'The label for your hosting account :domain has been updated to ":label".',
        ],
    ],
    
    // Ticket Notifications
    'ticket' => [
        'created' => [
            'title' => 'Ticket Created',
            'content' => 'Your ticket #:ticket_id has been created successfully.',
        ],
        'replied' => [
            'title' => 'New Reply to Your Ticket',
            'content' => 'Staff member :staff_name has replied to your ticket #:ticket_id.',
        ],
        'status_changed' => [
            'title' => 'Ticket Status Changed',
            'content' => 'The status of your ticket #:ticket_id has been changed to :status.',
        ],
        'closed' => [
            'title' => 'Ticket Closed',
            'content' => 'Your ticket #:ticket_id has been closed.',
        ],
    ],
    
    // SSL Notifications
    'ssl' => [
        'created' => [
            'title' => 'SSL Certificate Created',
            'content' => 'Your SSL certificate for :domain has been created successfully.',
        ],
        'activated' => [
            'title' => 'SSL Certificate Activated',
            'content' => 'Your SSL certificate for :domain has been activated successfully.',
        ],
        'revoked' => [
            'title' => 'SSL Certificate Revoked',
            'content' => 'Your SSL certificate for :domain has been revoked.',
        ],
    ],
    
    // Account Notifications
    'account' => [
        '2fa_enabled' => [
            'title' => 'Two-Factor Authentication Enabled',
            'content' => 'Two-factor authentication has been enabled for your account.',
        ],
        '2fa_disabled' => [
            'title' => 'Two-Factor Authentication Disabled',
            'content' => 'Two-factor authentication has been disabled for your account.',
        ],
        'password_changed' => [
            'title' => 'Password Changed',
            'content' => 'Your account password has been changed successfully.',
        ],
        'profile_updated' => [
            'title' => 'Profile Updated',
            'content' => 'Your profile information has been updated successfully.',
        ],
    ],
];