@extends('layouts.master')

@section('title') @lang('translation.Dashboard') @endsection

@section('content')

@component('components.breadcrumb')
@slot('li_1') @lang('translation.Home') @endslot
@slot('title') @lang('translation.Dashboard') @endslot
@endcomponent

<div class="row">
    <!-- Hosting Stats -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-3">{{ Str::upper(__('translation.Free_accounts')) }}</h5>
                        <h2 class="mb-2">{{ $stats['hosting']['total'] }}</h2>
                        <a href="{{ route('hosting.index') }}" class="text-primary d-inline-block">
                            @lang('translation.View_accounts')
                            <i data-feather="chevron-right"></i>
                        </a>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i data-feather="hard-drive" class="font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SSL Stats -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-3">{{ Str::upper(__('translation.SSL_Certificates')) }}</h5>
                        <h2 class="mb-2">{{ $stats['ssl']['total'] }}</h2>
                        <a href="{{ route('ssl.index') }}" class="text-primary d-inline-block">
                            @lang('translation.View_SSL')
                            <i data-feather="chevron-right"></i>
                        </a>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-success">
                            <span class="avatar-title">
                                <i data-feather="shield" class="font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Stats -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-3">{{ Str::upper(__('translation.Tickets')) }}</h5>
                        <h2 class="mb-2">{{ $stats['tickets']['total'] }}</h2>
                        <a href="{{ route('user.tickets.index') }}" class="text-primary d-inline-block">
                            @lang('translation.View_tickets')
                            <i data-feather="chevron-right"></i>
                        </a>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-info">
                            <span class="avatar-title">
                                <i data-feather="life-buoy" class="font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Account List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">@lang('translation.Account_List')</h4>
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <thead class="table-light">
                              <tr>
                                <th>#</th>
                                <th>@lang('translation.Username')</th>
                                <th>@lang('translation.Label')</th>
                                <th>@lang('translation.Status')</th>
                                <th class="text-end">@lang('translation.Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accounts as $key => $account)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $account->username }}</td>
                                <td>{{ $account->label }}</td>
                                <td>
                                    @if(in_array($account->status, ['pending', 'deactivating', 'reactivating']))
                                        <span class="badge bg-warning">
                                            <i data-feather="loader" class="font-size-14 align-middle me-1"></i>
                                            @lang('translation.status_' . $account->status)
                                        </span>
                                    @elseif($account->status === 'active')
                                        <span class="badge bg-success">
                                            <i data-feather="check-circle" class="font-size-14 align-middle me-1"></i>
                                            @lang('translation.status_active')
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i data-feather="x-circle" class="font-size-14 align-middle me-1"></i>
                                            @lang('translation.status_' . $account->status)
                                        </span>
                                        
                                        @if($account->admin_deactivated)
                                            <span class="badge bg-dark ms-1">
                                                <i data-feather="shield" class="font-size-14 align-middle me-1"></i>
                                                @lang('translation.Admin')
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('hosting.view', $account->username) }}" 
                                       class="btn btn-sm waves-effect waves-light
                                       {{ $account->status === 'active' ? 'btn-success' : 
                                          (in_array($account->status, ['pending', 'deactivating', 'reactivating']) ? 'btn-warning' : 'btn-danger') }}">
                                        <i data-feather="settings" class="font-size-14 align-middle me-1"></i>
                                        @lang('translation.Manage')
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">@lang('translation.No_accounts_found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">
                        <i class="bx bx-megaphone me-1"></i> 
                        @lang('translation.Announcements')
                    </h4>
                    <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-primary">
                        @lang('translation.View_all')
                    </a>
                </div>
                
                @php
                // Get up to 3 active announcements
                $activeAnnouncements = App\Models\Announcement::where('is_enabled', true)
                    ->where(function($query) {
                        $query->whereNull('start_date')
                            ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                    })
                    ->orderBy('display_order', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->take(3) // Maximum 3 announcements
                    ->get();
                @endphp
                
                <div class="announcement-list">
                    @forelse($activeAnnouncements as $announcement)
                    <a href="{{ route('announcements.show', $announcement->id) }}" class="text-decoration-none">
                        <div class="card border-{{ $announcement->type }} border mb-3">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs">
                                            <span class="avatar-title rounded-circle bg-{{ $announcement->type }} text-white">
                                                @if($announcement->icon)
                                                    <i class="bx {{ $announcement->icon }}"></i>
                                                @else
                                                    <i class="bx bx-bell"></i>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="font-size-14 mb-1">{{ $announcement->title }}</h5>
                                        <p class="text-muted mb-0 text-truncate">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 65) }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <i class="bx bx-chevron-right text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @empty
                    @forelse($announcements as $announcement)
                    <div class="announcement-item mb-3">
                        <p class="text-muted mb-2">{{ $announcement['message'] }}</p>
                    </div>
                    @empty
                    <p class="text-muted">@lang('translation.No_announcements')</p>
                    @endforelse
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize feather icons
        feather.replace();
        
        // Handle announcement clicks if necessary
        // You can add JavaScript here if needed
    });
</script>
@endsection