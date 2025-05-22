@extends('layouts.master')

@section('title') {{ $announcement->title }} @endsection

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

    /* For announcement detail page */
    .announcement-detail-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .detail-header {
        position: relative;
        padding: 30px;
        border-bottom: 1px solid var(--border-color);
    }

    .detail-header.type-info {
        background-color: rgba(58, 161, 255, 0.1);
    }

    .detail-header.type-success {
        background-color: rgba(19, 194, 150, 0.1);
    }

    .detail-header.type-warning {
        background-color: rgba(251, 176, 64, 0.1);
    }

    .detail-header.type-danger {
        background-color: rgba(251, 93, 93, 0.1);
    }

    .detail-meta {
        display: flex;
        align-items: flex-start;
    }

    .detail-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        margin-right: 20px;
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

    .detail-icon i {
        font-size: 28px;
    }

    .detail-title {
        font-size: 22px;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .detail-date {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        font-size: 14px;
        color: var(--text-light);
    }

    .detail-date-item {
        display: flex;
        align-items: center;
        margin-right: 20px;
        margin-bottom: 5px;
    }

    .detail-date-item i {
        margin-right: 5px;
    }

    .detail-separator {
        margin: 0 10px;
    }

    .detail-content {
        padding: 30px;
        color: var(--text-color);
        font-size: 15px;
        line-height: 1.7;
    }

    .detail-content p {
        margin-bottom: 15px;
    }

    .detail-content img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin: 15px 0;
    }

    .detail-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 30px;
        background-color: #f8f9fa;
        border-top: 1px solid var(--border-color);
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        padding: 8px 20px;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
        background-color: var(--primary-color);
        transition: all var(--transition-fast);
        border: none;
    }

    .back-button i {
        margin-right: 5px;
    }

    .back-button:hover {
        background-color: #3d5bd9;
    }

    .share-buttons {
        display: flex;
        gap: 10px;
    }

    .share-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #f0f2f5;
        color: var(--text-light);
        transition: all var(--transition-fast);
    }

    .share-button:hover {
        background-color: var(--primary-color);
        color: #fff;
    }

    /* Mobile responsiveness */
    @media (max-width: 767px) {
        .detail-meta {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .detail-icon {
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        .detail-date {
            justify-content: center;
        }
        
        .detail-footer {
            flex-direction: column;
            gap: 15px;
        }
        
        .share-buttons {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.Home') @endslot
@slot('li_2') <a href="{{ route('announcements.index') }}">@lang('translation.Announcements')</a> @endslot
@slot('title') {{ $announcement->title }} @endslot
@endcomponent

<div class="row">
    <div class="col-lg-10 col-md-12 mx-auto">
        <div class="announcement-detail-card">
            <div class="detail-header type-{{ $announcement->type }}">
                <div class="detail-meta">
                    <div class="detail-icon icon-{{ $announcement->type }}">
                        @if($announcement->icon)
                            <i class="bx {{ $announcement->icon }}"></i>
                        @else
                            <i class="bx bx-bell"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="detail-title">{{ $announcement->title }}</h1>
                        <div class="detail-date">
                            <div class="detail-date-item">
                                <i class="bx bx-calendar"></i>
                                <span>{{ $announcement->created_at->format('F d, Y') }}</span>
                            </div>
                            
                            @if($announcement->start_date || $announcement->end_date)
                                <div class="detail-date-item">
                                    <i class="bx bx-time-five"></i>
                                    @if($announcement->start_date && $announcement->end_date)
                                        <span>@lang('translation.Active_from') {{ $announcement->start_date->format('M d, Y') }} 
                                        @lang('translation.to') {{ $announcement->end_date->format('M d, Y') }}</span>
                                    @elseif($announcement->start_date)
                                        <span>@lang('translation.Active_since') {{ $announcement->start_date->format('M d, Y') }}</span>
                                    @elseif($announcement->end_date)
                                        <span>@lang('translation.Active_until') {{ $announcement->end_date->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="detail-date-item">
                                <span class="badge bg-{{ $announcement->type }} rounded-pill text-capitalize">
                                    @lang('translation.type_'.$announcement->type)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="detail-content">
                {!! $announcement->content !!}
            </div>
            <div class="detail-footer">
                <a href="{{ route('announcements.index') }}" class="back-button">
                    <i class="bx bx-arrow-back"></i> @lang('translation.Back_to_Announcements')
                </a>
                
                <div class="share-buttons">
                    <a href="javascript:void(0)" class="share-button" title="@lang('translation.Share_on_social')" onclick="shareOnFacebook()">
                        <i class="bx bxl-facebook"></i>
                    </a>
                    <a href="javascript:void(0)" class="share-button" title="@lang('translation.Share_on_social')" onclick="shareOnTwitter()">
                        <i class="bx bxl-twitter"></i>
                    </a>
                    <a href="javascript:void(0)" class="share-button" title="@lang('translation.Share_via_email')" onclick="shareViaEmail()">
                        <i class="bx bx-envelope"></i>
                    </a>
                    <a href="javascript:void(0)" class="share-button" title="@lang('translation.Copy_link')" onclick="copyLink()">
                        <i class="bx bx-link"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Share functionality
        window.shareOnFacebook = function() {
            const title = "{{ $announcement->title }}";
            const url = window.location.href;
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        };
        
        window.shareOnTwitter = function() {
            const title = "{{ $announcement->title }}";
            const url = window.location.href;
            window.open(`https://twitter.com/intent/tweet?text=${title}&url=${url}`, '_blank');
        };
        
        window.shareViaEmail = function() {
            const title = "{{ $announcement->title }}";
            const url = window.location.href;
            window.location.href = `mailto:?subject=${title}&body=Check out this announcement: ${url}`;
        };
        
        window.copyLink = function() {
            const url = window.location.href;
            
            // Create a temporary input element
            const tempInput = document.createElement('input');
            tempInput.style.position = 'absolute';
            tempInput.style.left = '-1000px';
            tempInput.value = url;
            document.body.appendChild(tempInput);
            
            // Select and copy the link
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // Show a tooltip or notification
            alert("@lang('translation.Copied_to_clipboard')");
        };
    });
</script>
@endsection