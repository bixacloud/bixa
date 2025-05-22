@extends('layouts.master')

@section('title') Notification Settings @endsection

@section('css')
<link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('/assets/libs/spectrum-colorpicker/spectrum-colorpicker.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('/assets/libs/datepicker/datepicker.min.css') }}">
<style>
    .settings-section {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }
    .settings-section h4 {
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .settings-info {
        background-color: #f8f9fa;
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .maintenance-btn {
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Admin @endslot
@slot('title') Notification Settings @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Notification System Configuration</h4>
                
                <div class="settings-info">
                    <p>Configure how the notification system functions. These settings affect performance, storage usage, and user experience.</p>
                </div>
                
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                
                <form action="{{ route('admin.notifications.settings.update') }}" method="POST">
                    @csrf
                    @method('POST')
                    
                    <div class="settings-section">
                        <h4>Cleanup Settings</h4>
                        <p>Configure how old notifications are automatically removed from the system.</p>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cleanup_days" class="form-label">Days to Keep Notifications</label>
                                    <input type="number" class="form-control" id="cleanup_days" name="cleanup_days" 
                                           min="1" max="365" value="{{ $settings['cleanup_days'] ?? 30 }}" required>
                                    <div class="form-text">Notifications older than this many days will be deleted automatically.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cleanup_probability" class="form-label">Cleanup Probability (%)</label>
                                    <input type="number" class="form-control" id="cleanup_probability" name="cleanup_probability" 
                                           min="1" max="100" value="{{ $settings['cleanup_probability'] ?? 5 }}" required>
                                    <div class="form-text">Percentage chance of cleanup running on each request. Lower values reduce performance impact.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="cleanup_batch_size" class="form-label">Cleanup Batch Size</label>
                                    <input type="number" class="form-control" id="cleanup_batch_size" name="cleanup_batch_size" 
                                           min="10" max="1000" value="{{ $settings['cleanup_batch_size'] ?? 100 }}" required>
                                    <div class="form-text">Maximum number of notifications to delete in one batch.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="maintenance-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="run_cleanup" id="run_cleanup">
                                <label class="form-check-label" for="run_cleanup">
                                    Run cleanup now (removes old notifications based on settings above)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h4>User Interface Settings</h4>
                        <p>Configure how notifications appear to users.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="floating_panel_limit" class="form-label">Floating Panel Limit</label>
                                    <input type="number" class="form-control" id="floating_panel_limit" name="floating_panel_limit" 
                                           min="1" max="20" value="{{ $settings['floating_panel_limit'] ?? 5 }}" required>
                                    <div class="form-text">Number of notifications to show in the floating notification panel.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="check_interval" class="form-label">Check Interval (milliseconds)</label>
                                    <input type="number" class="form-control" id="check_interval" name="check_interval" 
                                           min="10000" max="600000" step="10000" value="{{ $settings['check_interval'] ?? 60000 }}" required>
                                    <div class="form-text">How often to check for new notifications. 60000 = 1 minute.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-section">
                        <h4>Notification Type Settings</h4>
                        <p>The following notification types are configured in the system:</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Icon</th>
                                        <th>Color</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings['types'] ?? [] as $type => $config)
                                    <tr>
                                        <td>{{ ucfirst($type) }}</td>
                                        <td><i class="{{ $config['icon'] ?? 'bx bx-bell' }}"></i> {{ $config['icon'] ?? 'bx bx-bell' }}</td>
                                        <td><span class="badge bg-{{ $config['color'] ?? 'primary' }}">{{ $config['color'] ?? 'primary' }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="form-text mt-2">Notification type settings are defined in the config file. If you need to change these, please contact your developer.</div>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/spectrum-colorpicker/spectrum-colorpicker.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/datepicker/datepicker.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/pages/form-advanced.init.js') }}"></script>
@endsection