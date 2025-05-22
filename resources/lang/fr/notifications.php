<?php

/**
 * Fichier de langue français pour les notifications
 */

return [
    // Étiquettes communes et boutons
    'all' => 'Toutes les notifications',
    'mark_all_read' => 'Tout marquer comme lu',
    'view_all' => 'Voir tout',
    'no_notifications' => 'Pas encore de notifications',
    'no_notifications_message' => 'Vous verrez vos notifications ici quand elles arriveront.',
    'notifications' => 'Notifications',
    'loading' => 'Chargement...',
    'retry' => 'Réessayer',
    
    // Catégories
    'categories' => [
        'all' => 'Toutes',
        'login' => 'Connexion',
        'hosting' => 'Hébergement',
        'tickets' => 'Tickets',
        'ssl' => 'SSL',
        'account' => 'Compte',
    ],
    
    // Boutons d'action
    'actions' => [
        'view' => 'Voir',
        'view_login_history' => 'Voir l\'historique de connexion',
        'view_account' => 'Voir le compte',
        'view_ticket' => 'Voir le ticket',
        'view_certificate' => 'Voir le certificat',
        'view_profile' => 'Voir le profil',
    ],
    
    // États vides
    'empty_states' => [
        'login' => 'Aucune notification de connexion',
        'login_message' => 'Les notifications de connexion apparaîtront ici.',
        'hosting' => 'Aucune notification d\'hébergement',
        'hosting_message' => 'Les notifications d\'hébergement apparaîtront ici.',
        'ticket' => 'Aucune notification de ticket',
        'ticket_message' => 'Les notifications de ticket apparaîtront ici.',
        'ssl' => 'Aucune notification SSL',
        'ssl_message' => 'Les notifications de certificat SSL apparaîtront ici.',
        'account' => 'Aucune notification de compte',
        'account_message' => 'Les notifications liées au compte apparaîtront ici.',
    ],
    
    // Messages d'erreur
    'errors' => [
        'failed_to_get_count' => 'Échec de la récupération du nombre de notifications non lues',
        'failed_to_get_recent' => 'Échec de la récupération des notifications récentes',
        'failed_to_mark_read' => 'Échec du marquage de la notification comme lue',
        'failed_to_mark_all_read' => 'Échec du marquage de toutes les notifications comme lues',
        'failed_to_load' => 'Échec du chargement des notifications',
    ],
    
    // Messages de succès
    'messages' => [
        'marked_as_read' => 'Notification marquée comme lue',
        'all_marked_as_read' => 'Toutes les notifications ont été marquées comme lues',
    ],
    
    // Étiquettes de temps
    'time' => [
        'just_now' => 'À l\'instant',
        'seconds_ago' => 'il y a :count secondes',
        'minute_ago' => 'il y a 1 minute',
        'minutes_ago' => 'il y a :count minutes',
        'hour_ago' => 'il y a 1 heure',
        'hours_ago' => 'il y a :count heures',
        'day_ago' => 'il y a 1 jour',
        'days_ago' => 'il y a :count jours',
        'week_ago' => 'il y a 1 semaine',
        'weeks_ago' => 'il y a :count semaines',
    ],
    
    // Notifications de connexion
    'login' => [
        'title' => 'Connexion réussie',
        'content' => 'Vous vous êtes connecté avec succès depuis l\'adresse IP :ip',
        'content_with_location' => 'Vous vous êtes connecté avec succès depuis l\'adresse IP :ip à :location',
        'content_with_location_and_device' => 'Vous vous êtes connecté avec succès depuis l\'adresse IP :ip à :location en utilisant :browser sur :platform (:device_type)',
    ],
    
    // Notifications d'hébergement
    'hosting' => [
        'created' => [
            'title' => 'Compte d\'hébergement créé',
            'content' => 'Votre compte d\'hébergement pour :domain a été créé avec succès.',
        ],
        'suspended' => [
            'title' => 'Compte d\'hébergement suspendu',
            'content' => 'Votre compte d\'hébergement pour :domain a été suspendu.',
        ],
        'reactivated' => [
            'title' => 'Compte d\'hébergement réactivé',
            'content' => 'Votre compte d\'hébergement pour :domain a été réactivé.',
        ],
        'password_changed' => [
            'title' => 'Mot de passe d\'hébergement modifié',
            'content' => 'Le mot de passe de votre compte d\'hébergement :domain a été modifié.',
        ],
        'label_changed' => [
            'title' => 'Étiquette d\'hébergement mise à jour',
            'content' => 'L\'étiquette de votre compte d\'hébergement :domain a été mise à jour en ":label".',
        ],
    ],
    
    // Notifications de ticket
    'ticket' => [
        'created' => [
            'title' => 'Ticket créé',
            'content' => 'Votre ticket #:ticket_id a été créé avec succès.',
        ],
        'replied' => [
            'title' => 'Nouvelle réponse à votre ticket',
            'content' => 'Le membre du personnel :staff_name a répondu à votre ticket #:ticket_id.',
        ],
        'status_changed' => [
            'title' => 'Statut de ticket modifié',
            'content' => 'Le statut de votre ticket #:ticket_id a été changé en :status.',
        ],
        'closed' => [
            'title' => 'Ticket fermé',
            'content' => 'Votre ticket #:ticket_id a été fermé.',
        ],
    ],
    
    // Notifications SSL
    'ssl' => [
        'created' => [
            'title' => 'Certificat SSL créé',
            'content' => 'Votre certificat SSL pour :domain a été créé avec succès.',
        ],
        'activated' => [
            'title' => 'Certificat SSL activé',
            'content' => 'Votre certificat SSL pour :domain a été activé avec succès.',
        ],
        'revoked' => [
            'title' => 'Certificat SSL révoqué',
            'content' => 'Votre certificat SSL pour :domain a été révoqué.',
        ],
    ],
    
    // Notifications de compte
    'account' => [
        '2fa_enabled' => [
            'title' => 'Authentification à deux facteurs activée',
            'content' => 'L\'authentification à deux facteurs a été activée pour votre compte.',
        ],
        '2fa_disabled' => [
            'title' => 'Authentification à deux facteurs désactivée',
            'content' => 'L\'authentification à deux facteurs a été désactivée pour votre compte.',
        ],
        'password_changed' => [
            'title' => 'Mot de passe modifié',
            'content' => 'Le mot de passe de votre compte a été modifié avec succès.',
        ],
        'profile_updated' => [
            'title' => 'Profil mis à jour',
            'content' => 'Les informations de votre profil ont été mises à jour avec succès.',
        ],
    ],
];