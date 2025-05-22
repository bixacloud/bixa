@extends('layouts.master')

@section('title')
    @if(!empty($libraryData))
        {{ $libraryData['name'] ?? 'Library' }} - CDN Library Details
    @else
        CDN Library Search
    @endif
@endsection

@section('css')
<!-- FontAwesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Highlight.js for syntax highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/github.min.css">
<!-- GitHub-style markdown CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown-light.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.min.css">
<style>
    /* Header Section */
    .header-section {
        background: linear-gradient(to right, #4338ca, #312e81);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 1.5rem;
        border-radius: 0.375rem;
        text-align: center;
    }
    
    .header-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: white !important;
        margin-bottom: 1rem;
    }
    
    .header-description {
        font-size: 1.125rem;
        opacity: 0.9;
    }
	
	/* Modern location info styling */
#locationInfo {
    padding: 0;
    border-radius: 0.5rem;
    background-color: #f8f9fa;
    border: none;
    margin-bottom: 1.5rem;
}

.location-card {
    display: flex;
    justify-content: space-between;
    padding: 0;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

/* Left side - Location */
.location-details {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #4c6ef5 0%, #6643b5 100%);
    color: white;
    flex: 1;
}

.location-icon {
    margin-right: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.location-icon img, .location-icon .flag-placeholder {
    width: 32px;
    height: 24px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.location-info {
    display: flex;
    flex-direction: column;
}

.location-primary {
    margin-bottom: 0.25rem;
}

.location-label {
    font-size: 0.8rem;
    text-transform: uppercase;
    opacity: 0.8;
    letter-spacing: 0.5px;
    margin-right: 0.5rem;
}

.location-name {
    font-weight: 600;
    font-size: 1.1rem;
}

.location-secondary {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* Right side - Test info */
.test-details {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    background-color: white;
    color: #333;
}

.test-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f0f4ff;
    margin-right: 1rem;
    color: #4c6ef5;
}

.test-icon i {
    font-size: 1rem;
}

.test-info {
    display: flex;
    flex-direction: column;
}

.test-primary, .test-secondary {
    display: flex;
    align-items: center;
}

.test-primary {
    margin-bottom: 0.25rem;
}

.test-stat {
    font-weight: 700;
    font-size: 1rem;
    color: #4c6ef5;
    margin-right: 0.25rem;
}

.test-label {
    font-size: 0.85rem;
    color: #666;
}

/* Responsive styles */
@media (max-width: 768px) {
    .location-card {
        flex-direction: column;
    }
    
    .location-details, .test-details {
        width: 100%;
    }
    
    .test-details {
        border-top: 1px solid rgba(0,0,0,0.05);
    }
}

    /* Search Container */
    .search-container {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        z-index: 1;
    }
    
    .search-box {
        width: 100%;
        padding: 0.9rem 1rem 0.9rem 2.5rem;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        font-size: 1.1rem;
        background-color: #ffffff;
        color: #111827;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }
    
    .search-box::placeholder {
        color: #9ca3af;
    }
    
    .search-box:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 6px 12px rgba(67, 56, 202, 0.15);
    }
    
    /* Library Details */
    .library-info {
        padding: 1rem 0;
    }
    
    .back-to-search {
        display: inline-flex;
        align-items: center;
        color: #4f46e5;
        margin-bottom: 0.75rem;
        text-decoration: none;
        font-weight: 500;
    }
    
    .back-to-search:hover {
        text-decoration: underline;
    }
    
    .library-header {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    
    .library-title {
        margin-right: 1.5rem;
    }
    
    .library-name {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.375rem;
    }
    
    .library-description {
        color: #4b5563;
        margin-bottom: 0.75rem;
        font-size: 0.9375rem;
    }
    
    /* Library metadata */
    .library-meta {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-top: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    @media (min-width: 640px) {
        .library-meta {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    @media (min-width: 1024px) {
        .library-meta {
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
        }
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #4b5563;
    }
    
    .meta-icon {
        color: #4f46e5;
    }
    
    /* External links */
    .external-links {
        display: flex;
        gap: 10px;
        margin-bottom: 1rem;
    }
    
    .external-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .external-link-github {
        color: #24292e;
        background-color: #f6f8fa;
        border: 1px solid #d0d7de;
    }
    
    .external-link-github:hover {
        background-color: #eaeef2;
    }
    
    .external-link-npm {
        color: #fff;
        background-color: #cb3837;
    }
    
    .external-link-npm:hover {
        background-color: #b02e2c;
    }
    
    .version-selector {
        margin-left: auto;
        width: 200px;
    }
    
    /* CDN Links */
    .cdn-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.75rem;
        margin-bottom: 2rem;
    }
    
    @media (min-width: 768px) {
        .cdn-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    .cdn-column-header {
        font-size: 1.125rem;
        font-weight: 600;
        padding: 1rem 1.25rem;
        background: linear-gradient(to right, #4338ca, #6366f1);
        color: white;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .cdn-column-content {
        border: 1px solid #e5e7eb;
        border-top: none;
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        padding: 1.25rem;
        background-color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .cdn-provider {
        margin-bottom: 1.5rem;
        border: 1px solid #f0f0f0;
        border-radius: 0.5rem;
        padding: 1.25rem;
        transition: all 0.2s;
    }
    
    .cdn-provider:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #e5e7eb;
    }
    
    .cdn-provider:last-child {
        margin-bottom: 0;
    }
    
    .cdn-provider-logo {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px dashed #f0f0f0;
    }
    
    .cdn-provider-logo img {
        max-width: 140px;
        height: 32px;
        object-fit: contain;
    }
    
    .cdn-url {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.875rem;
        background-color: #f9fafb;
        padding: 0.875rem 1rem;
        border-radius: 0.375rem;
        color: #111827;
        margin-bottom: 1rem;
        word-break: break-all;
        border: 1px solid #f0f0f0;
        position: relative;
    }
    
    .cdn-url:hover {
        background-color: #f3f4f6;
    }
    
    .cdn-buttons {
        display: flex;
        gap: 0.75rem;
    }
    
    .cdn-button {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    
    .btn-url {
        background-color: #4f46e5;
        color: white;
    }
    
    .btn-url:hover {
        background-color: #4338ca;
        transform: translateY(-2px);
    }
    
    .btn-html {
        background-color: #ef4444;
        color: white;
    }
    
    .btn-html:hover {
        background-color: #dc2626;
        transform: translateY(-2px);
    }
    
    /* Tabs */
    .tabs {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .tabs-header {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .tab-button {
        padding: 0.75rem 1.5rem;
        border: none;
        background: none;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        position: relative;
    }
    
    .tab-button.active {
        color: #4f46e5;
    }
    
    .tab-button.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #4f46e5;
    }
    
    .tab-content {
        display: none;
        padding: 1.5rem 0;
    }
    
    .tab-content.active {
        display: block;
    }
    
    /* README styling */
    .readme-content {
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        background-color: white;
    }
    
    .markdown-body {
        color: #24292f;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
    }
    
    .markdown-body img {
        max-width: 100%;
    }
    
    /* Files styling */
    .file-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .file-icon {
        margin-right: 0.75rem;
        color: #6b7280;
    }
    
    .file-name {
        flex: 1;
        font-family: monospace;
        font-size: 0.875rem;
    }
    
    .file-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .file-btn {
        width: 2rem;
        height: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
        background-color: white;
        color: #6b7280;
        cursor: pointer;
    }
    
    .file-btn:hover {
        border-color: #4f46e5;
        color: #4f46e5;
    }
    
    /* Popular libraries */
    .popular-libraries {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.25rem;
    }
    
    .library-card {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .library-card:hover {
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
        border-color: #d1d5db;
    }
    
    .library-card-header {
        padding: 1rem;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .library-card-header h5 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
        color: #111827;
    }
    
    .library-card-body {
        padding: 1rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .library-card-description {
        color: #6b7280;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.5;
        height: 3em;
    }
    
    /* Search results */
    .results-container {
        position: absolute;
        left: 0;
        right: 0;
        top: 100%;
        background-color: white;
        border-radius: 0 0 0.375rem 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 10;
        max-height: 300px;
        overflow-y: auto;
        display: none;
    }
    
    /* Result item styling */
    .result-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
    }
    
    .result-item:hover {
        background-color: #f9fafb;
    }
    
    .result-name {
        font-weight: 500;
        color: #111827;
    }
    
    .result-description {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .result-providers {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .result-providers .cdn-badge {
        font-size: 0.7rem;
        padding: 0.15rem 0.4rem;
    }
    
    /* CDN Provider Badges */
    .cdn-provider-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: -0.25rem;
        margin-bottom: 1.25rem;
    }
    
    .cdn-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 500;
        border-radius: 3px;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .cdn-badge-cdnjs {
        background-color: #fff5f1;
        color: #dd6b31;
        border: 1px solid #f8d3c3;
    }
    
    .cdn-badge-jsdelivr {
        background-color: #fff7f2;
        color: #e84d3d;
        border: 1px solid #fdd9d5;
    }
    
    .cdn-badge-unpkg {
        background-color: #f8f8f8;
        color: #333333;
        border: 1px solid #e0e0e0;
    }
    
    .cdn-badge-disabled {
        background-color: #f3f4f6;
        color: #9ca3af;
        border: 1px solid #e5e7eb;
    }
    
    /* Library card footer */
    .library-card-footer {
        padding: 0.75rem;
        background-color: #f9fafb;
        border-top: 1px solid #e5e7eb;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .library-card-footer .cdn-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }

    /* jsDelivr-only version styling */
    option[data-jsdelivr-only="true"] {
        background-color: #fffdf2;
        font-style: italic;
    }
    
    .jsdelivr-only-badge {
        font-size: 0.75rem;
        background-color: #f0ad4e;
        color: white;
        padding: 0.15rem 0.5rem;
        border-radius: 3px;
        margin-left: 0.5rem;
    }
    
    /* CDN Speed Test Styling */
    .speed-test-container .card-header {
        background: linear-gradient(to right, #4338ca, #6366f1);
    }
    
    .speed-result {
        font-weight: 600;
        text-align: center;
    }
    
    .speed-fast {
        color: #10b981;
    }
    
    .speed-medium {
        color: #f59e0b;
    }
    
    .speed-slow {
        color: #ef4444;
    }
    
    .speed-error {
        color: #6b7280;
    }

    /* Flag styling */
    .flag-icon {
        width: 20px;
        height: 15px;
        margin-right: 5px;
        border-radius: 2px;
        vertical-align: middle;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .flag-placeholder {
        display: inline-block;
        width: 20px;
        height: 15px;
        line-height: 15px;
        text-align: center;
        background-color: #f3f4f6;
        border-radius: 2px;
        font-size: 8px;
        margin-right: 5px;
        border: 1px solid rgba(0,0,0,0.1);
        vertical-align: middle;
    }

    /* Provider grouping in results */
    .provider-header {
        background-color: #f9fafb;
    }

    .provider-summary {
        padding: 0.75rem !important;
        font-size: 1.1rem;
        text-align: center !important;
    }

    .provider-summary .badge {
        font-size: 0.85rem;
        font-weight: normal;
        vertical-align: middle;
    }

    .provider-summary img {
        vertical-align: middle;
    }

    /* Animation for loading */
    @keyframes fade-in-out {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }

    .test-loading {
        animation: fade-in-out 1.5s infinite;
    }

    /* Statistics summary */
    .cdn-stats-summary {
        background-color: #f8fafc;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
    }

    .cdn-stats-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .cdn-stats-item:last-child {
        margin-bottom: 0;
    }

    .cdn-stats-icon {
        width: 24px;
        height: 24px;
        background-color: #4338ca;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 12px;
    }

    .cdn-stats-value {
        font-weight: 600;
        margin-left: auto;
    }
    
    /* Provider logo styling */
    .provider-logo {
        height: 24px;
        width: auto;
    }

    /* Status badges */
    .badge-good {
        background-color: #10b981;
        color: white;
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
    }
    
    .badge-bad {
        background-color: #f59e0b;
        color: white;
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
    }
    
    .badge-failed {
        background-color: #ef4444;
        color: white;
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 0.25rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .speed-test-container th:nth-child(2), 
        .speed-test-container td:nth-child(2) {
            max-width: 130px;
        }
        
        .provider-summary .badge {
            display: block;
            margin-top: 0.5rem;
            margin-left: 0 !important;
            width: fit-content;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="header-section">
        <h1 class="header-title">CDN Library Search</h1>
        <p class="header-description">Find and integrate popular JavaScript and CSS libraries into your project</p>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- CSRF Token for AJAX requests -->
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    
                    <!-- Search Container -->
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="librarySearch" class="search-box" 
                               placeholder="Search for JavaScript or CSS libraries (e.g., jquery, bootstrap, vue)"
                               value="{{ $searchQuery ?? '' }}">
                        
                        <div class="results-container" id="searchResults">
                            @if(!empty($searchResults))
                                @foreach($searchResults as $result)
                                    <a href="{{ route('tools.cdn-search', ['library' => $result['name']]) }}" class="text-decoration-none">
                                        <div class="result-item">
                                            <div class="result-name">{{ $result['name'] }}</div>
                                            <div class="result-description">{{ $result['description'] ?? 'No description available' }}</div>
                                            <div class="result-providers">
                                                <span class="cdn-badge cdn-badge-cdnjs">cdnjs</span>
                                                <span class="cdn-badge cdn-badge-jsdelivr">jsDelivr</span>
                                                @if(strpos($result['name'], '/') === false)
                                                <span class="cdn-badge cdn-badge-unpkg">unpkg</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    @if(!empty($libraryData))
                        <!-- Library Info -->
                        <div class="library-info">
                            <a href="{{ route('tools.cdn-search') }}" class="back-to-search">
                                <i class="fas fa-arrow-left me-2"></i> Back to Search
                            </a>
                            
                            <div class="library-header">
                                <div class="library-title">
                                    <h2 class="library-name">{{ $libraryData['name'] ?? $library }}</h2>
                                    <p class="library-description">{{ $libraryData['description'] ?? 'No description available' }}</p>
                                    
                                    <!-- CDN Provider Badges -->
                                    <div class="cdn-provider-badges">
                                        @php
                                            $hasCdnjs = (!isset($libraryData['exists_on_cdnjs']) || $libraryData['exists_on_cdnjs']) &&
                                                    (!empty($libraryData['cdnLinks']['js']) && isset($libraryData['cdnLinks']['js'][0]['cdnjs']) ||
                                                     !empty($libraryData['cdnLinks']['css']) && isset($libraryData['cdnLinks']['css'][0]['cdnjs']));
                                            $hasJsdelivr = !empty($libraryData['cdnLinks']['js']) && isset($libraryData['cdnLinks']['js'][0]['jsdelivr']) || 
                                                          !empty($libraryData['cdnLinks']['css']) && isset($libraryData['cdnLinks']['css'][0]['jsdelivr']);
                                            $hasUnpkg = !empty($libraryData['cdnLinks']['js']) && isset($libraryData['cdnLinks']['js'][0]['unpkg']) || 
                                                       !empty($libraryData['cdnLinks']['css']) && isset($libraryData['cdnLinks']['css'][0]['unpkg']);
                                            $hasAnyProvider = $hasCdnjs || $hasJsdelivr || $hasUnpkg;
                                        @endphp

                                        @if($hasAnyProvider)
                                            @if($hasCdnjs)
                                                <span class="cdn-badge cdn-badge-cdnjs">cdnjs</span>
                                            @endif
                                            
                                            @if($hasJsdelivr)
                                                <span class="cdn-badge cdn-badge-jsdelivr">jsDelivr</span>
                                            @endif
                                            
                                            @if($hasUnpkg)
                                                <span class="cdn-badge cdn-badge-unpkg">unpkg</span>
                                            @endif
                                        @else
                                            <span class="cdn-badge cdn-badge-disabled">No CDN providers available</span>
                                        @endif
                                    </div>
                                    
                                    <!-- External Links -->
                                    <div class="external-links">
                                        @if(!empty($libraryData['github']['url'] ?? $libraryData['github']['path'] ?? null))
                                            <a href="{{ 'https://github.com/' . ($libraryData['github']['path'] ?? '') }}" 
                                               target="_blank" rel="noopener noreferrer" 
                                               class="external-link external-link-github">
                                                <i class="fab fa-github"></i> GitHub
                                            </a>
                                        @endif
                                        
                                        <a href="https://www.npmjs.com/package/{{ $libraryData['name'] }}" 
                                           target="_blank" rel="noopener noreferrer" 
                                           class="external-link external-link-npm">
                                            <i class="fab fa-npm"></i> npm
                                        </a>
                                        
                                        @if(!empty($libraryData['homepage']))
                                            <a href="{{ $libraryData['homepage'] }}" 
                                               target="_blank" rel="noopener noreferrer" 
                                               class="external-link" style="background-color: #0ea5e9; color: white;">
                                                <i class="fas fa-globe"></i> Website
                                            </a>
                                        @endif
                                        
                                        <span class="external-link" style="background-color: #22c55e; color: white;">
                                            <i class="fas fa-check-circle"></i> {{ $libraryData['license'] ?? 'MIT' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Version selector with badge for jsDelivr-only versions -->
                                <div class="version-selector">
                                    <select id="versionSelect" class="form-select">
                                        @php
                                            // Use merged and sorted versions from both sources
                                            $versions = $libraryData['all_versions'] ?? [];
                                            if (empty($versions) && !empty($libraryData['versions'])) {
                                                // Fallback for backward compatibility
                                                $versions = array_map(function($v) {
                                                    return ['version' => $v, 'exists_on_cdnjs' => true];
                                                }, $libraryData['versions']);
                                            }
                                        @endphp
                                        @foreach($versions as $ver)
                                            <option value="{{ $ver['version'] }}" 
                                                {{ $version == $ver['version'] ? 'selected' : '' }}
                                                @if(!$ver['exists_on_cdnjs']) data-jsdelivr-only="true" @endif>
                                                {{ $ver['version'] }} 
                                                @if(!$ver['exists_on_cdnjs']) 
                                                    (jsDelivr only)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Display warning for jsDelivr-only versions -->
                            @if(isset($libraryData['exists_on_cdnjs']) && !$libraryData['exists_on_cdnjs'])
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Version {{ $version }} is only available on jsDelivr and not on CDNJS. 
                                CDN links will be provided for jsDelivr and unpkg, but not for CDNJS.
                            </div>
                            @endif
                            
                            <!-- Library Metadata -->
                            <div class="library-meta">
                                <div class="meta-item">
                                    <i class="fas fa-star meta-icon"></i>
                                    <span>
                                        @if(isset($libraryData['github']['stargazers_count']))
                                            {{ number_format($libraryData['github']['stargazers_count']) }} stars
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="meta-item">
                                    <i class="fas fa-download meta-icon"></i>
                                    <span>
                                        @if(isset($libraryData['npmDownloads']))
                                            {{ number_format($libraryData['npmDownloads']) }} npm downloads
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt meta-icon"></i>
                                    <span>Last update: {{ isset($libraryData['github']['lastUpdate']) ? date('M d, Y', strtotime($libraryData['github']['lastUpdate'])) : 'Unknown' }}</span>
                                </div>
                                
                                <div class="meta-item">
                                    <i class="fas fa-code-branch meta-icon"></i>
                                    <span>
                                        @if(isset($libraryData['github']['forks_count']))
                                            {{ number_format($libraryData['github']['forks_count']) }} forks
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="meta-item">
                                    <i class="fas fa-shield-alt meta-icon"></i>
                                    <span class="cdn-security-badge cdn-security-badge-success">0 known vulnerabilities</span>
                                </div>
                            </div>
                            
                            <!-- CDN Links Grid -->
                            <div class="cdn-grid">
                                <!-- JavaScript Column -->
                                <div>
                                    <div class="cdn-column-header">
                                        <i class="fab fa-js-square me-2"></i> JavaScript
                                    </div>
                                    <div class="cdn-column-content">
                                        @if(!empty($libraryData['cdnLinks']['js']))
                                            @foreach($libraryData['cdnLinks']['js'] as $jsLink)
                                                @if(isset($jsLink['cdnjs']))
                                                <!-- CDNJS Provider -->
                                                <div class="cdn-provider">
                                                    <div class="cdn-provider-logo">
                                                        <img src="{{ URL::asset('/build/images/cdnjs.svg') }}" alt="CDNJS">
                                                    </div>
                                                    
                                                    <div class="cdn-url">{{ $jsLink['cdnjs']['url'] }}</div>
                                                    
                                                    <div class="cdn-buttons">
                                                        <button class="cdn-button btn-url" data-content="{{ $jsLink['cdnjs']['url'] }}">
                                                            <i class="fas fa-copy me-1"></i> Copy URL
                                                        </button>
                                                        <button class="cdn-button btn-html" data-content="{{ $jsLink['cdnjs']['html'] }}">
                                                            <i class="fas fa-code me-1"></i> Copy HTML
                                                        </button>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <!-- jsDelivr Provider - Always shown -->
                                                <div class="cdn-provider">
                                                    <div class="cdn-provider-logo">
                                                        <img src="{{ URL::asset('/build/images/jsdelivr.svg') }}" alt="jsDelivr">
                                                        @if(isset($libraryData['exists_on_cdnjs']) && !$libraryData['exists_on_cdnjs'])
                                                            <span class="jsdelivr-only-badge">only on jsDelivr</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="cdn-url">{{ $jsLink['jsdelivr']['url'] }}</div>
                                                    
                                                    <div class="cdn-buttons">
                                                        <button class="cdn-button btn-url" data-content="{{ $jsLink['jsdelivr']['url'] }}">
                                                            <i class="fas fa-copy me-1"></i> Copy URL
                                                        </button>
                                                        <button class="cdn-button btn-html" data-content="{{ $jsLink['jsdelivr']['html'] }}">
                                                            <i class="fas fa-code me-1"></i> Copy HTML
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Unpkg Provider - For npm packages -->
                                                @if(isset($jsLink['unpkg']))
                                                <div class="cdn-provider">
                                                    <div class="cdn-provider-logo">
                                                        <img src="{{ URL::asset('/build/images/unpkg.png') }}" alt="Unpkg">
                                                    </div>
                                                    
                                                    <div class="cdn-url">{{ $jsLink['unpkg']['url'] }}</div>
                                                    
                                                    <div class="cdn-buttons">
                                                        <button class="cdn-button btn-url" data-content="{{ $jsLink['unpkg']['url'] }}">
                                                            <i class="fas fa-copy me-1"></i> Copy URL
                                                        </button>
                                                        <button class="cdn-button btn-html" data-content="{{ $jsLink['unpkg']['html'] }}">
                                                            <i class="fas fa-code me-1"></i> Copy HTML
                                                        </button>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                @break {{-- Only display the first file --}}
                                            @endforeach
                                        @else
                                            <p class="text-center text-muted py-4">No JavaScript files available for this library.</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- CSS Column -->
                                <div>
                                    <div class="cdn-column-header">
                                        <i class="fab fa-css3-alt me-2"></i> CSS
                                    </div>
                                    <div class="cdn-column-content">
                                        @if(!empty($libraryData['cdnLinks']['css']))
                                            @foreach($libraryData['cdnLinks']['css'] as $cssLink)
                                                @if(isset($cssLink['cdnjs']))
                                                <!-- CDNJS Provider -->
                                                <div class="cdn-provider">
                                                    <div class="cdn-provider-logo">
                                                        <img src="{{ URL::asset('/build/images/cdnjs.svg') }}" alt="CDNJS">
                                                    </div>
                                                    
                                                    <div class="cdn-url">{{ $cssLink['cdnjs']['url'] }}</div>
                                                    
                                                    <div class="cdn-buttons">
                                                        <button class="cdn-button btn-url" data-content="{{ $cssLink['cdnjs']['url'] }}">
                                                            <i class="fas fa-copy me-1"></i> Copy URL
                                                        </button>
                                                        <button class="cdn-button btn-html" data-content="{{ $cssLink['cdnjs']['html'] }}">
                                                            <i class="fas fa-code me-1"></i> Copy HTML
                                                        </button>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <!-- jsDelivr Provider - Always shown -->
                                                <div class="cdn-provider">
                                                    <div class="cdn-provider-logo">
                                                        <img src="{{ URL::asset('/build/images/jsdelivr.svg') }}" alt="jsDelivr">
                                                        @if(isset($libraryData['exists_on_cdnjs']) && !$libraryData['exists_on_cdnjs'])
                                                            <span class="jsdelivr-only-badge">only on jsDelivr</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="cdn-url">{{ $cssLink['jsdelivr']['url'] }}</div>
                                                    
                                                    <div class="cdn-buttons">
                                                        <button class="cdn-button btn-url" data-content="{{ $cssLink['jsdelivr']['url'] }}">
                                                            <i class="fas fa-copy me-1"></i> Copy URL
                                                        </button>
                                                        <button class="cdn-button btn-html" data-content="{{ $cssLink['jsdelivr']['html'] }}">
                                                            <i class="fas fa-code me-1"></i> Copy HTML
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Unpkg Provider - For npm packages -->
                                                @if(isset($cssLink['unpkg']))
                                                <div class="cdn-provider">
                                                    <div class="cdn-provider-logo">
                                                        <img src="{{ URL::asset('/build/images/unpkg.png') }}" alt="Unpkg">
                                                    </div>
                                                    
                                                    <div class="cdn-url">{{ $cssLink['unpkg']['url'] }}</div>
                                                    
                                                    <div class="cdn-buttons">
                                                        <button class="cdn-button btn-url" data-content="{{ $cssLink['unpkg']['url'] }}">
                                                            <i class="fas fa-copy me-1"></i> Copy URL
                                                        </button>
                                                        <button class="cdn-button btn-html" data-content="{{ $cssLink['unpkg']['html'] }}">
                                                            <i class="fas fa-code me-1"></i> Copy HTML
                                                        </button>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                @break {{-- Only display the first file --}}
                                            @endforeach
                                        @else
                                            <p class="text-center text-muted py-4">No CSS files available for this library.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- CDN Speed Test Section -->
                            @if(!empty($libraryData) && !empty($libraryData['cdnLinks']) && (count($libraryData['cdnLinks']['js']) > 0 || count($libraryData['cdnLinks']['css']) > 0))
                            <div class="speed-test-container mt-4 mb-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 style="color: white !important;" class="mb-0">
                                            <i class="fas fa-tachometer-alt me-2"></i> Global CDN Speed Test
                                            <span class="float-end">
                                                <button type="button" class="btn btn-sm btn-light" id="runSpeedTest">
                                                    <i class="fas fa-play me-1"></i> Run Test
                                                </button>
                                            </span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i> This test measures connection speeds to multiple CDN edge servers around the world to help you choose the fastest provider for your location. Tests are run from the server side for more accurate results.
                                        </div>
                                        
                                        <!-- Store library and version for JavaScript -->
                                        <input type="hidden" id="libraryName" value="{{ $library ?? '' }}">
                                        <input type="hidden" id="versionValue" value="{{ $version ?? '' }}">
                                        
                                        <div id="speedTestLoading" class="text-center py-3 d-none">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2" id="testProgressText">Testing CDN speeds...</p>
                                            
                                            <!-- Progress bar -->
                                            <div class="progress mt-3">
                                                <div id="testProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                                     role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        
                                        <div id="speedTestResults" class="d-none">
                                            <div class="alert alert-secondary mt-3 mb-3" id="locationInfo">
                                                <!-- Location info will be displayed here -->
                                            </div>
                                            
                                            <!-- Stats summary will be inserted here -->
                                            <div id="cdn-stats-summary" class="cdn-stats-summary d-none"></div>
                                            
                                            <h6 class="mb-3">Results from global CDN edge servers:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Edge Location</th>
                                                            <th>Response Time</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="speedTestResultsBody">
                                                        <!-- Results will be displayed here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> Results reflect network conditions between our server and CDN edges. Tests performed using HEAD requests to measure initial connection latency.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Readme/Files Tabs -->
                            <div class="tabs">
                                <div class="tabs-header">
                                    <button type="button" class="tab-button active" data-tab="readme">Readme</button>
                                    <button type="button" class="tab-button" data-tab="files">Files</button>
                                </div>
                                
                                <!-- Readme Tab -->
                                <div id="readme" class="tab-content active">
                                    <div class="readme-content">
                                        <div class="markdown-body">
                                            @if(!empty($libraryData['parsedReadme']))
                                                {!! $libraryData['parsedReadme'] !!}
                                            @elseif(!empty($libraryData['readme']))
                                                <pre>{{ $libraryData['readme'] }}</pre>
                                            @else
                                                <div class="alert alert-info">README not available or couldn't be loaded.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Files Tab -->
                                <div id="files" class="tab-content">
                                    <div class="card">
                                        <div class="card-body p-0">
                                            @if(!empty($libraryData['versionFiles']))
                                                @foreach($libraryData['versionFiles'] as $file)
                                                    <div class="file-item">
                                                        <div class="file-icon">
                                                            @php
                                                                $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                                $iconClass = 'fa-file';
                                                                
                                                                if ($extension === 'js') $iconClass = 'fa-js';
                                                                elseif ($extension === 'css') $iconClass = 'fa-css3';
                                                                elseif ($extension === 'map') $iconClass = 'fa-map';
                                                                elseif (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'svg'])) 
                                                                    $iconClass = 'fa-image';
                                                            @endphp
                                                            <i class="fab {{ $iconClass }}"></i>
                                                        </div>
                                                        <div class="file-name">{{ $file }}</div>
                                                        <div class="file-actions">
                                                            @php
                                                                $fileUrl = '';
                                                                
                                                                if (isset($libraryData['source']) && $libraryData['source'] === 'jsdelivr') {
                                                                    // If the file is from jsdelivr
                                                                    $isGitHubRepo = strpos($library, '/') !== false;
                                                                    if ($isGitHubRepo) {
                                                                        list($owner, $repo) = explode('/', $library);
                                                                        $fileUrl = "https://cdn.jsdelivr.net/gh/{$library}@{$version}/{$file}";
                                                                    } else {
                                                                        $fileUrl = "https://cdn.jsdelivr.net/npm/{$library}@{$version}/{$file}";
                                                                    }
                                                                } else {
                                                                    // Default to CDNJS
                                                                    $fileUrl = "https://cdnjs.cloudflare.com/ajax/libs/{$library}/{$version}/{$file}";
                                                                }
                                                            @endphp
                                                            
                                                            <button type="button" class="file-btn" 
                                                                    data-content="{{ $fileUrl }}" 
                                                                    title="Copy URL">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                            
                                                            <a href="{{ $fileUrl }}" 
                                                               target="_blank" rel="noopener noreferrer" 
                                                               class="file-btn" 
                                                               title="Open in new tab">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="p-4 text-center text-muted">No files available for this version.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Popular Libraries Section -->
                        <div class="mt-4">
                            <h3 class="mb-4">Popular Libraries</h3>
                            <div class="popular-libraries">
                                @foreach($popularLibraries as $lib)
                                    <div class="library-card-wrapper">
                                        @php
                                            // Fix for lodash.js and moment.js packages
                                            $libraryName = $lib['name'];
                                            if (in_array($libraryName, ['lodash', 'moment'])) {
                                                $libraryName .= '.js';
                                            }
                                        @endphp
                                        <a href="{{ route('tools.cdn-search', ['library' => $libraryName]) }}" class="text-decoration-none">
                                            <div class="library-card">
                                                <div class="library-card-header">
                                                    <h5>{{ $lib['name'] }}</h5>
                                                </div>
                                                <div class="library-card-body">
                                                    <p class="library-card-description">{{ $lib['description'] }}</p>
                                                </div>
                                                <div class="library-card-footer">
                                                    <span class="cdn-badge cdn-badge-cdnjs">cdnjs</span>
                                                    <span class="cdn-badge cdn-badge-jsdelivr">jsDelivr</span>
                                                    @if(strpos($lib['name'], '/') === false)
                                                    <span class="cdn-badge cdn-badge-unpkg">unpkg</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.21.0/dist/sweetalert2.all.min.js"></script>

<!-- Highlight.js for syntax highlighting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>

<!-- CDN Speed Test JavaScript -->
<script>
/**
 * Country Flags Helper Functions
 */

/**
 * Get country flag HTML using local images
 * @param {String} countryCode - Two-letter country code
 * @returns {String} - HTML for the country flag
 */
function getCountryFlag(countryCode) {
    // Convert country code to lowercase
    const code = (countryCode || 'xx').toLowerCase();
    
    // Map of special cases for certain country codes
    const specialCases = {
        'uk': 'gb', // United Kingdom is 'gb' in flag icons
        'lo': null, // Local network - no flag
        'xx': null  // Unknown - no flag
    };
    
    // If this is a special case with no flag
    if (specialCases[code] === null) {
        return `<span class="flag-placeholder">${countryCode}</span>`;
    }
    
    // Convert full country names to ISO codes if necessary
    if (code.length > 2) {
        const isoCode = getISOCodeFromCountryName(code);
        if (isoCode) {
            return getCountryFlag(isoCode); // Recursive call with the ISO code
        }
    }
    
    // Use the mapped code if it exists, otherwise use the original
    const flagCode = specialCases[code] || code;
    
    // Now use local flag images from build/images/flags directory
    return `<img src="/build/images/flags/${flagCode}.png" 
               class="flag-icon" alt="${countryCode}" 
               title="${getCountryName(countryCode)}">`;
}

/**
 * Convert full country name to ISO code
 * @param {String} countryName - Full country name
 * @returns {String} - Two-letter ISO country code or null if not found
 */
function getISOCodeFromCountryName(countryName) {
    if (!countryName) return null;
    
    // Remove spaces and convert to lowercase for better matching
    const normalizedName = countryName.toLowerCase().trim();
    
    // Map of country names to ISO codes
    const countryMap = {
        'united states': 'us',
        'vietnam': 'vn',
        'united kingdom': 'gb',
        'germany': 'de',
        'france': 'fr',
        'japan': 'jp',
        'china': 'cn',
        'india': 'in',
        'brazil': 'br',
        'russia': 'ru',
        'canada': 'ca',
        'australia': 'au',
        'italy': 'it',
        'spain': 'es',
        'mexico': 'mx',
        'indonesia': 'id',
        'turkey': 'tr',
        'netherlands': 'nl',
        'saudi arabia': 'sa',
        'switzerland': 'ch',
        'poland': 'pl',
        'thailand': 'th',
        'sweden': 'se',
        'belgium': 'be',
        'nigeria': 'ng',
        'argentina': 'ar',
        'austria': 'at',
        'hong kong': 'hk',
        'singapore': 'sg',
        'malaysia': 'my',
        'philippines': 'ph',
        'ireland': 'ie',
        'denmark': 'dk',
        'south africa': 'za',
        'south korea': 'kr',
        'norway': 'no',
        'finland': 'fi',
        'local': 'lo',
        'unknown': 'xx'
        // Add more countries as needed
    };
    
    return countryMap[normalizedName] || null;
}

/**
 * Get country name from code
 * @param {String} countryCode - Two-letter country code
 * @returns {String} - Country name
 */
function getCountryName(countryCode) {
    if (!countryCode) return 'Unknown';
    
    // Map of country codes to names
    const countryNames = {
        'us': 'United States',
        'uk': 'United Kingdom',
        'gb': 'United Kingdom',
        'de': 'Germany',
        'jp': 'Japan',
        'hk': 'Hong Kong',
        'au': 'Australia',
        'sg': 'Singapore',
        'in': 'India',
        'br': 'Brazil',
        'ca': 'Canada',
        'fr': 'France',
        'it': 'Italy',
        'es': 'Spain',
        'ru': 'Russia',
        'cn': 'China',
        'kr': 'South Korea',
        'nl': 'Netherlands',
        'se': 'Sweden',
        'ch': 'Switzerland',
        'be': 'Belgium',
        'mx': 'Mexico',
        'za': 'South Africa',
        'pl': 'Poland',
        'tr': 'Turkey',
        'ar': 'Argentina',
        'id': 'Indonesia',
        'th': 'Thailand',
        'my': 'Malaysia',
        'vn': 'Vietnam',
        'ph': 'Philippines',
        'lo': 'Local Network',
        'xx': 'Unknown'
        // Add more as needed
    };
    
    return countryNames[countryCode.toLowerCase()] || countryCode.toUpperCase();
}

/**
 * Count total CDN nodes from all providers
 * 
 * @param {Object} providers - CDN providers object
 * @return {Number} - Total number of nodes
 */
function getCdnNodeCount(providers) {
    let count = 0;
    for (const provider of Object.values(providers || {})) {
        if (provider.nodes && Array.isArray(provider.nodes)) {
            count += provider.nodes.length;
        }
    }
    return count;
}

/**
 * Update the table structure to show only 3 columns
 */
function updateTableStructure() {
    const table = document.querySelector('.speed-test-container table');
    if (!table) return;
    
    const thead = table.querySelector('thead');
    if (!thead) return;
    
    // Replace the header row
    thead.innerHTML = `
        <tr>
            <th>Edge Location</th>
            <th>Response Time</th>
            <th>Status</th>
        </tr>
    `;
}

/**
 * Get CSS class for speed result with safe handling of undefined values
 * @param {Object} result - Test result
 * @returns {String} - CSS class
 */
function getSpeedClass(result) {
    if (!result || result.status !== 'success') return 'speed-error';
    if (!result.time) return 'speed-error';
    if (result.time < 150) return 'speed-fast';
    if (result.time < 400) return 'speed-medium';
    return 'speed-slow';
}

/**
 * Get the status badge HTML based on response time
 * @param {Object} result - Test result
 * @returns {String} - HTML for the status badge
 */
function getStatusBadge(result) {
    if (!result || result.status !== 'success') {
        return '<span class="badge badge-failed">Failed</span>';
    }
    
    // Classify response speed
    if (!result.time) {
        return '<span class="badge badge-failed">Failed</span>';
    } else if (result.time < 200) {
        return '<span class="badge badge-good">Good</span>';
    } else if (result.time < 500) {
        return '<span class="badge badge-bad">Bad</span>';
    } else {
        return '<span class="badge badge-failed">Failed</span>';
    }
}

/**
 * Get formatted text for speed result
 * @param {Object} result - Test result
 * @returns {String} - Formatted text
 */
function getSpeedText(result) {
    if (!result || result.status !== 'success' || !result.time) {
        return 'Connection failed';
    }
    return `${result.time} ms`;
}

/**
 * Update progress bar and text
 *
 * @param {Number} percent - Progress percentage (0-100)
 * @param {String} message - Progress message
 */
function updateProgress(percent, message) {
    const progressBar = document.getElementById('testProgressBar');
    const progressText = document.getElementById('testProgressText');
    
    if (progressBar) {
        progressBar.style.width = `${percent}%`;
        progressBar.setAttribute('aria-valuenow', percent);
    }
    
    if (progressText && message) {
        progressText.textContent = message;
    }
}

/**
 * Show error alert using SweetAlert2
 * @param {String} message - Error message to display
 */
function showErrorAlert(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Error',
            text: message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } else {
        alert(message);
    }
}

/**
 * Display user location information with modern design and error handling
 * 
 * @param {Object} locationInfo - User location information from the server
 */
function displayUserLocation(locationInfo) {
    const locationInfoElement = document.getElementById('locationInfo');
    if (!locationInfoElement) return;
    
    try {
        if (!locationInfo) throw new Error('No location information provided');
        
        const countryCode = (locationInfo.location?.country_code || locationInfo.location?.countryCode || 'XX').toUpperCase();
        const country = locationInfo.location?.country || 'Unknown';
        const city = locationInfo.location?.city || 'Unknown';
        const ip = locationInfo.ip || 'Unknown';
        
        // Count providers and servers
        const providerCount = Object.keys(window.speedTestData?.cdn_providers || {}).length;
        const serverCount = getCdnNodeCount(window.speedTestData?.cdn_providers || {});
        
        // Create modern card design
        locationInfoElement.innerHTML = `
            <div class="location-card">
                <div class="location-details">
                    <div class="location-icon">
                        ${getCountryFlag(countryCode)}
                    </div>
                    <div class="location-info">
                        <div class="location-primary">
                            <span class="location-label">Your Location</span>
                            <span class="location-name">${city !== 'Unknown' ? city + ', ' : ''}${country}</span>
                        </div>
                        <div class="location-secondary">
                            <span class="location-ip">IP: ${ip}</span>
                        </div>
                    </div>
                </div>
                <div class="test-details">
                    <div class="test-icon">
                        <i class="fas fa-server"></i>
                    </div>
                    <div class="test-info">
                        <div class="test-primary">
                            <span class="test-stat">${providerCount}</span>
                            <span class="test-label">CDN Providers</span>
                        </div>
                        <div class="test-secondary">
                            <span class="test-stat">${serverCount}</span>
                            <span class="test-label">Edge Servers</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error displaying user location:', error);
        locationInfoElement.innerHTML = `
            <div class="location-card">
                <div class="location-details">
                    <div class="location-icon">
                        <span class="flag-placeholder">?</span>
                    </div>
                    <div class="location-info">
                        <div class="location-primary">
                            <span class="location-label">Your Location</span>
                            <span class="location-name">Unknown</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

/**
 * Calculate and display summary statistics for test results with error handling
 * @param {Array} results - Array of test results
 * @returns {Object} - Statistics summary
 */
function calculateStatsSummary(results) {
    try {
        // Safety check for results
        if (!Array.isArray(results) || results.length === 0) {
            return {
                fastest: null,
                slowest: null,
                average: null,
                medianResponse: null,
                successRate: 0
            };
        }
        
        // Only consider successful tests
        const successfulResults = results.filter(r => r && r.status === 'success');
        
        if (successfulResults.length === 0) {
            return {
                fastest: null,
                slowest: null,
                average: null,
                medianResponse: null,
                successRate: 0
            };
        }
        
        // Sort by response time
        const sortedResults = [...successfulResults].sort((a, b) => (a.time || 0) - (b.time || 0));
        
        // Calculate fastest and slowest
        const fastest = sortedResults[0];
        const slowest = sortedResults[sortedResults.length - 1];
        
        // Calculate average response time
        const totalTime = successfulResults.reduce((sum, result) => sum + (result.time || 0), 0);
        const averageTime = Math.round(totalTime / successfulResults.length);
        
        // Calculate median response time
        const medianIndex = Math.floor(sortedResults.length / 2);
        const medianResponse = sortedResults.length % 2 === 0
            ? Math.round(((sortedResults[medianIndex - 1].time || 0) + (sortedResults[medianIndex].time || 0)) / 2)
            : sortedResults[medianIndex].time || 0;
        
        // Calculate success rate
        const successRate = Math.round((successfulResults.length / results.length) * 100);
        
        // Group by CDN provider
        const providerStats = {};
        for (const result of successfulResults) {
            const provider = result.provider || 'unknown';
            if (!providerStats[provider]) {
                providerStats[provider] = [];
            }
            providerStats[provider].push(result);
        }
        
        // Find fastest provider
        let fastestProvider = 'unknown';
        let fastestProviderTime = Infinity;
        
        for (const [provider, providerResults] of Object.entries(providerStats)) {
            if (providerResults.length === 0) continue;
            
            const avgTime = providerResults.reduce((sum, r) => sum + (r.time || 0), 0) / providerResults.length;
            if (avgTime < fastestProviderTime) {
                fastestProviderTime = avgTime;
                fastestProvider = provider;
            }
        }
        
        return {
            fastest,
            slowest,
            averageTime,
            medianResponse,
            successRate,
            fastestProvider,
            fastestProviderTime: Math.round(fastestProviderTime),
            totalTests: results.length,
            successfulTests: successfulResults.length
        };
    } catch (error) {
        console.error('Error calculating statistics:', error);
        return {
            fastest: null,
            slowest: null,
            average: null,
            medianResponse: null,
            successRate: 0,
            error: error.message
        };
    }
}

/**
 * Display statistics summary in UI
 * @param {Object} stats - Statistics object
 */
function displayStatisticsSummary(stats) {
    try {
        // Create the summary element if it doesn't exist
        let summaryElement = document.getElementById('cdn-stats-summary');
        if (!summaryElement) {
            summaryElement = document.createElement('div');
            summaryElement.id = 'cdn-stats-summary';
            summaryElement.className = 'cdn-stats-summary mt-3 mb-3';
            
            // Insert after the locationInfo element
            const locationInfo = document.getElementById('locationInfo');
            if (locationInfo && locationInfo.parentNode) {
                locationInfo.parentNode.insertBefore(summaryElement, locationInfo.nextSibling);
            }
        }
        
        // If we couldn't calculate stats, show an error
        if (!stats || !stats.fastest) {
            summaryElement.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No successful tests completed. Please try again.
                </div>
            `;
            summaryElement.classList.remove('d-none');
            return;
        }
        
        // Generate HTML for the summary
        summaryElement.innerHTML = `
            <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i> Performance Summary</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="cdn-stats-item">
                        <div class="cdn-stats-icon"><i class="fas fa-bolt"></i></div>
                        <div>Fastest Response</div>
                        <div class="cdn-stats-value speed-fast">${stats.fastest.time} ms</div>
                    </div>
                    <div class="cdn-stats-item">
                        <div class="cdn-stats-icon"><i class="fas fa-stopwatch"></i></div>
                        <div>Slowest Response</div>
                        <div class="cdn-stats-value speed-slow">${stats.slowest.time} ms</div>
                    </div>
                    <div class="cdn-stats-item">
                        <div class="cdn-stats-icon"><i class="fas fa-calculator"></i></div>
                        <div>Average Response</div>
                        <div class="cdn-stats-value">${stats.averageTime} ms</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="cdn-stats-item">
                        <div class="cdn-stats-icon"><i class="fas fa-trophy"></i></div>
                        <div>Fastest Provider</div>
                        <div class="cdn-stats-value speed-fast">${stats.fastestProvider} (avg: ${stats.fastestProviderTime} ms)</div>
                    </div>
                    <div class="cdn-stats-item">
                        <div class="cdn-stats-icon"><i class="fas fa-check-circle"></i></div>
                        <div>Success Rate</div>
                        <div class="cdn-stats-value">${stats.successRate}% (${stats.successfulTests}/${stats.totalTests})</div>
                    </div>
                    <div class="cdn-stats-item">
                        <div class="cdn-stats-icon"><i class="fas fa-sort-numeric-down"></i></div>
                        <div>Median Response</div>
                        <div class="cdn-stats-value">${stats.medianResponse} ms</div>
                    </div>
                </div>
            </div>
        `;
        
        summaryElement.classList.remove('d-none');
    } catch (error) {
        console.error('Error displaying statistics summary:', error);
    }
}

/**
 * Display speed test results in the table with error handling for missing properties
 * @param {Array} results - Array of test results
 */
function displaySpeedTestResults(results) {
    try {
        const resultsBody = document.getElementById('speedTestResultsBody');
        if (!resultsBody) return;
        
        // Safety check for results
        if (!Array.isArray(results)) {
            console.error('Results is not an array:', results);
            resultsBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Invalid results format</td></tr>';
            return;
        }
        
        // Clear previous results
        resultsBody.innerHTML = '';
        
        // Group results by provider
        const resultsByProvider = {};
        results.forEach(result => {
            if (!result) return;
            
            // Ensure provider exists or use 'unknown'
            const provider = result.provider || 'unknown';
            if (!resultsByProvider[provider]) {
                resultsByProvider[provider] = [];
            }
            resultsByProvider[provider].push(result);
        });
        
        // Process each provider and add their nodes
        for (const [provider, providerResults] of Object.entries(resultsByProvider)) {
            // Find the fastest node for this provider - ONLY from successful results
            const successfulResults = providerResults.filter(r => r && r.status === 'success');
            const fastestResult = successfulResults.length > 0 ? 
                successfulResults.reduce((fastest, current) => {
                    if (!fastest || ((fastest.time || Infinity) > (current.time || Infinity))) return current;
                    return fastest;
                }, null) : null;
            
            // Calculate percentage of "bad" results (>=200ms)
            const badResultsCount = successfulResults.filter(r => (r.time || 0) >= 200).length;
            const badResultsPercentage = successfulResults.length > 0 ? 
                (badResultsCount / successfulResults.length) * 100 : 0;
            
            // Calculate percentage of "fail" results
            const failResultsCount = providerResults.filter(r => !r || r.status !== 'success').length;
            const failResultsPercentage = providerResults.length > 0 ?
                (failResultsCount / providerResults.length) * 100 : 0;
            
            // Add a provider group header
            const headerRow = document.createElement('tr');
            headerRow.className = 'table-light provider-header';
            
            // Get provider name and logo
            const providerName = providerResults[0]?.provider_name || provider;
            const logoUrl = providerResults[0]?.logo || `/build/images/${provider}.${provider === 'jsdelivr' || provider === 'cdnjs' ? 'svg' : 'png'}`;
            
            // Different header based on whether we have successful results
            if (fastestResult) {
                // Ensure node object exists before accessing properties
                const nodeCountry = fastestResult.node?.country || 'XX'; 
                const nodeLocation = fastestResult.node?.location || 'Unknown';
                
                // Use appropriate message and style based on response time and result distribution
                let badgeClass = "bg-primary";
                let message = "Fastest";
                
                // Check for "Not Connected" condition
                if ((fastestResult.time || 0) >= 500 || (failResultsPercentage > 60 && (fastestResult.time || 0) >= 200)) {
                    badgeClass = "bg-danger";
                    message = "Not Connected";
                }
                // Check for "All Slow" condition
                else if ((fastestResult.time || 0) >= 200 && (fastestResult.time || 0) < 500 || badResultsPercentage > 60) {
                    badgeClass = "bg-warning text-dark";
                    message = "All Slow";
                }
                
                headerRow.innerHTML = `
                    <td colspan="3" class="provider-summary text-center">
                        <img src="${logoUrl}" alt="${providerName}" height="28" class="me-2">
                        <span class="badge ms-2 ${badgeClass}">
                            ${message}: ${getCountryFlag(nodeCountry)} ${nodeLocation} (${fastestResult.time} ms)
                        </span>
                    </td>
                `;
            } else {
                // Show "All tests failed" when there are no successful tests
                headerRow.innerHTML = `
                    <td colspan="3" class="provider-summary text-center">
                        <img src="${logoUrl}" alt="${providerName}" height="28" class="me-2">
                        <span class="badge bg-danger">All tests failed</span>
                    </td>
                `;
            }
            
            resultsBody.appendChild(headerRow);
            
            // Add each node result
            providerResults.forEach(result => {
                if (!result) return;
                
                const row = document.createElement('tr');
                row.className = (result === fastestResult) ? 'table-success' : '';
                
                // Ensure node object exists before accessing properties
                const nodeCountry = result.node?.country || 'XX';
                const nodeLocation = result.node?.location || 'Unknown';
                const nodeIp = result.node?.reference_ip || result.node?.ip || result.ip || 'Unknown';
                
                // Create the row with 3 columns
                row.innerHTML = `
                    <td>
                        ${getCountryFlag(nodeCountry)} ${nodeLocation} 
                        <br><small class="text-muted">${nodeIp}</small>
                    </td>
                    <td class="speed-result ${getSpeedClass(result)}">
                        ${getSpeedText(result)}
                    </td>
                    <td>
                        ${getStatusBadge(result)}
                    </td>
                `;
                
                resultsBody.appendChild(row);
            });
        }
    } catch (error) {
        console.error('Error displaying speed test results:', error);
        const resultsBody = document.getElementById('speedTestResultsBody');
        if (resultsBody) {
            resultsBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        Error displaying results: ${error.message}
                    </td>
                </tr>
            `;
        }
    }
}

// Global test counter
window.testCount = 0;

/**
 * Run CDN speed test from server-side with improved error handling
 */
function runCdnSpeedTest() {
    try {
        const speedTestContainer = document.querySelector('.speed-test-container');
        if (speedTestContainer) {
            speedTestContainer.scrollIntoView({ behavior: 'smooth' });
        }
        
        // Increment test count
        window.testCount = (window.testCount || 0) + 1;
        console.log(`Running test #${window.testCount}`);
        
        // Update table structure first
        updateTableStructure();
        
        // Show loading indicator
        const speedTestLoading = document.getElementById('speedTestLoading');
        const speedTestResults = document.getElementById('speedTestResults');
        
        if (!speedTestLoading || !speedTestResults) {
            console.error('Missing UI elements: speedTestLoading or speedTestResults');
            return;
        }
        
        // Remove Test Again container if exists
        const testAgainContainer = document.getElementById('testAgainContainer');
        if (testAgainContainer) {
            testAgainContainer.remove();
        }
        
        speedTestLoading.classList.remove('d-none');
        speedTestResults.classList.add('d-none');
        
        // Hide stats summary
        const statsSummary = document.getElementById('cdn-stats-summary');
        if (statsSummary) {
            statsSummary.classList.add('d-none');
        }
        
        // Clear previous results
        const resultsBody = document.getElementById('speedTestResultsBody');
        const locationInfo = document.getElementById('locationInfo');
        
        if (!resultsBody || !locationInfo) {
            console.error('Missing UI elements: speedTestResultsBody or locationInfo');
            speedTestLoading.classList.add('d-none');
            return;
        }
        
        resultsBody.innerHTML = '';
        locationInfo.innerHTML = '';
        
        // Get the library and version information from the page
        const library = document.querySelector('input#libraryName')?.value || 'jquery';
        const version = document.querySelector('input#versionValue')?.value || document.querySelector('select#versionSelect')?.value || '3.6.0';
        
        // Get CSRF token - handle multiple potential sources of csrf token
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            csrfToken = document.querySelector('input[name="_token"]')?.value;
        }
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            showErrorAlert('CSRF token not found. Please reload the page and try again.');
            speedTestLoading.classList.add('d-none');
            return;
        }
        
        // Debugging information
        console.log('Starting server-side CDN speed test...');
        console.log(`Testing library: ${library}, version: ${version}`);
        
        // First step: Get edge servers information
        updateProgress(0, 'Fetching CDN edge server information...');
        
        // Setup the request with proper error handling
        const fetchOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ library, version })
        };
        
        // Add timeout to fetch
        const controller = new AbortController();
        const signal = controller.signal;
        const timeout = setTimeout(() => controller.abort(), 10000); // 10 second timeout
        
        fetch('/tools/cdn-search/edge-servers', {
            ...fetchOptions,
            signal
        })
        .then(response => {
            clearTimeout(timeout);
            console.log('Server response status:', response.status);
            
            if (!response.ok) {
                if (response.status === 419) {
                    throw new Error('CSRF token mismatch - Please reload the page');
                } else if (response.status === 500) {
                    throw new Error('Internal server error - Please try again later');
                } else {
                    throw new Error(`Network error: ${response.status}`);
                }
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Edge servers data received:', data);
            
            if (!data || !data.success) {
                throw new Error(data?.message || 'Unknown error getting server information');
            }
            
            // Save data in global scope for use in other functions
            window.speedTestData = data;
            
            // Display user location information
            displayUserLocation(data.user_location);
            
            // Second step: Run the actual speed test
            updateProgress(30, 'Running server-side CDN speed tests...');
            
            return fetch('/tools/cdn-search/run-speed-test', {
                ...fetchOptions,
                signal: (new AbortController()).signal
            });
        })
        .then(response => {
            console.log('Speed test response status:', response.status);
            
            if (!response.ok) {
                if (response.status === 419) {
                    throw new Error('CSRF token mismatch - Please reload the page');
                } else if (response.status === 500) {
                    throw new Error('Internal server error - Please try again later');
                } else {
                    throw new Error(`Network error: ${response.status}`);
                }
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Speed test data received:', data);
            
            if (!data || !data.success) {
                throw new Error(data?.message || 'Unknown error running speed test');
            }
            
            updateProgress(100, 'Processing test results...');
            
            // Use the improved extractTestResults function
            const results = extractTestResults(data);
            
            // Calculate statistics from results
            try {
                const stats = calculateStatsSummary(results);
                displayStatisticsSummary(stats);
            } catch (statsError) {
                console.error('Error calculating statistics:', statsError);
                // Continue execution even if stats calculation fails
            }
            
            // Display results
            displaySpeedTestResults(results);
            
            // Hide loading indicator
            speedTestLoading.classList.add('d-none');
            speedTestResults.classList.remove('d-none');
            
            // Add Test Again button if we haven't reached the limit
            if (window.testCount < 3) {
                const buttonContainer = document.createElement('div');
                buttonContainer.id = 'testAgainContainer';
                buttonContainer.className = 'text-center mt-3 mb-3';
                
                // Show remaining tests count
                const remainingTests = 3 - window.testCount;
                
                buttonContainer.innerHTML = `
                    <button type="button" class="btn btn-primary" id="testAgainButton">
                        <i class="fas fa-redo me-1"></i> Test Again <span class="badge bg-light text-dark ms-1">${remainingTests} left</span>
                    </button>
                `;
                
                speedTestResults.appendChild(buttonContainer);
                
                // Add event listener to the button
                setTimeout(() => {
                    document.getElementById('testAgainButton')?.addEventListener('click', runCdnSpeedTest);
                }, 100);
            } else {
                // Show message that max tests reached
                const messageContainer = document.createElement('div');
                messageContainer.id = 'testAgainContainer';
                messageContainer.className = 'text-center mt-3 mb-3';
                messageContainer.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Maximum test limit reached. Refresh the page to run more tests.
                    </div>
                `;
                
                speedTestResults.appendChild(messageContainer);
            }
        })
        .catch(error => {
            clearTimeout(timeout);
            console.error('Error in CDN speed test:', error);
            speedTestLoading.classList.add('d-none');
            showErrorAlert('Unable to run CDN speed test: ' + error.message);
            
            // Even on error, count it as a test attempt
            if (window.testCount >= 3) {
                const errorContainer = document.createElement('div');
                errorContainer.id = 'testAgainContainer';
                errorContainer.className = 'text-center mt-3 mb-3';
                errorContainer.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Maximum test limit reached. Refresh the page to run more tests.
                    </div>
                `;
                speedTestResults.appendChild(errorContainer);
            }
        });
    } catch (outerError) {
        console.error('Unexpected error in runCdnSpeedTest:', outerError);
        showErrorAlert('An unexpected error occurred: ' + outerError.message);
    }
}

/**
 * Extract test results from response data with improved error handling
 * for the specific structure returned by CDNSpeedTestAdapter
 * 
 * @param {Object} data - Response data from server
 * @returns {Array} - Array of test results
 */
function extractTestResults(data) {
    console.log('Extracting test results from data:', data);
    
    if (!data || !data.test_results) {
        console.error('No test_results in data:', data);
        return [];
    }
    
    // Debug the structure of test_results
    console.log('Test results structure:', data.test_results);
    console.log('Test results keys:', Object.keys(data.test_results));
    
    let results = [];
    
    // The CDNSpeedTestAdapter specifically puts the results in test_results.results
    if (data.test_results.results && Array.isArray(data.test_results.results)) {
        console.log('Found results in data.test_results.results with length:', data.test_results.results.length);
        results = data.test_results.results;
    } else {
        console.warn('Results not found in expected location');
        
        // Try to find results in any array property
        for (const key in data.test_results) {
            if (Array.isArray(data.test_results[key]) && 
                data.test_results[key].length > 0 && 
                typeof data.test_results[key][0] === 'object' && 
                data.test_results[key][0].hasOwnProperty('location')) {
                
                console.log(`Found results array in data.test_results.${key}`);
                results = data.test_results[key];
                break;
            }
        }
    }
    
    // Create fallback if still empty
    if (!results || results.length === 0) {
        console.warn('Creating fallback results array');
        
        results = [{
            location: 'Error: No results found',
            country: 'XX',
            ip: '0.0.0.0',
            region: 'Unknown',
            provider: 'unknown',
            provider_name: 'Unknown',
            status: 'fail',
            time: null,
            error: 'No test results were returned in the expected format'
        }];
    }
    
    // Verify each result has required properties
    results = results.map(result => {
        // Ensure this is an object
        if (!result || typeof result !== 'object') {
            return {
                location: 'Error: Invalid result',
                country: 'XX',
                ip: '0.0.0.0',
                status: 'fail',
                time: null,
                error: 'Invalid result format'
            };
        }
        
        // Ensure it has required properties
        if (!result.status) result.status = 'unknown';
        if (!result.country) result.country = 'XX';
        if (!result.time && result.status === 'success') result.time = 0;
        
        // Add download/upload speed if missing
        if (!result.download_speed) result.download_speed = 0;
        if (!result.upload_speed) result.upload_speed = 0;
        
        return result;
    });
    
    console.log(`Extracted ${results.length} test results:`, results);
    return results;
}

/**
 * Fix image paths in README content
 */
function fixReadmeImages() {
    const images = document.querySelectorAll('.markdown-body img');
    const library = document.querySelector('input#libraryName')?.value || '';
    const version = document.querySelector('input#versionValue')?.value || document.querySelector('select#versionSelect')?.value || '';
    
    images.forEach(img => {
        img.onerror = function() {
            // Special case for sweetalert2 logo
            if (img.src.includes('swal2-logo.png') || library === 'sweetalert2') {
                img.src = "https://cdn.jsdelivr.net/gh/sweetalert2/sweetalert2@839d906cabda403cf5e647b6ff1008198d2455f9/assets/swal2-logo.png";
            } else {
                // Handle other relative paths
                const originalSrc = img.getAttribute('src');
                
                // Only process relative URLs (not starting with http:// or https://)
                if (originalSrc && !originalSrc.match(/^https?:\/\//)) {
                    // Remove leading ./ or ../ if present
                    const cleanSrc = originalSrc.replace(/^\.\/|^\.\.\//, '');
                    
                    // Try GitHub raw content as a fallback
                    if (library.includes('/')) {
                        const [owner, repo] = library.split('/');
                        img.src = `https://raw.githubusercontent.com/${owner}/${repo}/master/${cleanSrc}`;
                    } else {
                        // Try jsDelivr path
                        img.src = `https://cdn.jsdelivr.net/npm/${library}@${version}/${cleanSrc}`;
                    }
                    
                    // Set one more fallback if that fails
                    img.onerror = function() {
                        this.src = 'https://via.placeholder.com/300x150?text=Image+Not+Found';
                    };
                }
            }
        };
    });
}

// Initialize when document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize syntax highlighting
    document.querySelectorAll('pre code').forEach((block) => {
        if (typeof hljs !== 'undefined') {
            hljs.highlightBlock(block);
        }
    });
    
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Hide all tabs
            tabContents.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabId)?.classList.add('active');
            
            // Update active tab state
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            
            this.classList.add('active');
        });
    });
    
    // Version selector
    const versionSelect = document.getElementById('versionSelect');
    if (versionSelect) {
        versionSelect.addEventListener('change', function() {
            const library = document.querySelector('input#libraryName')?.value || '';
            window.location.href = `/tools/cdn-search?library=${library}&version=${this.value}`;
        });
    }
    
    // Copy to clipboard functionality
    const copyButtons = document.querySelectorAll('.cdn-button, .file-btn');
    copyButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const content = this.getAttribute('data-content');
            if (!content) return;
            
            // Create a temporary textarea
            const textarea = document.createElement('textarea');
            textarea.value = content;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            
            // Select and copy
            textarea.select();
            document.execCommand('copy');
            
            // Remove the temporary element
            document.body.removeChild(textarea);
            
            // Show success toast
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Copied!',
                    text: 'Content copied to clipboard',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                alert('Copied to clipboard!');
            }
        });
    });
    
    // Fix README images
    fixReadmeImages();
    
    // AJAX Live Search Implementation
    const searchInput = document.getElementById('librarySearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    if (searchInput) {
        // Handle input changes for live search
        searchInput.addEventListener('keyup', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Don't search if query is too short
            if (query.length < 2) {
                if (searchResults) searchResults.style.display = 'none';
                return;
            }

            // Set a slight delay to prevent sending too many requests
            searchTimeout = setTimeout(function() {
                // Show loading indicator
                if (searchResults) {
                    searchResults.innerHTML = '<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
                    searchResults.style.display = 'block';
                    
                    // Send AJAX request
                    fetch(`/tools/cdn-search/ajax?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.results && data.results.length > 0) {
                                let html = '';
                                
                                // Generate HTML for results
                                data.results.forEach(function(result) {
                                    const isGithubRepo = result.name.indexOf('/') !== -1;
                                    
                                    html += `
                                        <a href="/tools/cdn-search?library=${encodeURIComponent(result.name)}" class="text-decoration-none">
                                            <div class="result-item">
                                                <div class="result-name">${result.name}</div>
                                                <div class="result-description">${result.description || 'No description available'}</div>
                                                <div class="result-providers">
                                                    <span class="cdn-badge cdn-badge-cdnjs">cdnjs</span>
                                                    <span class="cdn-badge cdn-badge-jsdelivr">jsDelivr</span>
                                                    ${!isGithubRepo ? '<span class="cdn-badge cdn-badge-unpkg">unpkg</span>' : ''}
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                });
                                
                                searchResults.innerHTML = html;
                            } else {
                                searchResults.innerHTML = '<div class="p-3 text-center">No libraries found matching your query</div>';
                            }
                        })
                        .catch(() => {
                            searchResults.innerHTML = '<div class="p-3 text-center text-danger">Error searching libraries. Please try again.</div>';
                        });
                }
            }, 300);
        });

        // Show search results when focusing on search input if there are results
        searchInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= 2 && searchResults && searchResults.children.length > 0) {
                searchResults.style.display = 'block';
            }
        });
    }
    
    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (searchResults && searchInput && !searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // CDN Speed Test Button
    const runSpeedTestButton = document.getElementById('runSpeedTest');
    if (runSpeedTestButton) {
        runSpeedTestButton.addEventListener('click', runCdnSpeedTest);
    }
});
</script>
@endsection