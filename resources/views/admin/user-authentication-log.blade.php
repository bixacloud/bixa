@extends('layouts.master')

@section('title') User Login History @endsection

@section('css')
<!-- DataTables -->
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') 
            <a href="{{ route('admin.authentication-logs') }}">Login History</a>
        @endslot
        @slot('title') {{ $user->name }}'s History @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                        <div>
                            <h4 class="card-title mb-1">Login History</h4>
                            <p class="text-muted mb-0">
                                <i class="mdi mdi-account me-1"></i> {{ $user->name }} 
                                <i class="mdi mdi-email-outline ms-2 me-1"></i> {{ $user->email }}
                            </p>
                        </div>
                        <div class="ms-auto">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.authentication-logs') }}" class="btn btn-soft-secondary">
                                    <i class="mdi mdi-arrow-left font-size-16 align-middle me-1"></i> Back to All Logs
                                </a>
                                <a href="{{ route('admin.auth-log-settings') }}" class="btn btn-soft-primary">
                                    <i class="mdi mdi-cog font-size-16 align-middle me-1"></i> Settings
                                </a>
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
                                    <th>IP Address</th>
                                    <th>Browser</th>
                                    @if($locationEnabled)
                                    <th>Location</th>
                                    @endif
                                    <th>Login Time</th>
                                    <th>Status</th>
                                    <th>Logout Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        $agent = new Jenssegers\Agent\Agent();
                                        $agent->setUserAgent($log->user_agent);
                                    @endphp
                                    <tr>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $locationEnabled ? 6 : 5 }}" class="text-center py-4">
                                            <div class="avatar-sm mx-auto mb-4">
                                                <div class="avatar-title rounded-circle bg-light text-primary">
                                                    <i class="mdi mdi-server-off font-size-24"></i>
                                                </div>
                                            </div>
                                            <h5 class="text-center">No login history found for this user</h5>
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