@extends('layouts.master')

@section('title') @lang('translation.Announcements') @endsection

@section('css')
<style>
    :root {
        --primary-color: #4a6cf7;
        --primary-light: #eef2ff;
        --success-color: #13c296;
        --warning-color: #fbb040;
        --danger-color: #fb5d5d;
        --info-color: #3aa1ff;
        --secondary-color: #8f95b0;
        --text-color: #212b36;
        --text-light: #637381;
        --border-color: #e9ecef;
        --card-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        --transition-fast: 0.2s ease;
        --transition-normal: 0.3s ease;
    }

    .announcement-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    @media (min-width: 992px) {
        .announcement-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .announcement-card {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: var(--card-shadow);
        transition: transform var(--transition-normal), box-shadow var(--transition-normal);
        overflow: hidden;
    }

    .announcement-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .announcement-card.type-info {
        border-top: 3px solid var(--info-color);
    }

    .announcement-card.type-success {
        border-top: 3px solid var(--success-color);
    }

    .announcement-card.type-warning {
        border-top: 3px solid var(--warning-color);
    }

    .announcement-card.type-danger {
        border-top: 3px solid var(--danger-color);
    }

    .announcement-header {
        display: flex;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .announcement-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        margin-right: 16px;
    }

    .icon-info {
        background-color: rgba(58, 161, 255, 0.15);
        color: var(--info-color);
    }

    .icon-success {
        background-color: rgba(19, 194, 150, 0.15);
        color: var(--success-color);
    }

    .icon-warning {
        background-color: rgba(251, 176, 64, 0.15);
        color: var(--warning-color);
    }

    .icon-danger {
        background-color: rgba(251, 93, 93, 0.15);
        color: var(--danger-color);
    }

    .announcement-icon i {
        font-size: 22px;
    }

    .announcement-meta {
        flex-grow: 1;
        min-width: 0;
    }

    .announcement-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 5px;
        line-height: 1.3;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .announcement-date {
        display: flex;
        align-items: center;
        font-size: 13px;
        color: var(--text-light);
    }

    .announcement-date i {
        margin-right: 5px;
    }

    .announcement-content {
        flex-grow: 1;
        padding: 20px;
        color: var(--text-light);
        font-size: 14px;
        line-height: 1.6;
    }

    .announcement-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #f8f9fa;
    }

    .read-more-btn {
        display: inline-flex;
        align-items: center;
        font-size: 14px;
        font-weight: 500;
        color: var(--primary-color);
        transition: all var(--transition-fast);
    }

    .read-more-btn i {
        margin-left: 5px;
        transition: transform var(--transition-fast);
    }

    .read-more-btn:hover {
        color: #3d5bd9;
    }

    .read-more-btn:hover i {
        transform: translateX(3px);
    }

    .announcement-filter {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .filter-tab {
        padding: 8px 16px;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition-fast);
        border: 1px solid var(--border-color);
    }

    .filter-tab.active {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .filter-tab:hover:not(.active) {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        text-align: center;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: var(--card-shadow);
    }

    .empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: var(--primary-light);
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .empty-icon i {
        font-size: 32px;
    }

    .empty-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 10px;
    }

    .empty-message {
        font-size: 14px;
        color: var(--text-light);
        max-width: 400px;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.Home') @endslot
@slot('title') @lang('translation.Announcements') @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">
                        <i class="bx bx-megaphone me-1"></i> 
                        @lang('translation.All_Announcements')
                    </h4>
                    
                    <div class="filter-tabs">
                        <button type="button" class="filter-tab active" data-filter="all">@lang('translation.All')</button>
                        <button type="button" class="filter-tab" data-filter="info">@lang('translation.type_info')</button>
                        <button type="button" class="filter-tab" data-filter="success">@lang('translation.type_success')</button>
                        <button type="button" class="filter-tab" data-filter="warning">@lang('translation.type_warning')</button>
                        <button type="button" class="filter-tab" data-filter="danger">@lang('translation.type_danger')</button>
                    </div>
                </div>

                @if(count($announcements) > 0)
                    <div class="announcement-list">
                        @foreach($announcements as $announcement)
                            <div class="announcement-card type-{{ $announcement->type }}" data-type="{{ $announcement->type }}">
                                <div class="announcement-header">
                                    <div class="announcement-icon icon-{{ $announcement->type }}">
                                        @if($announcement->icon)
                                            <i class="bx {{ $announcement->icon }}"></i>
                                        @else
                                            <i class="bx bx-bell"></i>
                                        @endif
                                    </div>
                                    <div class="announcement-meta">
                                        <h3 class="announcement-title">{{ $announcement->title }}</h3>
                                        <div class="announcement-date">
                                            <i class="bx bx-calendar"></i>
                                            <span>{{ $announcement->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="announcement-content">
                                    {!! \Illuminate\Support\Str::limit(strip_tags($announcement->content), 150) !!}
                                </div>
                                <div class="announcement-footer">
                                    <a href="{{ route('announcements.show', $announcement->id) }}" class="read-more-btn">
                                        @lang('translation.Read_more')
                                        <i class="bx bx-right-arrow-alt"></i>
                                    </a>
                                    <span class="badge bg-{{ $announcement->type }} rounded-pill text-capitalize">
                                        @lang('translation.type_'.$announcement->type)
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $announcements->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bx bx-message-alt-x"></i>
                        </div>
                        <h3 class="empty-title">@lang('translation.No_announcements_available')</h3>
                        <p class="empty-message">@lang('translation.No_announcements_message')</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter announcements by type
        const filterTabs = document.querySelectorAll('.filter-tab');
        const announcements = document.querySelectorAll('.announcement-card');
        
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                filterTabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Get filter value
                const filter = this.getAttribute('data-filter');
                
                // Filter announcements
                announcements.forEach(announcement => {
                    if (filter === 'all' || announcement.getAttribute('data-type') === filter) {
                        announcement.style.display = 'flex';
                    } else {
                        announcement.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection