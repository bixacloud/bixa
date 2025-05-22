@extends('layouts.master')

@section('title') @lang('translation.Login_History') @endsection

@section('css')
<!-- DataTables -->
<link href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Account') @endslot
        @slot('title') @lang('translation.Login_History') @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                        <h4 class="card-title me-2">@lang('translation.My_Login_History')</h4>
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
                                    <th>@lang('translation.IP_Address')</th>
                                    <th>@lang('translation.Device_Browser')</th>
                                    @if($locationEnabled)
                                    <th>@lang('translation.Location')</th>
                                    @endif
                                    <th>@lang('translation.Login_Time')</th>
                                    <th>@lang('translation.Status')</th>
                                    <th>@lang('translation.Logout_Time')</th>
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
                                                <span class="badge rounded-pill bg-success">@lang('translation.Success')</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger">@lang('translation.Failed')</span>
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
                                            <h5 class="text-center">@lang('translation.No_login_history')</h5>
                                            <p class="text-muted">@lang('translation.Login_activities_appear_here')</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Simple pagination - only showing page numbers -->
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
<script src="https://cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.2/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables if needed
        // $('.datatable').DataTable();
    });
</script>
@endsection