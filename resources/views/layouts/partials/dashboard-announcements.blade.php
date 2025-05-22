@php
    $announcements = \App\Models\Announcement::active()->take(3)->get();
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Announcements</h5>
    </div>
    <div class="card-body">
        @if($announcements->isEmpty())
            <div class="text-center py-3">
                <i class="bx bx-bell font-size-24 text-muted mb-2"></i>
                <p class="mb-0">No announcements</p>
            </div>
        @else
            @foreach($announcements as $announcement)
                <div class="alert alert-{{ $announcement->type }} mb-3" role="alert">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <i class="{{ $announcement->icon_class }} font-size-18"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-1">{{ $announcement->title }}</h5>
                            <div>{!! $announcement->content !!}</div>
                            
                            @if($announcement->start_date || $announcement->end_date)
                                <hr>
                                <small class="text-muted">
                                    @if($announcement->start_date)
                                        From: {{ $announcement->start_date->format('M d, Y') }}
                                    @endif
                                    
                                    @if($announcement->end_date)
                                        @if($announcement->start_date) | @endif
                                        Until: {{ $announcement->end_date->format('M d, Y') }}
                                    @endif
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>