@extends('layouts.master')

@section('title')
    Data Migration Tool
@endsection

@section('css')
    <style>
	h5{
	color:#fff !important;
	}
        .migration-step {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .status-pending {
            background-color: #f8f9fa;
            border-left: 4px solid #6c757d;
        }
        .status-in_progress {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .status-completed {
            background-color: #d1e7dd;
            border-left: 4px solid #198754;
        }
        .status-failed {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        
        /* Added CSS for text color and spacing */
        .card-header.bg-primary {
            color: white;
        }
        
        .gap-2 {
            gap: 0.5rem !important;
        }
        
        @media (max-width: 576px) {
            .d-flex.flex-column.flex-sm-row {
                width: 100%;
            }
            
            .d-flex.flex-column.flex-sm-row .btn {
                width: 100%;
                margin-bottom: 0.5rem;
                text-align: center;
            }
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Admin
        @endslot
        @slot('title')
            Data Migration Tool
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Data Migration Tool</h4>
                    <p class="card-text">
                        This tool helps you migrate data from your old database to the new one.
                        Make sure to back up your database before proceeding.
                    </p>

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

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Database Connection Form -->
                    @if($connectionStatus !== 'connected')
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card border">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Connect to Old Database</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.migration.connect') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="host" class="form-label">Database Host</label>
                                                <input type="text" class="form-control" id="host" name="host" value="{{ old('host', 'localhost') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="port" class="form-label">Database Port</label>
                                                <input type="number" class="form-control" id="port" name="port" value="{{ old('port', '3306') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="database" class="form-label">Database Name</label>
                                                <input type="text" class="form-control" id="database" name="database" value="{{ old('database') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Database Username</label>
                                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Database Password</label>
                                                <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Connect</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card border">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">Migration Steps</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i data-feather="info" class="me-2 icon-sm"></i>
                                            Connect to your old database first, then you can start the migration process.
                                        </div>
                                        <ol class="list-group list-group-numbered mb-3">
                                            <li class="list-group-item d-flex align-items-center">
                                                <div class="ms-2">
                                                    <div class="fw-bold">Connect to old database</div>
                                                    Establish connection to your old database system
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center">
                                                <div class="ms-2">
                                                    <div class="fw-bold">Migrate users</div>
                                                    Transfer users and admins to new system with random passwords
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center">
                                                <div class="ms-2">
                                                    <div class="fw-bold">Migrate hosting accounts</div>
                                                    Transfer hosting accounts to new system
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center">
                                                <div class="ms-2">
                                                    <div class="fw-bold">Migrate tickets</div>
                                                    Transfer support tickets and replies to new system
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center">
                                                <div class="ms-2">
                                                    <div class="fw-bold">Migrate SSL certificates</div>
                                                    Transfer SSL certificates to new system
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center">
                                                <div class="ms-2">
                                                    <div class="fw-bold">Migrate settings</div>
                                                    Transfer reCAPTCHA and other settings
                                                </div>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Migration Dashboard -->
                        <div class="mb-4">
                            <!-- Improved button spacing for better mobile responsiveness -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                                <h5 class="mb-3 mb-md-0">
                                    <i data-feather="database" class="icon-sm me-2 text-success"></i>
                                    Connected to Old Database
                                </h5>
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    <a href="{{ route('admin.migration.start') }}" class="btn btn-primary">
                                        <i data-feather="play" class="icon-sm me-1"></i>
                                        Start Migration
                                    </a>
                                    <a href="{{ route('admin.migration.disconnect') }}" class="btn btn-danger">
                                        <i data-feather="x-circle" class="icon-sm me-1"></i>
                                        Disconnect
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Migration Progress -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border">
                                    <!-- Added text-white to make text white on purple background -->
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Migration Progress</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Users -->
                                        <div class="migration-step status-{{ $migrationStatus['users'] }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0">1. Migrate Users</h5>
                                                <span class="badge bg-{{ 
                                                    $migrationStatus['users'] === 'pending' ? 'secondary' : 
                                                    ($migrationStatus['users'] === 'in_progress' ? 'warning' : 
                                                    ($migrationStatus['users'] === 'completed' ? 'success' : 'danger')) 
                                                }}">
                                                    {{ ucfirst($migrationStatus['users']) }}
                                                </span>
                                            </div>
                                            @if($migrationStatus['users'] !== 'pending')
                                                <div class="d-flex justify-content-between small">
                                                    <span>Total: {{ $migrationStats['users']['total'] }}</span>
                                                    <span>Migrated: {{ $migrationStats['users']['migrated'] }}</span>
                                                    <span>Failed: {{ $migrationStats['users']['failed'] }}</span>
                                                </div>
                                                <div class="progress mt-2">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                        style="width: {{ $migrationStats['users']['total'] > 0 ? ($migrationStats['users']['migrated'] / $migrationStats['users']['total'] * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Hosting Accounts -->
                                        <div class="migration-step status-{{ $migrationStatus['accounts'] }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0">2. Migrate Hosting Accounts</h5>
                                                <span class="badge bg-{{ 
                                                    $migrationStatus['accounts'] === 'pending' ? 'secondary' : 
                                                    ($migrationStatus['accounts'] === 'in_progress' ? 'warning' : 
                                                    ($migrationStatus['accounts'] === 'completed' ? 'success' : 'danger')) 
                                                }}">
                                                    {{ ucfirst($migrationStatus['accounts']) }}
                                                </span>
                                            </div>
                                            @if($migrationStatus['accounts'] !== 'pending')
                                                <div class="d-flex justify-content-between small">
                                                    <span>Total: {{ $migrationStats['accounts']['total'] }}</span>
                                                    <span>Migrated: {{ $migrationStats['accounts']['migrated'] }}</span>
                                                    <span>Failed: {{ $migrationStats['accounts']['failed'] }}</span>
                                                </div>
                                                <div class="progress mt-2">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                        style="width: {{ $migrationStats['accounts']['total'] > 0 ? ($migrationStats['accounts']['migrated'] / $migrationStats['accounts']['total'] * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Tickets -->
                                        <div class="migration-step status-{{ $migrationStatus['tickets'] }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0">3. Migrate Tickets</h5>
                                                <span class="badge bg-{{ 
                                                    $migrationStatus['tickets'] === 'pending' ? 'secondary' : 
                                                    ($migrationStatus['tickets'] === 'in_progress' ? 'warning' : 
                                                    ($migrationStatus['tickets'] === 'completed' ? 'success' : 'danger')) 
                                                }}">
                                                    {{ ucfirst($migrationStatus['tickets']) }}
                                                </span>
                                            </div>
                                            @if($migrationStatus['tickets'] !== 'pending')
                                                <div class="d-flex justify-content-between small">
                                                    <span>Total: {{ $migrationStats['tickets']['total'] }}</span>
                                                    <span>Migrated: {{ $migrationStats['tickets']['migrated'] }}</span>
                                                    <span>Failed: {{ $migrationStats['tickets']['failed'] }}</span>
                                                </div>
                                                <div class="progress mt-2">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                        style="width: {{ $migrationStats['tickets']['total'] > 0 ? ($migrationStats['tickets']['migrated'] / $migrationStats['tickets']['total'] * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <!-- Added text-white to make text white on purple background -->
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Migration Progress (Continued)</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- SSL Certificates -->
                                        <div class="migration-step status-{{ $migrationStatus['ssl'] }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0">4. Migrate SSL Certificates</h5>
                                                <span class="badge bg-{{ 
                                                    $migrationStatus['ssl'] === 'pending' ? 'secondary' : 
                                                    ($migrationStatus['ssl'] === 'in_progress' ? 'warning' : 
                                                    ($migrationStatus['ssl'] === 'completed' ? 'success' : 'danger')) 
                                                }}">
                                                    {{ ucfirst($migrationStatus['ssl']) }}
                                                </span>
                                            </div>
                                            @if($migrationStatus['ssl'] !== 'pending')
                                                <div class="d-flex justify-content-between small">
                                                    <span>Total: {{ $migrationStats['ssl']['total'] }}</span>
                                                    <span>Migrated: {{ $migrationStats['ssl']['migrated'] }}</span>
                                                    <span>Failed: {{ $migrationStats['ssl']['failed'] }}</span>
                                                </div>
                                                <div class="progress mt-2">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                        style="width: {{ $migrationStats['ssl']['total'] > 0 ? ($migrationStats['ssl']['migrated'] / $migrationStats['ssl']['total'] * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Settings -->
                                        <div class="migration-step status-{{ $migrationStatus['settings'] }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0">5. Migrate Settings</h5>
                                                <span class="badge bg-{{ 
                                                    $migrationStatus['settings'] === 'pending' ? 'secondary' : 
                                                    ($migrationStatus['settings'] === 'in_progress' ? 'warning' : 
                                                    ($migrationStatus['settings'] === 'completed' ? 'success' : 'danger')) 
                                                }}">
                                                    {{ ucfirst($migrationStatus['settings']) }}
                                                </span>
                                            </div>
                                            @if($migrationStatus['settings'] !== 'pending')
                                                <div class="d-flex justify-content-between small">
                                                    <span>Total: {{ $migrationStats['settings']['total'] }}</span>
                                                    <span>Migrated: {{ $migrationStats['settings']['migrated'] }}</span>
                                                    <span>Failed: {{ $migrationStats['settings']['failed'] }}</span>
                                                </div>
                                                <div class="progress mt-2">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                        style="width: {{ $migrationStats['settings']['total'] > 0 ? ($migrationStats['settings']['migrated'] / $migrationStats['settings']['total'] * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Management Section (Only shown after completed migration) -->
                        @if($passwordsMigrated && $migrationStatus['users'] === 'completed')
                            <div class="card border mt-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Password Management</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i data-feather="info" class="me-2 icon-sm"></i>
                                        <strong>Note:</strong> All user passwords were automatically set to random values during migration.
                                        You can now notify users of their new passwords via email or export the password list.
                                    </div>
                                    
                                    <!-- Password Email Form -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card border h-100">
                                                <div class="card-header bg-primary text-white">
                                                    <h5 class="mb-0">Send Password Emails</h5>
                                                </div>
                                                <div class="card-body">
                                                    <form action="{{ route('admin.migration.send-passwords') }}" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="email_subject" class="form-label">Email Subject</label>
                                                            <input type="text" class="form-control" id="email_subject" name="email_subject" 
                                                                   value="Important: Your New Account Password" required>
                                                        </div>
                                                        <p class="text-muted mb-3">
                                                            This will send an email to all migrated users with their new passwords.
                                                            The email will be sent using the configured SMTP settings.
                                                        </p>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i data-feather="mail" class="icon-sm me-1"></i>
                                                            Send Password Emails
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card border h-100">
                                                <div class="card-header bg-primary text-white">
                                                    <h5 class="mb-0">Export Password List</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p>Export a list of user emails and their new passwords:</p>
                                                    
                                                    <div class="d-grid gap-2">
                                                        <a href="{{ route('admin.migration.export-passwords') }}" class="btn btn-outline-primary mb-2">
                                                            <i data-feather="file-text" class="icon-sm me-1"></i>
                                                            Export Masked Passwords
                                                            <small class="d-block mt-1 text-muted">
                                                                (Partially hidden for security - e.g., "pa****rd")
                                                            </small>
                                                        </a>
                                                        
                                                        <a href="{{ route('admin.migration.export-full-passwords') }}" class="btn btn-outline-danger">
                                                            <i data-feather="shield" class="icon-sm me-1"></i>
                                                            Export Full Passwords
                                                            <small class="d-block mt-1 text-muted">
                                                                (Admin only - contains actual passwords)
                                                            </small>
                                                        </a>
                                                    </div>
                                                    
                                                    <div class="alert alert-warning mt-3 mb-0">
                                                        <i data-feather="alert-triangle" class="icon-sm me-1"></i>
                                                        <small>Keep exported password files secure and delete them after use.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Migration Summary -->
                        @if($migrationStatus['users'] === 'completed' && 
                            $migrationStatus['accounts'] === 'completed' && 
                            $migrationStatus['tickets'] === 'completed' && 
                            $migrationStatus['ssl'] === 'completed' && 
                            $migrationStatus['settings'] === 'completed')
                            <div class="card border mt-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Migration Completed</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-success">
                                        <i data-feather="check-circle" class="me-2 icon-sm"></i>
                                        Migration has been completed successfully.
                                    </div>
                                    
                                    <h5>Migration Summary</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Data Type</th>
                                                    <th>Total</th>
                                                    <th>Migrated</th>
                                                    <th>Failed</th>
                                                    <th>Success Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Users</td>
                                                    <td>{{ $migrationStats['users']['total'] }}</td>
                                                    <td>{{ $migrationStats['users']['migrated'] }}</td>
                                                    <td>{{ $migrationStats['users']['failed'] }}</td>
                                                    <td>
                                                        {{ $migrationStats['users']['total'] > 0 ? 
                                                            round(($migrationStats['users']['migrated'] / $migrationStats['users']['total']) * 100, 2) : 0 }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Hosting Accounts</td>
                                                    <td>{{ $migrationStats['accounts']['total'] }}</td>
                                                    <td>{{ $migrationStats['accounts']['migrated'] }}</td>
                                                    <td>{{ $migrationStats['accounts']['failed'] }}</td>
                                                    <td>
                                                        {{ $migrationStats['accounts']['total'] > 0 ? 
                                                            round(($migrationStats['accounts']['migrated'] / $migrationStats['accounts']['total']) * 100, 2) : 0 }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Tickets</td>
                                                    <td>{{ $migrationStats['tickets']['total'] }}</td>
                                                    <td>{{ $migrationStats['tickets']['migrated'] }}</td>
                                                    <td>{{ $migrationStats['tickets']['failed'] }}</td>
                                                    <td>
                                                        {{ $migrationStats['tickets']['total'] > 0 ? 
                                                            round(($migrationStats['tickets']['migrated'] / $migrationStats['tickets']['total']) * 100, 2) : 0 }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>SSL Certificates</td>
                                                    <td>{{ $migrationStats['ssl']['total'] }}</td>
                                                    <td>{{ $migrationStats['ssl']['migrated'] }}</td>
                                                    <td>{{ $migrationStats['ssl']['failed'] }}</td>
                                                    <td>
                                                        {{ $migrationStats['ssl']['total'] > 0 ? 
                                                            round(($migrationStats['ssl']['migrated'] / $migrationStats['ssl']['total']) * 100, 2) : 0 }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Settings</td>
                                                    <td>{{ $migrationStats['settings']['total'] }}</td>
                                                    <td>{{ $migrationStats['settings']['migrated'] }}</td>
                                                    <td>{{ $migrationStats['settings']['failed'] }}</td>
                                                    <td>
                                                        {{ $migrationStats['settings']['total'] > 0 ? 
                                                            round(($migrationStats['settings']['migrated'] / $migrationStats['settings']['total']) * 100, 2) : 0 }}%
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h5>Next Steps</h5>
                                        <ul>
                                            <li>Ensure all users are notified about their new password</li>
                                            <li>Verify that all data has been migrated correctly</li>
                                            <li>Check for any failed migrations and handle them manually if needed</li>
                                            <li>Update any necessary configurations</li>
                                            <li>Test the system thoroughly</li>
                                            <li>Set up automatic password change requirement on first login</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Auto refresh the page if migration is in progress
        @if(in_array('in_progress', $migrationStatus))
            setTimeout(function() {
                window.location.reload();
            }, 5000);
        @endif
    </script>
@endsection