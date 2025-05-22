@extends('layouts.master')

@section('title') Advertisement Statistics @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Advertisements @endslot
        @slot('title') Statistics @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="me-3 text-primary">
                            <i data-feather="eye" class="icon-md"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-size-16 mt-2">Total Impressions</div>
                        </div>
                    </div>
                    <h4 class="mt-4">{{ number_format($totalStats->total_impressions) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="me-3 text-primary">
                            <i data-feather="mouse-pointer" class="icon-md"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-size-16 mt-2">Total Clicks</div>
                        </div>
                    </div>
                    <h4 class="mt-4">{{ number_format($totalStats->total_clicks) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="me-3 text-primary">
                            <i data-feather="percent" class="icon-md"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-size-16 mt-2">Average CTR</div>
                        </div>
                    </div>
                    <h4 class="mt-4">{{ number_format($totalStats->ctr, 2) }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">Advertisement Performance</h4>
                <a href="{{ route('admin.advertisements.statistics.export') }}" class="btn btn-primary btn-sm">
                    <i data-feather="download" class="icon-sm"></i> Export CSV
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Advertisement</th>
                            <th>Slot</th>
                            <th>Impressions</th>
                            <th>Clicks</th>
                            <th>CTR</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements as $ad)
                            <tr>
                                <td>{{ $ad->id }}</td>
                                <td>{{ $ad->name }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $ad->slot->name ?? $ad->slot_position }}</span>
                                </td>
                                <td>{{ number_format($ad->impressions) }}</td>
                                <td>{{ number_format($ad->clicks) }}</td>
                                <td>{{ number_format($ad->ctr, 2) }}%</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.advertisements.statistics.show', $ad->id) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i data-feather="bar-chart-2"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No statistics found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Slot Performance</h4>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Slot</th>
                            <th>Impressions</th>
                            <th>Clicks</th>
                            <th>CTR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slotStats as $stat)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $stat->slot->name ?? $stat->slot_position }}</span>
                                </td>
                                <td>{{ number_format($stat->impressions) }}</td>
                                <td>{{ number_format($stat->clicks) }}</td>
                                <td>{{ number_format($stat->ctr, 2) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No slot statistics found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection