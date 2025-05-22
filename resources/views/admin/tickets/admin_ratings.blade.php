@extends('layouts.master')

@section('title') Ratings for {{ $admin->name }} @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') Tickets @endslot
        @slot('li_3') 
            <a href="{{ route('admin.tickets.ratings') }}">Ratings</a>
        @endslot
        @slot('title') {{ $admin->name }}'s Ratings @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Support Ratings for {{ $admin->name }}</h4>
                        <a href="{{ route('admin.tickets.ratings') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-1"></i> Back to All Ratings
                        </a>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card border shadow-none mb-md-0">
                                <div class="card-body text-center">
                                    <div class="avatar-xl mx-auto mb-3">
                                        <div class="avatar-title  text-primary rounded-circle display-5">
                                            {{ substr($admin->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <h5 class="font-size-16 mb-1">{{ $admin->name }}</h5>
                                    <p class="text-muted">{{ $admin->email }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3">
                            <div class="card border shadow-none mb-md-0">
                                <div class="card-body">
                                    <div class="text-center">
                                        <h1 class="display-5 mb-2">{{ number_format($averageRating, 1) }}</h1>
                                        <div class="d-flex justify-content-center mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= round($averageRating))
                                                    <i class="bx bxs-star text-warning fs-3"></i>
                                                @else
                                                    <i class="bx bx-star text-muted fs-3"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <p class="text-muted mb-0">Average Rating</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3">
                            <div class="card border shadow-none mb-md-0">
                                <div class="card-body">
                                    <div class="text-center">
                                        <h1 class="display-5 mb-2">{{ $totalRatings }}</h1>
                                        <p class="text-muted mb-0">Total Ratings</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3">
                            <div class="card border shadow-none mb-0">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Rating Distribution</h5>
                                    
                                    @foreach($ratingDistribution as $rating => $count)
                                        @php 
                                            $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                                        @endphp
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2" style="width: 40px;">
                                                <span>{{ $rating }} <i class="bx bxs-star text-warning"></i></span>
                                            </div>
                                            <div class="progress flex-1" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" 
                                                    style="width: {{ $percentage }}%" 
                                                    aria-valuenow="{{ $percentage }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100"></div>
                                            </div>
                                            <div class="ms-2" style="width: 30px;">
                                                <small>{{ $count }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5 class="font-size-16 mb-3">Rating Details</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Ticket</th>
                                        <th>User</th>
                                        <th>Rating</th>
                                        <th>Comment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ratings as $rating)
                                        <tr>
                                            <td>{{ $rating->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                <a href="{{ route('admin.tickets.show', $rating->ticket_id) }}" class="text-body fw-medium">
                                                    #{{ $rating->ticket_id }} - 
                                                    {{ Str::limit($rating->ticket->title, 40) }}
                                                </a>
                                            </td>
                                            <td>{{ $rating->user->name }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $rating->rating)
                                                            <i class="bx bxs-star text-warning"></i>
                                                        @else
                                                            <i class="bx bx-star text-muted"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </td>
                                            <td>
                                                @if($rating->comment)
                                                    <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                                        {{ $rating->comment }}
                                                    </span>
                                                    @if(strlen($rating->comment) > 50)
                                                        <button type="button" class="btn btn-sm btn-link p-0 ms-1" 
                                                                data-bs-toggle="modal" data-bs-target="#commentModal-{{ $rating->id }}">
                                                            Read more
                                                        </button>
                                                        
                                                        <!-- Comment Modal -->
                                                        <div class="modal fade" id="commentModal-{{ $rating->id }}" tabindex="-1" 
                                                             aria-labelledby="commentModalLabel-{{ $rating->id }}" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="commentModalLabel-{{ $rating->id }}">
                                                                            Rating Comment
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="d-flex mb-3">
                                                                            @for($i = 1; $i <= 5; $i++)
                                                                                @if($i <= $rating->rating)
                                                                                    <i class="bx bxs-star text-warning fs-4"></i>
                                                                                @else
                                                                                    <i class="bx bx-star text-muted fs-4"></i>
                                                                                @endif
                                                                            @endfor
                                                                        </div>
                                                                        <p>{{ $rating->comment }}</p>
                                                                        <div class="d-flex justify-content-between">
                                                                            <small class="text-muted">By: {{ $rating->user->name }}</small>
                                                                            <small class="text-muted">{{ $rating->created_at->format('d M Y, h:i A') }}</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No comment</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.tickets.show', $rating->ticket_id) }}" class="btn btn-sm btn-primary">
                                                    <i data-feather="external-link" class="icon-xs"></i> View Ticket
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i data-feather="alert-circle" class="icon-md mb-2"></i>
                                                    <p>No ratings found.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-3">
                            <nav>
                                <ul class="pagination justify-content-center">
                                    @for ($i = 1; $i <= $ratings->lastPage(); $i++)
                                        <li class="page-item {{ ($ratings->currentPage() == $i) ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $ratings->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .display-5 {
        font-size: 2.5rem;
    }
    .avatar-xl {
        height: 6rem;
        width: 6rem;
    }
    .avatar-title {
        align-items: center;
        background-color: #e9f3ff !important;
        color: #556ee6;
        display: flex;
        font-weight: 500;
        
        height: 100%;
        justify-content: center;
        width: 100%;
    }
</style>
@endsection