@extends('layouts.master')

@section('title') Login History Management @endsection

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('title') Login History @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                        <h4 class="card-title me-2">All Users Login History</h4>
                        <div class="ms-auto">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.auth-log-settings') }}" class="btn btn-soft-primary">
                                    <i class="mdi mdi-cog font-size-16 align-middle me-1"></i> Settings
                                </a>
                                
                                <div class="dropdown">
                                    <button class="btn btn-soft-secondary dropdown-toggle" type="button" 
                                            id="userFilterDropdown" data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <i class="mdi mdi-filter-outline font-size-16 align-middle me-1"></i>
                                        {{ request('user_id') ? \App\Models\User::find(request('user_id'))->name : 'All Users' }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userFilterDropdown">
                                        <li>
                                            <a class="dropdown-item {{ !request('user_id') ? 'active' : '' }}" 
                                               href="{{ route('admin.authentication-logs') }}">All Users</a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @foreach($users as $user)
                                            <li>
                                                <a class="dropdown-item {{ request('user_id') == $user->id ? 'active' : '' }}" 
                                                   href="{{ route('admin.authentication-logs', ['user_id' => $user->id]) }}">
                                                   {{ $user->name }} ({{ $user->email }})
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Browser</th>
                                    @if($locationEnabled)
                                    <th>Location</th>
                                    @endif
                                    <th>Login Time</th>
                                    <th>Status</th>
                                    <th>Logout Time</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        $user = \App\Models\User::find($log->authenticatable_id);
                                        $agent = new Jenssegers\Agent\Agent();
                                        $agent->setUserAgent($log->user_agent);
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($user)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-primary text-white">
                                                            {{ substr($user->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>{{ $user->name }}</div>
                                                </div>
                                            @else
                                                <span class="text-muted">Unknown User</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>
                                            <div>{{ $agent->platform() }}</div>
                                            <small class="text-muted">{{ $agent->browser() }}</small>
                                        </td>
                                        @if($locationEnabled)
                                        <td>
                                            @if($log->location && isset($log->location['default']) && $log->location['default'] === false)
                                                <div>{{ $log->location['city'] ?? '' }}</div>
                                                <small class="text-muted">{{ $log->location['state'] ?? '' }}, {{ $log->location['country'] ?? '' }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            @if($log->login_at)
                                                <div>{{ $log->login_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $log->login_at->format('H:i:s') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->login_successful)
                                                <span class="badge rounded-pill bg-success">Success</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->logout_at)
                                                <div>{{ $log->logout_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $log->logout_at->format('H:i:s') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($user)
                                                <a href="{{ route('admin.user-authentication-logs', $log->authenticatable_id) }}" 
                                                   class="btn btn-sm btn-primary" title="View Details">
                                                    <i class="mdi mdi-eye font-size-16"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $locationEnabled ? 8 : 7 }}" class="text-center py-4">
                                            <div class="avatar-sm mx-auto mb-4">
                                                <div class="avatar-title rounded-circle bg-light text-primary">
                                                    <i class="mdi mdi-server-off font-size-24"></i>
                                                </div>
                                            </div>
                                            <h5 class="text-center">No login history found</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang đơn giản - chỉ hiển thị số trang -->
                    <div class="d-flex justify-content-center mt-4">
                        <ul class="pagination">
                            @for ($i = 1; $i <= $logs->lastPage(); $i++)
                                <li class="page-item {{ ($logs->currentPage() == $i) ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $logs->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection