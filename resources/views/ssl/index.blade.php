@extends('layouts.master')
@section('title') @lang('translation.SSL_Certificates') @endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.SSL') @endslot
        @slot('title') @lang('translation.Certificates') @endslot
    @endcomponent
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">@lang('translation.SSL_Certificates')</h4>
                        <a href="{{ route('ssl.create') }}" class="btn btn-primary">
                            @lang('translation.Create_Certificate')
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('translation.Domain')</th>
                                    <th>@lang('translation.Type')</th>
                                    <th>@lang('translation.Status')</th>
                                    <th>@lang('translation.Valid_Until')</th>
                                    <th>@lang('translation.Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certificates as $cert)
                                    <tr>
                                        <td>{{ $cert->domain }}</td>
                                        <td>{{ ucfirst($cert->type) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $cert->status === 'active' ? 'success' : 'warning' }}">
                                                {{ ucfirst($cert->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $cert->valid_until?->format('Y-m-d') ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('ssl.show', $cert) }}" 
                                               class="btn btn-sm btn-info">@lang('translation.View')</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            @lang('translation.No_certificates_found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <nav>
                            <ul class="pagination justify-content-center">
                                @for ($i = 1; $i <= $certificates->lastPage(); $i++)
                                    <li class="page-item {{ ($certificates->currentPage() == $i) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $certificates->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection