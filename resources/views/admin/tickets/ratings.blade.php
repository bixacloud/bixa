@extends('layouts.master')

@section('title') Support Team Ratings @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') Tickets @endslot
        @slot('title') Support Team Ratings @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="avatar-sm me-4">
                            <span class=" bg-soft-primary text-primary rounded-circle fs-2">
                                <i data-feather="star"></i>
                            </span>
                        </div>
                        <div class="flex-1 align-self-center">
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="mb-0 me-2">{{ number_format($averageRating, 1) }}</h5>
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <i class="bx bxs-star text-warning me-1"></i>
                                        @else
                                            <i class="bx bx-star text-warning me-1"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <h6 class="text-muted mb-0">Overall Average Rating</h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Rating Distribution</h5>
                    
                    @foreach($ratingDistribution as $rating => $count)
                        @php 
                            $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                            $starClass = [
                                5 => 'success',
                                4 => 'info',
                                3 => 'warning',
                                2 => 'danger',
                                1 => 'dark'
                            ][$rating];
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <span class="me-2">{{ $rating }}</span>
                                <div class="d-flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rating)
                                            <i class="bx bxs-star text-warning"></i>
                                        @else
                                            <i class="bx bx-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <div class="ms-2">{{ $count }}</div>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-{{ $starClass }}" role="progressbar" 
                                style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" 
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <strong>Total: {{ $totalRatings }} ratings</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Support Agents Ratings</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Admin</th>
                                    <th>Average Rating</th>
                                    <th>Total Ratings</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-soft-primary me-3">
                                                    <span class="font-size-16">{{ substr($admin->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h5 class="font-size-14 mb-0">{{ $admin->name }}</h5>
                                                    <small class="text-muted">{{ $admin->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="font-size-16 me-2">{{ number_format($admin->average_rating, 1) }}</span>
                                                <div>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= round($admin->average_rating))
                                                            <i class="bx bxs-star text-warning"></i>
                                                        @else
                                                            <i class="bx bx-star text-muted"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $admin->received_ratings_count }}</td>
                                        <td>
                                            <a href="{{ route('admin.tickets.admin-ratings', $admin->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i data-feather="eye" class="me-1 icon-xxs"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="text-muted">
                                                <i data-feather="alert-circle" class="icon-md mb-2"></i>
                                                <p>No ratings have been submitted yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e9f3ff;
        color: #556ee6;
    }
</style>
@endsection