{{-- Account pending --}}
@if($account->status === 'pending')
    @if($account->created_at->addHour()->isFuture())
        <div class="alert alert-info" role="alert">
            <i class="mdi mdi-information-outline"></i>
            Please note that it may take up to 1 hour for your account to be fully activated.
        </div>
    @endif

    <div class="alert alert-info">
        <i class="mdi mdi-information-outline"></i>
        Your account is pending activation. Please wait.
    </div>
@endif

{{-- Account reactivating --}}
@if($account->status === 'reactivating')
    <div class="alert alert-warning">
        <i class="mdi mdi-alert-outline"></i>
        Your account is being reactivated. This may take a few minutes.
    </div>
@endif

{{-- Account deactivating --}}
@if($account->status === 'deactivating')
    <div class="alert alert-warning">
        <i class="mdi mdi-alert-outline"></i>
        Your account is being deactivated. This may take a few minutes.
    </div>
@endif

{{-- Account suspended --}}
@if($account->status === 'suspended')
    <div class="alert alert-danger">
        <i class="mdi mdi-alert-circle-outline"></i>
        Your account has been suspended. Please contact support for assistance.
    </div>
@endif

{{-- Account deactivated --}}
@if($account->status === 'deactivated')
    <div class="alert alert-info">
        <i class="mdi mdi-information-outline"></i>
        Your account has been deactivated. You can reactivate it any time.
    </div>
@endif