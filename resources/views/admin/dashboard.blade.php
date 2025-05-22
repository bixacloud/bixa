@extends('layouts.master')

@section('title') Dashboard @endsection

@section('content')

@component('components.breadcrumb')
@slot('li_1') Home @endslot
@slot('title') Dashboard @endslot
@endcomponent

<div class="row">
    @if($isAdmin)
    <!-- Users Stats - Only Admin can see this -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="users" class="text-warning font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Users Overview</h5>
                        <p class="text-muted mb-0">{{ $stats['users']['total'] }} in total</p>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-0 text-muted"><i data-feather="shield" class="icon-xs text-primary me-1"></i> Admin: {{ $stats['users']['admin'] }}</p>
                        </div>
                        <div>
                            <p class="mb-0 text-muted"><i data-feather="users" class="icon-xs text-success me-1"></i> Support: {{ $stats['users']['support'] }}</p>
                        </div>
                        <div>
                            <p class="mb-0 text-muted"><i data-feather="user" class="icon-xs text-info me-1"></i> Users: {{ $stats['users']['user'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Hosting Stats - Both Admin and Support can see this -->
    <div class="col-xl-{{ $isAdmin ? '4' : '6' }}">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="server" class="text-primary font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Hosting Accounts</h5>
                        <p class="text-muted mb-0">{{ $stats['hosting']['total'] }} in total</p>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-4">
                            <p class="mb-0 text-muted"><i data-feather="check-circle" class="icon-xs text-success me-1"></i> Active: {{ $stats['hosting']['active'] }}</p>
                        </div>
                        <div class="col-4">
                            <p class="mb-0 text-muted"><i data-feather="clock" class="icon-xs text-warning me-1"></i> Pending: {{ $stats['hosting']['pending'] }}</p>
                        </div>
                        <div class="col-4">
                            <p class="mb-0 text-muted"><i data-feather="x-circle" class="icon-xs text-danger me-1"></i> Suspended: {{ $stats['hosting']['suspended'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Stats - Both Admin and Support can see this -->
    <div class="col-xl-{{ $isAdmin ? '4' : '6' }}">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="message-square" class="text-info font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Support Tickets</h5>
                        <p class="text-muted mb-0">{{ $stats['tickets']['total'] }} in total</p>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-4">
                            <p class="mb-0 text-muted"><i data-feather="mail" class="icon-xs text-warning me-1"></i> Open: {{ $stats['tickets']['open'] }}</p>
                        </div>
                        <div class="col-4">
                            <p class="mb-0 text-muted"><i data-feather="clock" class="icon-xs text-info me-1"></i> Pending: {{ $stats['tickets']['pending'] }}</p>
                        </div>
                        <div class="col-4">
                            <p class="mb-0 text-muted"><i data-feather="check-square" class="icon-xs text-success me-1"></i> Closed: {{ $stats['tickets']['closed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tools Section -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tools</h4>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tools Cards -->
    @if($isAdmin)
    <!-- Admin Only Tools -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="settings" class="text-primary font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Configuration</h5>
                        <a href="{{ route('admin.settings.index') }}" class="text-muted">Manage Settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="users" class="text-success font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">User Management</h5>
                        <a href="{{ route('admin.users.index') }}" class="text-muted">Manage Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tools for both Admin and Support -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="server" class="text-warning font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Hosting Accounts</h5>
                        <a href="{{ route('admin.hosting.index') }}" class="text-muted">Manage Hosting</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="message-square" class="text-info font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Support Tickets</h5>
                        <a href="{{ route('admin.tickets.index') }}" class="text-muted">Manage Tickets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isAdmin)
<!-- Additional Tools and Info - Only Admin can see this -->
<div class="row">
    <!-- Documentation -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="book" class="text-info font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Documentation</h5>
                        <a href="{{ route('admin.documentation') }}" class="text-muted">Setup Guide</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms of Service -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="file-text" class="text-cyan font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Terms of Service</h5>
                        <a href="{{ route('admin.tos') }}" class="text-muted">View here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Bixa -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="info" class="text-warning font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">About Bixa</h5>
                        <a href="{{ route('admin.about') }}" class="text-muted">View here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- License -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="file" class="text-teal font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">License</h5>
                        <a href="{{ route('admin.license') }}" class="text-muted">View here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support/Github -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="help-circle" class="text-success font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Need Help?</h5>
                        <a href="https://github.com/bixadotapp/bixa/issues" class="text-muted" target="_blank">Open an issue in Github</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Donate -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <i data-feather="heart" class="text-danger font-size-24"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Like Bixa?</h5>
                        <a href="{{ route('admin.donate') }}" class="text-muted" target="_blank">Donate here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information - Only Admin can see this -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">System Information</h4>
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <tbody>
                            <tr>
                                <th scope="row">Version :</th>
                                <td>{{ $systemInfo['version'] }}</td>
                                <th scope="row">PHP Version :</th>
                                <td>{{ $systemInfo['php_version'] }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Laravel Version :</th>
                                <td>{{ $systemInfo['laravel_version'] }}</td>
                                <th scope="row">Database :</th>
                                <td>{{ $systemInfo['database'] }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Server :</th>
                                <td colspan="3">{{ $systemInfo['server'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection