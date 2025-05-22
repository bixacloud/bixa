@extends('layouts.master')

@section('title') Advertisement Detail Statistics @endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/chartist/chartist.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Advertisements @endslot
        @slot('li_2') 
            <a href="{{ route('admin.advertisements.statistics') }}">Statistics</a>
        @endslot
        @slot('title') {{ $advertisement->name }} @endslot
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
                            <div class="font-size-16 mt-2">Impressions</div>
                        </div>
                    </div>
                    <h4 class="mt-4">{{ number_format($advertisement->impressions) }}</h4>
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
                            <div class="font-size-16 mt-2">Clicks</div>
                        </div>
                    </div>
                    <h4 class="mt-4">{{ number_format($advertisement->clicks) }}</h4>
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
                            <div class="font-size-16 mt-2">CTR</div>
                        </div>
                    </div>
                    <h4 class="mt-4">{{ number_format($ctr, 2) }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Advertisement Details</h4>
            
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th width="20%">Name</th>
                            <td>{{ $advertisement->name }}</td>
                        </tr>
                        <tr>
                            <th>Slot</th>
                            <td>
                                <span class="badge bg-primary">{{ $advertisement->slot->name ?? $advertisement->slot_position }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($advertisement->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Performance Chart</h4>
            
            <div id="performance-chart" class="apex-charts" style="height: 350px"></div>
        </div>
    </div>

    @if(count($dailyStats) > 0)
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Daily Statistics</h4>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Impressions</th>
                            <th>Clicks</th>
                            <th>CTR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyStats as $stat)
                            <tr>
                                <td>{{ $stat->date }}</td>
                                <td>{{ number_format($stat->impressions) }}</td>
                                <td>{{ number_format($stat->clicks) }}</td>
                                <td>{{ number_format($stat->ctr, 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('script')
    <script src="{{ URL::asset('/build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sample data for the chart (can be replaced with actual data)
            const dates = [];
            const impressionsData = [];
            const clicksData = [];
            
            // Generate sample data for 30 days
            for (let i = 30; i >= 1; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                dates.push(date.toISOString().slice(0, 10));
                
                // Generate sample data - replace with actual data if available
                const baseImpressions = {{ $advertisement->impressions / 30 }};
                const fluctuation = Math.random() * 0.5 + 0.75; // 75%-125% of base value
                const dailyImpressions = Math.round(baseImpressions * fluctuation);
                impressionsData.push(dailyImpressions);
                
                const baseClicks = {{ $advertisement->clicks / 30 }};
                const clickFluctuation = Math.random() * 0.5 + 0.75;
                const dailyClicks = Math.round(baseClicks * clickFluctuation);
                clicksData.push(dailyClicks);
            }
            
            // Create the chart
            var options = {
                series: [{
                    name: 'Impressions',
                    data: impressionsData
                }, {
                    name: 'Clicks',
                    data: clicksData
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: ['#556ee6', '#34c38f'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        inverseColors: false,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100, 100, 100]
                    }
                },
                xaxis: {
                    categories: dates,
                    labels: {
                        formatter: function(value) {
                            const date = new Date(value);
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Count'
                    }
                },
                markers: {
                    size: 4,
                    hover: {
                        size: 6
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return y.toFixed(0);
                            }
                            return y;
                        }
                    },
                    x: {
                        formatter: function(x) {
                            return new Date(dates[x]).toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'short', 
                                day: 'numeric' 
                            });
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#performance-chart"), options);
            chart.render();
        });
    </script>
@endsection