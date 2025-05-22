<?php

/**
 * Deutsche Sprachdatei für Benachrichtigungen
 */

return [
    // Allgemeine Bezeichnungen und Schaltflächen
    'all' => 'Alle Benachrichtigungen',
    'mark_all_read' => 'Alle als gelesen markieren',
    'view_all' => 'Alle anzeigen',
    'no_notifications' => 'Keine Benachrichtigungen vorhanden',
    'no_notifications_message' => 'Hier werden Ihre Benachrichtigungen angezeigt, sobald sie eintreffen.',
    'notifications' => 'Benachrichtigungen',
    'loading' => 'Wird geladen...',
    'retry' => 'Wiederholen',
    
    // Kategorien
    'categories' => [
        'all' => 'Alle',
        'login' => 'Anmeldung',
        'hosting' => 'Hosting',
        'tickets' => 'Tickets',
        'ssl' => 'SSL',
        'account' => 'Konto',
    ],
    
    // Aktionsschaltflächen
    'actions' => [
        'view' => 'Anzeigen',
        'view_login_history' => 'Anmeldeverlauf anzeigen',
        'view_account' => 'Konto anzeigen',
        'view_ticket' => 'Ticket anzeigen',
        'view_certificate' => 'Zertifikat anzeigen',
        'view_profile' => 'Profil anzeigen',
    ],
    
    // Leere Zustände
    'empty_states' => [
        'login' => 'Keine Anmeldebenachrichtigungen',
        'login_message' => 'Anmeldebenachrichtigungen werden hier angezeigt.',
        'hosting' => 'Keine Hosting-Benachrichtigungen',
        'hosting_message' => 'Hosting-Benachrichtigungen werden hier angezeigt.',
        'ticket' => 'Keine Ticket-Benachrichtigungen',
        'ticket_message' => 'Ticket-Benachrichtigungen werden hier angezeigt.',
        'ssl' => 'Keine SSL-Benachrichtigungen',
        'ssl_message' => 'SSL-Zertifikatsbenachrichtigungen werden hier angezeigt.',
        'account' => 'Keine Kontobenachrichtigungen',
        'account_message' => 'Kontobenachrichtigungen werden hier angezeigt.',
    ],
    
    // Fehlermeldungen
    'errors' => [
        'failed_to_get_count' => 'Fehler beim Abrufen der Anzahl ungelesener Benachrichtigungen',
        'failed_to_get_recent' => 'Fehler beim Abrufen der neuesten Benachrichtigungen',
        'failed_to_mark_read' => 'Fehler beim Markieren der Benachrichtigung als gelesen',
        'failed_to_mark_all_read' => 'Fehler beim Markieren aller Benachrichtigungen als gelesen',
        'failed_to_load' => 'Fehler beim Laden der Benachrichtigungen',
    ],
    
    // Erfolgsmeldungen
    'messages' => [
        'marked_as_read' => 'Benachrichtigung als gelesen markiert',
        'all_marked_as_read' => 'Alle Benachrichtigungen wurden als gelesen markiert',
    ],
    
    // Zeitangaben
    'time' => [
        'just_now' => 'Gerade eben',
        'seconds_ago' => 'vor :count Sekunden',
        'minute_ago' => 'vor 1 Minute',
        'minutes_ago' => 'vor :count Minuten',
        'hour_ago' => 'vor 1 Stunde',
        'hours_ago' => 'vor :count Stunden',
        'day_ago' => 'vor 1 Tag',
        'days_ago' => 'vor :count Tagen',
        'week_ago' => 'vor 1 Woche',
        'weeks_ago' => 'vor :count Wochen',
    ],
    
    // Anmeldebenachrichtigungen
    'login' => [
        'title' => 'Erfolgreiche Anmeldung',
        'content' => 'Sie haben sich erfolgreich von IP-Adresse :ip angemeldet',
        'content_with_location' => 'Sie haben sich erfolgreich von IP-Adresse :ip in :location angemeldet',
        'content_with_location_and_device' => 'Sie haben sich erfolgreich von IP-Adresse :ip in :location mit :browser auf :platform (:device_type) angemeldet',
    ],
    
    // Hosting-Benachrichtigungen
    'hosting' => [
        'created' => [
            'title' => 'Hosting-Konto erstellt',
            'content' => 'Ihr Hosting-Konto für :domain wurde erfolgreich erstellt.',
        ],
        'suspended' => [
            'title' => 'Hosting-Konto ausgesetzt',
            'content' => 'Ihr Hosting-Konto für :domain wurde ausgesetzt.',
        ],
        'reactivated' => [
            'title' => 'Hosting-Konto reaktiviert',
            'content' => 'Ihr Hosting-Konto für :domain wurde reaktiviert.',
        ],
        'password_changed' => [
            'title' => 'Hosting-Passwort geändert',
            'content' => 'Das Passwort für Ihr Hosting-Konto :domain wurde geändert.',
        ],
        'label_changed' => [
            'title' => 'Hosting-Bezeichnung aktualisiert',
            'content' => 'Die Bezeichnung für Ihr Hosting-Konto :domain wurde auf ":label" aktualisiert.',
        ],
    ],
    
    // Ticket-Benachrichtigungen
    'ticket' => [
        'created' => [
            'title' => 'Ticket erstellt',
            'content' => 'Ihr Ticket #:ticket_id wurde erfolgreich erstellt.',
        ],
        'replied' => [
            'title' => 'Neue Antwort auf Ihr Ticket',
            'content' => 'Mitarbeiter :staff_name hat auf Ihr Ticket #:ticket_id geantwortet.',
        ],
        'status_changed' => [
            'title' => 'Ticket-Status geändert',
            'content' => 'Der Status Ihres Tickets #:ticket_id wurde auf :status geändert.',
        ],
        'closed' => [
            'title' => 'Ticket geschlossen',
            'content' => 'Ihr Ticket #:ticket_id wurde geschlossen.',
        ],
    ],
    
    // SSL-Benachrichtigungen
    'ssl' => [
        'created' => [
            'title' => 'SSL-Zertifikat erstellt',
            'content' => 'Ihr SSL-Zertifikat für :domain wurde erfolgreich erstellt.',
        ],
        'activated' => [
            'title' => 'SSL-Zertifikat aktiviert',
            'content' => 'Ihr SSL-Zertifikat für :domain wurde erfolgreich aktiviert.',
        ],
        'revoked' => [
            'title' => 'SSL-Zertifikat widerrufen',
            'content' => 'Ihr SSL-Zertifikat für :domain wurde widerrufen.',
        ],
    ],
    
    // Kontobenachrichtigungen
    'account' => [
        '2fa_enabled' => [
            'title' => 'Zwei-Faktor-Authentifizierung aktiviert',
            'content' => 'Die Zwei-Faktor-Authentifizierung wurde für Ihr Konto aktiviert.',
        ],
        '2fa_disabled' => [
            'title' => 'Zwei-Faktor-Authentifizierung deaktiviert',
            'content' => 'Die Zwei-Faktor-Authentifizierung wurde für Ihr Konto deaktiviert.',
        ],
        'password_changed' => [
            'title' => 'Passwort geändert',
            'content' => 'Das Passwort Ihres Kontos wurde erfolgreich geändert.',
        ],
        'profile_updated' => [
            'title' => 'Profil aktualisiert',
            'content' => 'Ihre Profilinformationen wurden erfolgreich aktualisiert.',
        ],
    ],
];