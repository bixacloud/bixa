@php
    // Only get active popups that the user hasn't dismissed (if they are "show once" type)
    $activePopup = null;
    
    if (auth()->check()) {
        $popupQuery = \App\Models\PopupNotification::active();
        
        // Get first active popup that hasn't been dismissed by the user (if it's a show-once popup)
        $popupQuery->whereDoesntHave('dismissedByUsers', function ($query) {
            $query->where('user_id', auth()->id());
        })->orWhere('show_once', false);
        
        $activePopup = $popupQuery->first();
    }
@endphp

@if(auth()->check() && $activePopup)
<!-- Popup Notification Modal -->
<div class="modal fade" id="popupNotificationModal" tabindex="-1" aria-labelledby="popupNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-{{ $activePopup->type }}">
                <h5 class="modal-title text-white" id="popupNotificationModalLabel">{{ $activePopup->title }}</h5>
                @if($activePopup->allow_dismiss)
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                @endif
            </div>
            <div class="modal-body">
                {!! $activePopup->content !!}
            </div>
            <div class="modal-footer">
                @if($activePopup->allow_dismiss && $activePopup->show_once)
                    <div class="form-check me-auto">
                        <input class="form-check-input" type="checkbox" id="dontShowAgain">
                        <label class="form-check-label" for="dontShowAgain">
                            Don't show again
                        </label>
                    </div>
                @endif
                
                <button type="button" class="btn btn-{{ $activePopup->type }}" data-bs-dismiss="modal">
                    @if($activePopup->allow_dismiss)
                        Close
                    @else
                        Acknowledge
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show the popup modal
    const popupModal = new bootstrap.Modal(document.getElementById('popupNotificationModal'));
    popupModal.show();
    
    // Handle "Don't show again" checkbox if exists
    const dontShowCheckbox = document.getElementById('dontShowAgain');
    const popupId = {{ $activePopup->id }};
    
    if (dontShowCheckbox) {
        document.getElementById('popupNotificationModal').addEventListener('hidden.bs.modal', function () {
            if (dontShowCheckbox.checked) {
                fetch('{{ route("notifications.dismiss-popup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        popup_id: popupId
                    })
                });
            }
        });
    }
});
</script>
@endif