<div class="dropdown d-inline-block">
    <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i data-feather="bell" class="icon-lg"></i>
        <span class="badge bg-danger rounded-pill notification-count" style="display: none;">0</span>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
        <div class="p-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0">Notifications</h6>
                </div>
                <div class="col-auto">
                    <a href="javascript:void(0);" class="small text-reset text-decoration-underline mark-all-read">
                        Mark all as read
                    </a>
                </div>
            </div>
        </div>
        <div data-simplebar style="max-height: 230px;" class="notification-list">
            <div class="text-center p-4 notifications-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Loading notifications...</p>
            </div>
            
            <div class="notifications-empty text-center p-4" style="display: none;">
                <i class="bx bx-bell-off font-size-24 text-muted mb-2"></i>
                <p class="mb-0">No notifications yet</p>
            </div>
        </div>
        <div class="p-2 border-top d-grid">
            <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('notifications.index') }}">
                <i class="mdi mdi-arrow-right-circle me-1"></i> <span>View All</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationCount = document.querySelector('.notification-count');
    const notificationList = document.querySelector('.notification-list');
    const notificationsLoading = document.querySelector('.notifications-loading');
    const notificationsEmpty = document.querySelector('.notifications-empty');
    const markAllReadButton = document.querySelector('.mark-all-read');
    
    // Function to load notifications
    function loadNotifications() {
        fetch('{{ route("notifications.get-recent") }}')
            .then(response => response.json())
            .then(data => {
                notificationsLoading.style.display = 'none';
                
                // Update unread count
                if (data.unread_count > 0) {
                    notificationCount.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    notificationCount.style.display = 'block';
                } else {
                    notificationCount.style.display = 'none';
                }
                
                // Display notifications or empty state
                if (data.notifications.length > 0) {
                    notificationsEmpty.style.display = 'none';
                    
                    // Clear existing notifications (except loading and empty states)
                    const existingItems = notificationList.querySelectorAll('.notification-item');
                    existingItems.forEach(item => item.remove());
                    
                    // Add notifications
                    data.notifications.forEach(notification => {
                        const notificationItem = document.createElement('a');
                        notificationItem.href = notification.action_url ?? 'javascript:void(0);';
                        notificationItem.className = 'text-reset notification-item';
                        notificationItem.dataset.id = notification.id;
                        
                        if (!notification.is_read) {
                            notificationItem.classList.add('bg-light-subtle');
                        }
                        
                        let avatar;
                        if (notification.image) {
                            avatar = `<img src="${notification.image}" class="rounded-circle avatar-sm" alt="user-pic">`;
                        } else {
                            avatar = `<span class="avatar-title ${notification.color_class} rounded-circle font-size-16">
                                <i class="${notification.icon_class}"></i>
                            </span>`;
                        }
                        
                        notificationItem.innerHTML = `
                            <div class="d-flex">
                                <div class="flex-shrink-0 avatar-sm me-3">
                                    ${avatar}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${notification.title}</h6>
                                    <div class="font-size-13 text-muted">
                                        <p class="mb-1">${notification.content}</p>
                                        <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>${notification.time_ago}</span></p>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Add click handler to mark as read
                        notificationItem.addEventListener('click', function(e) {
                            if (!notification.is_read) {
                                e.preventDefault();
                                markAsRead(notification.id, notification.action_url);
                            }
                        });
                        
                        notificationList.appendChild(notificationItem);
                    });
                } else {
                    notificationsEmpty.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationsLoading.style.display = 'none';
                notificationsEmpty.style.display = 'block';
                notificationsEmpty.querySelector('p').textContent = 'Failed to load notifications';
            });
    }
    
    // Function to mark a notification as read
    function markAsRead(id, redirectUrl = null) {
        fetch(`{{ url('notifications') }}/${id}/mark-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
                if (notificationItem) {
                    notificationItem.classList.remove('bg-light-subtle');
                }
                
                // Redirect if necessary
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
                
                // Reload notifications
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    // Function to mark all notifications as read
    function markAllAsRead() {
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload notifications
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }
    
    // Event listeners
    document.getElementById('page-header-notifications-dropdown').addEventListener('click', function() {
        loadNotifications();
    });
    
    markAllReadButton.addEventListener('click', function(e) {
        e.preventDefault();
        markAllAsRead();
    });
    
    // Initial load of unread count
    fetch('{{ route("notifications.get-unread-count") }}')
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                notificationCount.textContent = data.count > 99 ? '99+' : data.count;
                notificationCount.style.display = 'block';
            } else {
                notificationCount.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading notification count:', error);
        });
    
    // Reload unread count every minute
    setInterval(function() {
        fetch('{{ route("notifications.get-unread-count") }}')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    notificationCount.textContent = data.count > 99 ? '99+' : data.count;
                    notificationCount.style.display = 'block';
                } else {
                    notificationCount.style.display = 'none';
                }
            });
    }, 60000);
});
</script>
@endpush