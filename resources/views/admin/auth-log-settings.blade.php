@extends('layouts.master')

@section('title') Authentication Log Settings @endsection

@section('css')
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
    }
    .status-badge.bg-success {
        background-color: #34c38f;
        color: #fff;
    }
    .status-badge.bg-warning {
        background-color: #f1b44c;
        color: #fff;
    }
    .status-badge.bg-danger {
        background-color: #f46a6a;
        color: #fff;
    }
    .nav-tabs .nav-link.active {
        font-weight: 600;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') 
            <a href="{{ route('admin.authentication-logs') }}">Login History</a>
        @endslot
        @slot('title') Settings @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                        <h4 class="card-title me-2">Authentication Log Settings</h4>
                        <div class="ms-auto">
                            <a href="{{ route('admin.authentication-logs') }}" class="btn btn-soft-secondary">
                                <i class="mdi mdi-arrow-left font-size-16 align-middle me-1"></i> Back to Logs
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#settings" role="tab">
                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                <span class="d-none d-sm-block">General Settings</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#geoip" role="tab">
                                <span class="d-block d-sm-none"><i class="fas fa-map-marker-alt"></i></span>
                                <span class="d-none d-sm-block">
                                    GeoIP Database
                                    @if(isset($geoipInfo) && $geoipInfo['needsUpdate'])
                                        <span class="badge rounded-pill bg-warning">Needs Update</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content p-3 text-muted">
                        <div class="tab-pane active" id="settings" role="tabpanel">
                            <form action="{{ route('admin.auth-log-settings.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card border mb-4">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Email Notifications</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="new_device_notification" 
                                                            name="new_device_notification" {{ $settings->new_device_notification ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="new_device_notification">
                                                            Send email notification for new device login
                                                        </label>
                                                    </div>
                                                    <div class="form-text text-muted">
                                                        When enabled, users will receive an email notification when they log in from a new device or browser.
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="failed_login_notification" 
                                                            name="failed_login_notification" {{ $settings->failed_login_notification ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="failed_login_notification">
                                                            Send email notification for failed login attempts
                                                        </label>
                                                    </div>
                                                    <div class="form-text text-muted">
                                                        When enabled, users will receive an email notification when there's a failed login attempt on their account.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card border mb-4">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Location and Language Detection</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="location_tracking" 
                                                            name="location_tracking" {{ $settings->location_tracking ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="location_tracking">
                                                            Enable location tracking
                                                        </label>
                                                    </div>
                                                    <div class="form-text text-muted">
                                                        When enabled, the system will determine the geographic location of login attempts using the GeoIP2 database.
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="language_detection" 
                                                            name="language_detection" {{ $settings->language_detection ?? true ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="language_detection">
                                                            Enable automatic language detection
                                                        </label>
                                                    </div>
                                                    <div class="form-text text-muted">
                                                        When enabled, the system will automatically detect and set the user's preferred language based on their location.
                                                        This setting requires location tracking to be enabled.
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label for="geoip_update_frequency" class="form-label">GeoIP Database Update Frequency (days)</label>
                                                    <input type="number" class="form-control" id="geoip_update_frequency" name="geoip_update_frequency" 
                                                        value="{{ $settings->geoip_update_frequency ?? 30 }}" min="1" max="90">
                                                    <div class="form-text text-muted mt-2">
                                                        How often to update the GeoIP database. Recommended: 30 days. Lower values provide more accurate location data but require more frequent updates.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card border mb-4">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Data Storage & Retention</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="save_user_agent" 
                                                            name="save_user_agent" {{ $settings->save_user_agent ?? true ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="save_user_agent">
                                                            Save browser and device information
                                                        </label>
                                                    </div>
                                                    <div class="form-text text-muted">
                                                        When enabled, the system will store information about the browser and device used for login attempts.
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label for="retention_days" class="form-label">Data Retention Period (days)</label>
                                                    <input type="number" class="form-control" id="retention_days" name="retention_days" 
                                                        value="{{ $settings->retention_days ?? 90 }}" min="1" max="365">
                                                    <div class="form-text text-muted mt-2">
                                                        Number of days to keep authentication logs before automatic deletion. Range: 1-365 days.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 mb-5">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-content-save font-size-16 align-middle me-1"></i> Save Settings
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="card border mt-md-0 mt-4">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0">Information</h5>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text mb-4">
                                                    <b>Email Notifications:</b> Email notifications can consume your SMTP service quota. 
                                                    For high-traffic sites, consider enabling notifications only for failed login attempts.
                                                </p>
                                                <p class="card-text mb-4">
                                                    <b>Location Tracking:</b> Location tracking uses the GeoIP2 Lite database to determine location based on IP address.
                                                    The database needs to be updated periodically to maintain accuracy.
                                                </p>
                                                <p class="card-text mb-4">
                                                    <b>Language Detection:</b> Automatically sets the user's interface language based on
                                                    their geographic location. This improves user experience by displaying content in
                                                    their likely preferred language.
                                                </p>
                                                <p class="card-text">
                                                    <b>Data Retention:</b> Longer retention periods consume more database space.
                                                    Consider your database capacity when setting the retention period.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="tab-pane" id="geoip" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card border mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Database Status</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <th width="30%">Status</th>
                                                            <td>
                                                                @if($geoipInfo['databaseExists'])
                                                                    @if($geoipInfo['needsUpdate'])
                                                                        <span class="status-badge bg-warning">Needs Update</span>
                                                                    @else
                                                                        <span class="status-badge bg-success">Up to Date</span>
                                                                    @endif
                                                                @else
                                                                    <span class="status-badge bg-danger">Not Installed</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Last Updated</th>
                                                            <td>{{ $geoipInfo['lastUpdate'] ? $geoipInfo['lastUpdate']->format('Y-m-d H:i:s') : 'Never' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Next Update</th>
                                                            <td>{{ $geoipInfo['nextUpdate'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Database Size</th>
                                                            <td>{{ $geoipInfo['databaseSize'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Database Path</th>
                                                            <td>{{ $geoipInfo['path'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Update Frequency</th>
                                                            <td>Every {{ $settings->geoip_update_frequency }} days</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Update Database</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.auth-log-settings.update-geoip') }}" method="POST" id="geoip-update-form">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="license_key" class="form-label">MaxMind License Key (optional)</label>
                                                    <input type="text" class="form-control" id="license_key" name="license_key" 
                                                        placeholder="Enter your MaxMind license key if not configured in .env">
                                                    <div class="form-text mt-2">
                                                        <div id="license-key-status">
                                                            @if(config('geoip.license_key'))
                                                                <span class="text-success"><i class="mdi mdi-check-circle"></i> License key is configured in .env file</span>
                                                            @else
                                                                <span class="text-danger"><i class="mdi mdi-close-circle"></i> License key is not configured. Please enter your key above or add it to your .env file</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="alert alert-info">
                                                    <i class="mdi mdi-information-outline me-2"></i>
                                                    Updating the GeoIP database requires a free MaxMind GeoLite2 license key. 
                                                    <a href="https://dev.maxmind.com/geoip/geolite2-free-geolocation-data" target="_blank">
                                                        Get a free license key here
                                                    </a>.
                                                </div>

                                                <div class="mb-4">
                                                    <button type="submit" class="btn btn-primary" id="update-button">
                                                        <i class="mdi mdi-download font-size-16 align-middle me-1"></i> 
                                                        Update GeoIP Database
                                                    </button>
                                                    <div class="mt-3" id="update-progress" style="display: none;">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        <span class="ms-2">Updating database, please wait...</span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text mb-4">
                                                <b>GeoIP Database:</b> The GeoIP database is used to determine the geographical 
                                                location of an IP address. This is used for location tracking and language detection.
                                            </p>
                                            <p class="card-text mb-4">
                                                <b>MaxMind License:</b> You need a free MaxMind license key to download and update
                                                the GeoIP database. The license key can be configured in your .env file or entered manually.
                                            </p>
                                            <p class="card-text">
                                                <b>Update Frequency:</b> The GeoIP database should be updated regularly to ensure
                                                accurate location data. MaxMind recommends updating at least once per month.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Show progress indicator on GeoIP update form submit
        $('#geoip-update-form').on('submit', function() {
            $('#update-button').prop('disabled', true);
            $('#update-progress').show();
        });
        
        // Auto cleanup of old authentication logs - run once per day
        (function checkAndCleanupLogs() {
            // Function to check if we should run cleanup today
            function shouldRunCleanup() {
                const lastCleanup = localStorage.getItem('auth_log_last_cleanup');
                if (!lastCleanup) return true;
                
                const today = new Date().toDateString();
                return lastCleanup !== today;
            }
            
            // Only run cleanup once per day
            if (shouldRunCleanup()) {
                // Set last cleanup date
                localStorage.setItem('auth_log_last_cleanup', new Date().toDateString());
                
                // Run cleanup in background
                $.ajax({
                    url: '{{ route("admin.auth-log-settings.cleanup") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Authentication logs cleanup:', response.message);
                    },
                    error: function(error) {
                        console.error('Authentication logs cleanup error:', error);
                    }
                });
            }
        })();
    });
</script>

@if(session('trigger_geoip_update'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Perform GeoIP update in the background after page has loaded
        setTimeout(function() {
            fetch('{{ route("admin.auth-log-settings.background-update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                // Use keepalive to continue the request even if the page is closed
                keepalive: true
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('GeoIP update completed:', data);
            })
            .catch(error => {
                console.error('GeoIP update error:', error);
            });
        }, 2000);  // Wait 2 seconds to ensure page has loaded completely
    });
</script>
@php
    // Clear session flag after triggering update to prevent repeated updates
    session()->forget('trigger_geoip_update');
@endphp
@endif
@endsection
