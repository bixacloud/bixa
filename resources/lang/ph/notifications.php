<?php

/**
 * Filipino (Tagalog) na file ng wika para sa mga notification
 */

return [
    // Mga karaniwang label at buttons
    'all' => 'Lahat ng Notification',
    'mark_all_read' => 'Markahan Lahat Bilang Nabasa',
    'view_all' => 'Tingnan Lahat',
    'no_notifications' => 'Wala pang notification',
    'no_notifications_message' => 'Makikita mo ang iyong mga notification dito kapag dumating ang mga ito.',
    'notifications' => 'Mga Notification',
    'loading' => 'Naglo-load...',
    'retry' => 'Subukan Muli',
    
    // Mga kategorya
    'categories' => [
        'all' => 'Lahat',
        'login' => 'Pag-login',
        'hosting' => 'Hosting',
        'tickets' => 'Mga Ticket',
        'ssl' => 'SSL',
        'account' => 'Account',
    ],
    
    // Mga action button
    'actions' => [
        'view' => 'Tingnan',
        'view_login_history' => 'Tingnan ang Kasaysayan ng Pag-login',
        'view_account' => 'Tingnan ang Account',
        'view_ticket' => 'Tingnan ang Ticket',
        'view_certificate' => 'Tingnan ang Certificate',
        'view_profile' => 'Tingnan ang Profile',
    ],
    
    // Mga empty state
    'empty_states' => [
        'login' => 'Walang notification sa pag-login',
        'login_message' => 'Ang mga notification sa pag-login ay lalabas dito.',
        'hosting' => 'Walang notification sa hosting',
        'hosting_message' => 'Ang mga notification sa hosting ay lalabas dito.',
        'ticket' => 'Walang notification sa ticket',
        'ticket_message' => 'Ang mga notification sa ticket ay lalabas dito.',
        'ssl' => 'Walang notification sa SSL',
        'ssl_message' => 'Ang mga notification sa SSL certificate ay lalabas dito.',
        'account' => 'Walang notification sa account',
        'account_message' => 'Ang mga notification na may kaugnayan sa account ay lalabas dito.',
    ],
    
    // Mga error message
    'errors' => [
        'failed_to_get_count' => 'Hindi makuha ang bilang ng mga hindi nabasa',
        'failed_to_get_recent' => 'Hindi makuha ang mga kamakailang notification',
        'failed_to_mark_read' => 'Hindi mamarkahan ang notification bilang nabasa',
        'failed_to_mark_all_read' => 'Hindi mamarkahan ang lahat ng notification bilang nabasa',
        'failed_to_load' => 'Hindi mai-load ang mga notification',
    ],
    
    // Mga success message
    'messages' => [
        'marked_as_read' => 'Namarkahan ang notification bilang nabasa',
        'all_marked_as_read' => 'Lahat ng notification ay namarkahan na bilang nabasa',
    ],
    
    // Mga time label
    'time' => [
        'just_now' => 'Ngayon lang',
        'seconds_ago' => ':count segundo ang nakalipas',
        'minute_ago' => '1 minuto ang nakalipas',
        'minutes_ago' => ':count minuto ang nakalipas',
        'hour_ago' => '1 oras ang nakalipas',
        'hours_ago' => ':count oras ang nakalipas',
        'day_ago' => '1 araw ang nakalipas',
        'days_ago' => ':count araw ang nakalipas',
        'week_ago' => '1 linggo ang nakalipas',
        'weeks_ago' => ':count linggo ang nakalipas',
    ],
    
    // Mga notification sa pag-login
    'login' => [
        'title' => 'Matagumpay na Pag-login',
        'content' => 'Matagumpay kang nag-login mula sa IP address na :ip',
        'content_with_location' => 'Matagumpay kang nag-login mula sa IP address na :ip sa :location',
        'content_with_location_and_device' => 'Matagumpay kang nag-login mula sa IP address na :ip sa :location gamit ang :browser sa :platform (:device_type)',
    ],
    
    // Mga notification sa hosting
    'hosting' => [
        'created' => [
            'title' => 'Nalikha ang Hosting Account',
            'content' => 'Ang iyong hosting account para sa :domain ay matagumpay na nalikha.',
        ],
        'suspended' => [
            'title' => 'Na-suspend ang Hosting Account',
            'content' => 'Ang iyong hosting account para sa :domain ay na-suspend.',
        ],
        'reactivated' => [
            'title' => 'Na-reactivate ang Hosting Account',
            'content' => 'Ang iyong hosting account para sa :domain ay na-reactivate.',
        ],
        'password_changed' => [
            'title' => 'Nabago ang Password ng Hosting',
            'content' => 'Ang password para sa iyong hosting account :domain ay nabago.',
        ],
        'label_changed' => [
            'title' => 'Na-update ang Label ng Hosting',
            'content' => 'Ang label para sa iyong hosting account :domain ay na-update sa ":label".',
        ],
    ],
    
    // Mga notification sa ticket
    'ticket' => [
        'created' => [
            'title' => 'Nalikha ang Ticket',
            'content' => 'Ang iyong ticket #:ticket_id ay matagumpay na nalikha.',
        ],
        'replied' => [
            'title' => 'Bagong Tugon sa Iyong Ticket',
            'content' => 'Ang staff member na si :staff_name ay tumugon sa iyong ticket #:ticket_id.',
        ],
        'status_changed' => [
            'title' => 'Nabago ang Status ng Ticket',
            'content' => 'Ang status ng iyong ticket #:ticket_id ay nabago sa :status.',
        ],
        'closed' => [
            'title' => 'Nasara ang Ticket',
            'content' => 'Ang iyong ticket #:ticket_id ay nasara na.',
        ],
    ],
    
    // Mga notification sa SSL
    'ssl' => [
        'created' => [
            'title' => 'Nalikha ang SSL Certificate',
            'content' => 'Ang iyong SSL certificate para sa :domain ay matagumpay na nalikha.',
        ],
        'activated' => [
            'title' => 'Na-activate ang SSL Certificate',
            'content' => 'Ang iyong SSL certificate para sa :domain ay matagumpay na na-activate.',
        ],
        'revoked' => [
            'title' => 'Na-revoke ang SSL Certificate',
            'content' => 'Ang iyong SSL certificate para sa :domain ay na-revoke.',
        ],
    ],
    
    // Mga notification sa account
    'account' => [
        '2fa_enabled' => [
            'title' => 'Na-enable ang Two-Factor Authentication',
            'content' => 'Ang two-factor authentication ay na-enable para sa iyong account.',
        ],
        '2fa_disabled' => [
            'title' => 'Na-disable ang Two-Factor Authentication',
            'content' => 'Ang two-factor authentication ay na-disable para sa iyong account.',
        ],
        'password_changed' => [
            'title' => 'Nabago ang Password',
            'content' => 'Ang password ng iyong account ay matagumpay na nabago.',
        ],
        'profile_updated' => [
            'title' => 'Na-update ang Profile',
            'content' => 'Ang impormasyon ng iyong profile ay matagumpay na na-update.',
        ],
    ],
];