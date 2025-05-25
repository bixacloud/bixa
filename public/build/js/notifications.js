/**
 * Notification System
 * For use with Laravel 11+ applications
 */

class NotificationSystem {
    constructor(options = {}) {
        // Default configuration
        this.config = {
            rootSelector: '#float-notification-btn',
            panelSelector: '#notification-panel',
            closeSelector: '#close-notification-btn',
            countSelector: '#float-notification-count',
            listSelector: '#float-notifications-list',
            loadingSelector: '#float-notifications-loading',
            emptySelector: '#float-notifications-empty',
            errorSelector: '#float-notifications-error',
            retrySelector: '#float-retry-btn',
            markAllReadSelector: '#mark-all-read-btn',
            overlaySelector: '#notification-overlay',
            routes: {
                unreadCount: '/notifications/unread-count',
                recent: '/notifications/recent',
                markAsRead: '/notifications',
                markAllRead: '/notifications/mark-all-read',
                refreshLocale: '/notifications/refresh-locale'
            },
            checkInterval: 60000, // 1 minute
            animationDuration: 300,
            ...options
        };

        // State variables
        this.state = {
            isLoading: false,
            isOpen: false,
            clickDisabled: false,
            currentLocale: document.body.dataset.lang || 'en',
            isMobile: window.innerWidth <= 768
        };

        // DOM elements
        this.elements = {};
        
        // Initialize component
        this.init();
    }

    /**
     * Initialize the notification system
     */
    init() {
        // Get DOM elements
        this.getElements();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Load initial unread count
        this.loadUnreadCount();
        
        // Set up interval to check for new notifications
        this.startPolling();
        
        // Listen for language changes globally
        this.listenForLanguageChanges();
    }

    /**
     * Get all required DOM elements
     */
    getElements() {
        const selectors = [
            'rootSelector', 
            'panelSelector', 
            'closeSelector', 
            'countSelector', 
            'listSelector',
            'loadingSelector', 
            'emptySelector', 
            'errorSelector', 
            'retrySelector',
            'markAllReadSelector', 
            'overlaySelector'
        ];
        
        selectors.forEach(selector => {
            const element = document.querySelector(this.config[selector]);
            if (element) {
                // Convert camelCase to simple name (e.g., rootSelector -> root)
                const name = selector.replace('Selector', '');
                this.elements[name] = element;
            }
        });
    }

    /**
     * Set up all event listeners
     */
    setupEventListeners() {
        // Toggle panel
        if (this.elements.root) {
            if (this.state.isMobile) {
                this.elements.root.addEventListener('touchstart', this.togglePanel.bind(this), { passive: false });
            } else {
                this.elements.root.addEventListener('click', this.togglePanel.bind(this));
            }
        }
        
        // Close panel
        if (this.elements.close) {
            if (this.state.isMobile) {
                this.elements.close.addEventListener('touchstart', this.togglePanel.bind(this), { passive: false });
            } else {
                this.elements.close.addEventListener('click', this.togglePanel.bind(this));
            }
        }
        
        // Retry loading
        if (this.elements.retry) {
            this.elements.retry.addEventListener('click', this.loadNotifications.bind(this));
        }
        
        // Mark all as read
        if (this.elements.markAllRead) {
            this.elements.markAllRead.addEventListener('click', this.markAllAsRead.bind(this));
        }
        
        // Overlay click
        if (this.elements.overlay) {
            this.elements.overlay.addEventListener('click', this.togglePanel.bind(this));
        }
        
        // Prevent panel event bubbling
        if (this.elements.panel) {
            if (this.state.isMobile) {
                this.elements.panel.addEventListener('touchstart', this.preventBubble.bind(this), { passive: false });
            } else {
                this.elements.panel.addEventListener('click', this.preventBubble.bind(this));
            }
        }
        
        // Handle window resize
        window.addEventListener('resize', this.handleResize.bind(this));
    }

    /**
     * Toggle notification panel visibility
     */
    togglePanel(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        // Prevent double-click issues
        if (this.state.clickDisabled) return;
        
        this.state.clickDisabled = true;
        setTimeout(() => {
            this.state.clickDisabled = false;
        }, 500);
        
        if (this.state.isOpen) {
            // Close panel
            this.elements.panel.classList.remove('show');
            if (this.elements.overlay) {
                this.elements.overlay.classList.remove('show');
            }
            
            // Set timeout to match transition duration
            setTimeout(() => {
                this.elements.panel.style.display = 'none';
            }, this.config.animationDuration);
        } else {
            // Open panel
            this.elements.panel.style.display = 'flex';
            if (this.elements.overlay) {
                this.elements.overlay.classList.add('show');
            }
            
            // Small delay to ensure display property is applied
            setTimeout(() => {
                this.elements.panel.classList.add('show');
            }, 10);
            
            // Load notifications
            this.loadNotifications();
        }
        
        this.state.isOpen = !this.state.isOpen;
    }

    /**
     * Load unread notification count
     */
    loadUnreadCount() {
        fetch(this.config.routes.unreadCount)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                this.updateBadge(data.count || 0);
            })
            .catch(error => {
                console.error('Error loading unread count:', error);
            });
    }

    /**
     * Load notifications with timeout
     */
    loadNotifications() {
        if (this.state.isLoading) return;
        
        this.showLoading();
        
        // Set timeout to prevent infinite loading
        const timeoutId = setTimeout(() => {
            if (this.state.isLoading) {
                this.showError();
                console.error('Request timeout after 15 seconds');
            }
        }, 15000);
        
        fetch(this.config.routes.recent)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                clearTimeout(timeoutId);
                
                // Update badge
                this.updateBadge(data.unread_count || 0);
                
                // Show notifications or empty state
                if (data.notifications && data.notifications.length > 0) {
                    this.renderNotifications(data.notifications);
                    if (this.elements.loading) {
                        this.elements.loading.style.display = 'none';
                    }
                } else {
                    this.showEmpty();
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('Error loading notifications:', error);
                this.showError();
            });
    }

    /**
     * Mark notification as read
     */
    markAsRead(id, redirectUrl = null) {
        if (!id) return;
        
        fetch(`${this.config.routes.markAsRead}/${id}/mark-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                this.loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    /**
     * Mark all notifications as read
     */
    markAllAsRead(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        fetch(this.config.routes.markAllRead, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            this.loadNotifications();
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }

    /**
     * Update badge count
     */
    updateBadge(count) {
        if (!this.elements.count) return;
        
        if (count > 0) {
            this.elements.count.textContent = count > 99 ? '99+' : count;
            this.elements.count.style.display = 'flex';
        } else {
            this.elements.count.style.display = 'none';
        }
    }

    /**
     * Show loading state
     */
    showLoading() {
        if (!this.elements.loading) return;
        
        this.elements.loading.style.display = 'block';
        if (this.elements.empty) this.elements.empty.style.display = 'none';
        if (this.elements.error) this.elements.error.style.display = 'none';
        if (this.elements.list) this.elements.list.innerHTML = '';
        this.state.isLoading = true;
    }

    /**
     * Show empty state
     */
    showEmpty() {
        if (!this.elements.empty) return;
        
        if (this.elements.loading) this.elements.loading.style.display = 'none';
        this.elements.empty.style.display = 'block';
        if (this.elements.error) this.elements.error.style.display = 'none';
        this.state.isLoading = false;
    }

    /**
     * Show error state
     */
    showError() {
        if (!this.elements.error) return;
        
        if (this.elements.loading) this.elements.loading.style.display = 'none';
        if (this.elements.empty) this.elements.empty.style.display = 'none';
        this.elements.error.style.display = 'block';
        this.state.isLoading = false;
    }

    /**
     * Render notifications
     */
    renderNotifications(notifications) {
        if (!this.elements.list) return;
        
        this.elements.list.innerHTML = '';
        
        notifications.forEach(notification => {
            if (!notification || !notification.id) return;
            
            const item = document.createElement('a');
            item.href = notification.action_url || '#';
            item.className = 'notification-item';
            item.dataset.id = notification.id;
            
            if (!notification.is_read) {
                item.classList.add('unread');
            }
            
            let avatarHtml = '';
            if (notification.image) {
                avatarHtml = `<img src="${notification.image}" class="avatar">`;
            } else {
                const iconClass = notification.icon_class || 'bx bx-bell';
                const bgColor = this.getColorByType(notification.type);
                avatarHtml = `<div class="avatar" style="background-color: ${bgColor}"><i class="${iconClass}"></i></div>`;
            }
            
            // Use localized attributes
            const title = notification.localized_title || notification.title || '';
            const content = notification.localized_content || notification.content || '';
            const actionText = notification.localized_action_text || notification.action_text || '';
            
            item.innerHTML = `
                <div class="notification-content">
                    ${avatarHtml}
                    <div class="details">
                        <div class="title">${title}</div>
                        <div class="message">${content}</div>
                        <div class="time">${notification.time_ago || 'Just now'}</div>
                    </div>
                </div>
            `;
            
            // Improved click handler for unread notifications
            if (!notification.is_read) {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.markAsRead(notification.id, notification.action_url);
                });
            }
            
            this.elements.list.appendChild(item);
        });
    }

    /**
     * Get color by notification type
     */
    getColorByType(type) {
        const colors = {
            login: 'var(--bs-primary, #556ee6)',
            hosting: 'var(--bs-success, #34c38f)',
            ticket: 'var(--bs-info, #50a5f1)',
            ssl: 'var(--bs-warning, #f1b44c)',
            account: 'var(--bs-secondary, #74788d)'
        };
        
        return colors[type] || colors.login;
    }

    /**
     * Prevent event bubbling
     */
    preventBubble(e) {
        e.stopPropagation();
    }

    /**
     * Handle window resize
     */
    handleResize() {
        this.state.isMobile = window.innerWidth <= 768;
    }

    /**
     * Start polling for new notifications
     */
    startPolling() {
        setInterval(() => {
            this.loadUnreadCount();
        }, this.config.checkInterval);
    }

    /**
     * Refresh notifications when language changes
     */
    refreshNotificationsLocale(locale) {
        if (!locale || locale === this.state.currentLocale) return;
        
        fetch(this.config.routes.refreshLocale, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ locale: locale })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success && this.state.isOpen) {
                this.loadNotifications(); // Reload with new locale
            }
            this.state.currentLocale = locale;
        })
        .catch(error => {
            console.error('Error refreshing notification locale:', error);
        });
    }

    /**
     * Listen for language change events
     */
    listenForLanguageChanges() {
        window.addEventListener('languageChanged', (e) => {
            if (e.detail && e.detail.locale) {
                this.refreshNotificationsLocale(e.detail.locale);
            }
        });
        
        // Add click listeners to language switchers
        document.querySelectorAll('.language-switcher').forEach(switcher => {
            switcher.addEventListener('click', (e) => {
                const locale = switcher.dataset.locale;
                if (locale) {
                    // Dispatch event for notification system
                    window.dispatchEvent(new CustomEvent('languageChanged', {
                        detail: { locale: locale }
                    }));
                }
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the notification system
    window.notificationSystem = new NotificationSystem({
        // Custom options can be passed here
    });
});