<?php

/**
 * File di lingua italiano per le notifiche
 */

return [
    // Etichette comuni e pulsanti
    'all' => 'Tutte le notifiche',
    'mark_all_read' => 'Segna tutte come lette',
    'view_all' => 'Visualizza tutte',
    'no_notifications' => 'Nessuna notifica',
    'no_notifications_message' => 'Vedrai le tue notifiche qui quando arriveranno.',
    'notifications' => 'Notifiche',
    'loading' => 'Caricamento...',
    'retry' => 'Riprova',
    
    // Categorie
    'categories' => [
        'all' => 'Tutte',
        'login' => 'Accesso',
        'hosting' => 'Hosting',
        'tickets' => 'Ticket',
        'ssl' => 'SSL',
        'account' => 'Account',
    ],
    
    // Pulsanti di azione
    'actions' => [
        'view' => 'Visualizza',
        'view_login_history' => 'Visualizza cronologia accessi',
        'view_account' => 'Visualizza account',
        'view_ticket' => 'Visualizza ticket',
        'view_certificate' => 'Visualizza certificato',
        'view_profile' => 'Visualizza profilo',
    ],
    
    // Stati vuoti
    'empty_states' => [
        'login' => 'Nessuna notifica di accesso',
        'login_message' => 'Le notifiche di accesso appariranno qui.',
        'hosting' => 'Nessuna notifica di hosting',
        'hosting_message' => 'Le notifiche di hosting appariranno qui.',
        'ticket' => 'Nessuna notifica di ticket',
        'ticket_message' => 'Le notifiche di ticket appariranno qui.',
        'ssl' => 'Nessuna notifica SSL',
        'ssl_message' => 'Le notifiche di certificato SSL appariranno qui.',
        'account' => 'Nessuna notifica dell\'account',
        'account_message' => 'Le notifiche relative all\'account appariranno qui.',
    ],
    
    // Messaggi di errore
    'errors' => [
        'failed_to_get_count' => 'Impossibile ottenere il conteggio delle notifiche non lette',
        'failed_to_get_recent' => 'Impossibile ottenere le notifiche recenti',
        'failed_to_mark_read' => 'Impossibile segnare la notifica come letta',
        'failed_to_mark_all_read' => 'Impossibile segnare tutte le notifiche come lette',
        'failed_to_load' => 'Impossibile caricare le notifiche',
    ],
    
    // Messaggi di successo
    'messages' => [
        'marked_as_read' => 'Notifica segnata come letta',
        'all_marked_as_read' => 'Tutte le notifiche sono state segnate come lette',
    ],
    
    // Etichette temporali
    'time' => [
        'just_now' => 'Proprio ora',
        'seconds_ago' => ':count secondi fa',
        'minute_ago' => '1 minuto fa',
        'minutes_ago' => ':count minuti fa',
        'hour_ago' => '1 ora fa',
        'hours_ago' => ':count ore fa',
        'day_ago' => '1 giorno fa',
        'days_ago' => ':count giorni fa',
        'week_ago' => '1 settimana fa',
        'weeks_ago' => ':count settimane fa',
    ],
    
    // Notifiche di accesso
    'login' => [
        'title' => 'Accesso riuscito',
        'content' => 'Hai effettuato l\'accesso con successo dall\'indirizzo IP :ip',
        'content_with_location' => 'Hai effettuato l\'accesso con successo dall\'indirizzo IP :ip in :location',
        'content_with_location_and_device' => 'Hai effettuato l\'accesso con successo dall\'indirizzo IP :ip in :location utilizzando :browser su :platform (:device_type)',
    ],
    
    // Notifiche di hosting
    'hosting' => [
        'created' => [
            'title' => 'Account di hosting creato',
            'content' => 'Il tuo account di hosting per :domain è stato creato con successo.',
        ],
        'suspended' => [
            'title' => 'Account di hosting sospeso',
            'content' => 'Il tuo account di hosting per :domain è stato sospeso.',
        ],
        'reactivated' => [
            'title' => 'Account di hosting riattivato',
            'content' => 'Il tuo account di hosting per :domain è stato riattivato.',
        ],
        'password_changed' => [
            'title' => 'Password di hosting modificata',
            'content' => 'La password per il tuo account di hosting :domain è stata modificata.',
        ],
        'label_changed' => [
            'title' => 'Etichetta di hosting aggiornata',
            'content' => 'L\'etichetta per il tuo account di hosting :domain è stata aggiornata a ":label".',
        ],
    ],
    
    // Notifiche di ticket
    'ticket' => [
        'created' => [
            'title' => 'Ticket creato',
            'content' => 'Il tuo ticket #:ticket_id è stato creato con successo.',
        ],
        'replied' => [
            'title' => 'Nuova risposta al tuo ticket',
            'content' => 'Il membro dello staff :staff_name ha risposto al tuo ticket #:ticket_id.',
        ],
        'status_changed' => [
            'title' => 'Stato del ticket modificato',
            'content' => 'Lo stato del tuo ticket #:ticket_id è stato cambiato in :status.',
        ],
        'closed' => [
            'title' => 'Ticket chiuso',
            'content' => 'Il tuo ticket #:ticket_id è stato chiuso.',
        ],
    ],
    
    // Notifiche SSL
    'ssl' => [
        'created' => [
            'title' => 'Certificato SSL creato',
            'content' => 'Il tuo certificato SSL per :domain è stato creato con successo.',
        ],
        'activated' => [
            'title' => 'Certificato SSL attivato',
            'content' => 'Il tuo certificato SSL per :domain è stato attivato con successo.',
        ],
        'revoked' => [
            'title' => 'Certificato SSL revocato',
            'content' => 'Il tuo certificato SSL per :domain è stato revocato.',
        ],
    ],
    
    // Notifiche dell'account
    'account' => [
        '2fa_enabled' => [
            'title' => 'Autenticazione a due fattori abilitata',
            'content' => 'L\'autenticazione a due fattori è stata abilitata per il tuo account.',
        ],
        '2fa_disabled' => [
            'title' => 'Autenticazione a due fattori disabilitata',
            'content' => 'L\'autenticazione a due fattori è stata disabilitata per il tuo account.',
        ],
        'password_changed' => [
            'title' => 'Password modificata',
            'content' => 'La password del tuo account è stata modificata con successo.',
        ],
        'profile_updated' => [
            'title' => 'Profilo aggiornato',
            'content' => 'Le informazioni del tuo profilo sono state aggiornate con successo.',
        ],
    ],
];