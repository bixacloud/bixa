@extends('layouts.master')

@section('title') Account Details - {{ $account->username }} @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') <a href="{{ route('admin.hosting.index') }}">Hosting Accounts</a> @endslot
        @slot('title') Account Details @endslot
    @endcomponent

    {{-- Alert Messages --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Info Card --}}
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <i data-feather="server" class="font-size-24 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">{{ $account->username }}</h4>
                            <span class="badge {{ $account->status === 'active' ? 'bg-success' : 
                                    (in_array($account->status, ['pending', 'deactivating', 'reactivating']) ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($account->status) }}
                            </span>
                            
                            @if($account->admin_deactivated)
                                <span class="badge bg-dark ms-1">
                                    Admin Deactivated
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- User Info (Simplified) --}}
                    <div class="border-top border-bottom py-3 mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i data-feather="user" class="font-size-24 text-info"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-size-15 mb-1">{{ $account->user->name ?? 'Unknown' }}</h5>
                                        <p class="text-muted mb-0">{{ $account->user->email }}</p>
                                        <p class="text-muted mb-0">User ID: {{ $account->user_id }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Admin Deactivation Notice --}}
                    @if($account->admin_deactivated)
                    <div class="alert alert-danger admin-deactivation-notice mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i data-feather="alert-octagon" class="font-size-24"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mt-0 mb-1">Account Admin Deactivated</h5>
                                <p class="mb-0">Deactivated At: {{ $account->admin_deactivated_at->format('F j, Y, g:i a') }}</p>
                                <p class="mt-2 mb-0"><strong>Reason:</strong> {{ $account->admin_deactivation_reason }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Quick Actions --}}
                    <h5 class="font-size-15 mb-3">Quick Actions</h5>
                    <div class="row g-3 mb-4">
                        {{-- Control Panel --}}
                        <div class="col-md-4">
                            <a href="{{ route('admin.hosting.cpanel', $account->username) }}" 
                               target="_blank"
                               class="btn btn-primary w-100 text-left d-flex align-items-center {{ $account->status !== 'active' ? 'disabled' : '' }}">
                                <i data-feather="monitor" class="font-size-20 me-2"></i>
                                <div>
                                    <h6 class="mb-0 text-white">Control Panel</h6>
                                    <p class="text-white-50 mb-0 small">Access cPanel</p>
                                </div>
                            </a>
                        </div>

                        {{-- File Manager --}}
                        <div class="col-md-4">
                            <a href="{{ route('admin.hosting.filemanager', $account->username) }}"
                               target="_blank"
                               class="btn btn-info w-100 text-left d-flex align-items-center {{ $account->status !== 'active' ? 'disabled' : '' }}">
                                <i data-feather="folder" class="font-size-20 me-2"></i>
                                <div>
                                    <h6 class="mb-0 text-white">File Manager</h6>
                                    <p class="text-white-50 mb-0 small">Manage Files</p>
                                </div>
                            </a>
                        </div>

                        {{-- Admin Action --}}
                        <div class="col-md-4">
                            @if($account->status === 'active' && !$account->admin_deactivated)
                                <button type="button" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#adminDeactivateModal"
                                       class="btn btn-danger w-100 text-left d-flex align-items-center">
                                    <i data-feather="shield-off" class="font-size-20 me-2"></i>
                                    <div>
                                        <h6 class="mb-0 text-white">Admin Deactivate</h6>
                                        <p class="text-white-50 mb-0 small">Block Account</p>
                                    </div>
                                </button>
                            @elseif($account->status === 'deactivated' && $account->admin_deactivated)
                                <button type="button" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#adminReactivateModal"
                                       class="btn btn-success w-100 text-left d-flex align-items-center">
                                    <i data-feather="shield" class="font-size-20 me-2"></i>
                                    <div>
                                        <h6 class="mb-0 text-white">Admin Reactivate</h6>
                                        <p class="text-white-50 mb-0 small">Unblock Account</p>
                                    </div>
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary w-100 text-left d-flex align-items-center" disabled>
                                    <i data-feather="shield" class="font-size-20 me-2"></i>
                                    <div>
                                        <h6 class="mb-0 text-white">Admin Action</h6>
                                        <p class="text-white-50 mb-0 small">Not Available</p>
                                    </div>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Account Details --}}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card border shadow-none mb-4">
                                <div class="card-header bg-transparent">
                                    <h5 class="card-title mb-0">Account Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <th><i data-feather="user" class="font-size-16 text-primary me-2"></i> Username</th>
                                                    <td>{{ $account->username }}</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="tag" class="font-size-16 text-primary me-2"></i> Label</th>
                                                    <td>{{ $account->label }}</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="key" class="font-size-16 text-primary me-2"></i> Password</th>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span id="password-hidden">••••••••</span>
                                                            <span id="password-shown" class="d-none">{{ $account->password }}</span>
                                                            <button type="button" onclick="togglePassword()" class="btn btn-link text-muted p-0 ms-2">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="globe" class="font-size-16 text-primary me-2"></i> Main Domain</th>
                                                    <td>{{ $account->domain }}</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="globe" class="font-size-16 text-primary me-2"></i> Server IP</th>
                                                    <td>{{ $serverIp ?: 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="clock" class="font-size-16 text-primary me-2"></i> Created At</th>
                                                    <td>{{ $account->created_at->format('F j, Y, g:i a') }}</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="shield" class="font-size-16 text-primary me-2"></i> cPanel Verified</th>
                                                    <td>
                                                        @if($account->cpanel_verified)
                                                            <span class="badge bg-success">Yes</span>
                                                            <small class="text-muted">{{ $account->cpanel_verified_at ? $account->cpanel_verified_at->format('F j, Y') : '' }}</small>
                                                        @else
                                                            <span class="badge bg-danger">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card border shadow-none mb-4">
                                <div class="card-header bg-transparent">
                                    <h5 class="card-title mb-0">Connection Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-nowrap mb-0">
                                            <tbody>
                                                <tr>
                                                    <th><i data-feather="database" class="font-size-16 text-primary me-2"></i> MySQL Host</th>
                                                    <td>{{ $account->mysql_host }}</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="git-commit" class="font-size-16 text-primary me-2"></i> MySQL Port</th>
                                                    <td>3306</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="database" class="font-size-16 text-primary me-2"></i> SQL Server</th>
                                                    <td>{{ $account->sql_server ?: 'Not assigned yet' }}</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="server" class="font-size-16 text-primary me-2"></i> FTP Host</th>
                                                    <td>ftpupload.net</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="git-commit" class="font-size-16 text-primary me-2"></i> FTP Port</th>
                                                    <td>21</td>
                                                </tr>
                                                <tr>
                                                    <th><i data-feather="user" class="font-size-16 text-primary me-2"></i> FTP Username</th>
                                                    <td>{{ $account->username }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Domains --}}
                    <div class="card border shadow-none">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">Domains</h5>
                        </div>
                        <div class="card-body">
                            @if($account->status === 'active' && count($domains) > 0)
                                <div class="table-responsive">
                                    <table class="table table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Domain</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($domains as $key => $domain)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ is_array($domain) ? $domain['domain'] : $domain }}</td>
                                                    <td class="text-end">
                                                        <a href="http://{{ is_array($domain) ? $domain['domain'] : $domain }}" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-soft-primary">
                                                            <i data-feather="external-link" class="font-size-12"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i data-feather="globe" class="text-muted font-size-40 mb-3"></i>
                                    <h5 class="text-muted">No domains found</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Deactivate Modal -->
    @if($account->status === 'active' && !$account->admin_deactivated)
    <div class="modal fade" id="adminDeactivateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.hosting.settings.update', $account->username) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="admin_deactivate">
                    <div class="modal-header">
                        <h5 class="modal-title">Admin Deactivate Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i data-feather="alert-triangle" class="me-2"></i>
                            This will deactivate the account and prevent the user from reactivating it.
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="{{ $account->username }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deactivation Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reason" rows="3" 
                                placeholder="Enter reason for deactivation" required></textarea>
                            <small class="text-muted">This reason will be shown to the user.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Deactivate Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Admin Reactivate Modal -->
    @if($account->status === 'deactivated' && $account->admin_deactivated)
    <div class="modal fade" id="adminReactivateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.hosting.settings.update', $account->username) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="admin_reactivate">
                    <div class="modal-header">
                        <h5 class="modal-title">Admin Reactivate Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i data-feather="info" class="me-2"></i>
                            This will remove the admin deactivation flag and allow the account to be reactivated.
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="{{ $account->username }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Current Deactivation Reason</label>
                            <textarea class="form-control" rows="3" readonly>{{ $account->admin_deactivation_reason }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Remove Admin Restrictions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Hidden account username for JS --}}
    <input type="hidden" id="accountUsername" value="{{ $account->username }}">
@endsection

@section('script')
<script>
// Password Toggle Function
function togglePassword() {
    const hiddenEl = document.getElementById('password-hidden');
    const shownEl = document.getElementById('password-shown');
    const icon = document.querySelector('.fa-eye');

    if (hiddenEl.classList.contains('d-none')) {
        hiddenEl.classList.remove('d-none');
        shownEl.classList.add('d-none');
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        hiddenEl.classList.add('d-none');
        shownEl.classList.remove('d-none');
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection