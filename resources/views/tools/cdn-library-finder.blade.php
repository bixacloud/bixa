@extends('layouts.master')

@section('title')
    @if(!empty($libraryDetails))
        {{ $libraryDetails['library'] ?? __('tools.library') }} - {{ __('tools.cdn_library_details') }}
    @else
        {{ __('tools.cdn_library_finder') }}
    @endif
@endsection

@section('css')
<!-- Highlight.js for syntax highlighting -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/github.min.css">
<!-- GitHub Markdown CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- GitHub Fonts -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<style>
    :root {
        --primary-color: #4f46e5;
        --primary-dark: #4338ca;
        --primary-light: #818cf8;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --info-color: #3b82f6;
    }
    
    /* Header Section */
    .header-section {
        background: linear-gradient(135deg, var(--primary-dark), #312e81);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .header-title {
        font-weight: 700;
        font-size: 2.5rem;
        color: white !important;
        margin-bottom: 1rem;
    }
    
    .header-description {
        font-size: 1.25rem;
        opacity: 0.9;
    }
    
    /* Main Container */
    .tool-container {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    /* Search Container */
    .search-wrapper {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .search-container {
        display: flex;
        align-items: center;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s;
    }
    
    .search-container:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
    }
    
    .search-icon {
        padding-left: 1rem;
        color: #6b7280;
    }
    
    .search-input {
        width: 100%;
        padding: 0.875rem 1rem;
        font-size: 1rem;
        border: none;
        outline: none;
    }
    
    .search-clear {
        color: #6b7280;
        background: none;
        border: none;
        padding: 0 1rem;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .search-clear:hover {
        color: #374151;
    }
    
    /* Search Results */
    .search-results {
        position: absolute;
        width: 100%;
        margin-top: 0.25rem;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        max-height: 350px;
        overflow-y: auto;
        z-index: 50;
    }
    
    .search-results.hidden {
        display: none;
    }
    
    .search-result-item {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .search-result-item:hover {
        background-color: #f9fafb;
    }
    
    .search-result-item:last-child {
        border-bottom: none;
    }
    
    .search-result-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
    }
    
    .search-result-description {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .search-result-badges {
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .search-result-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 9999px;
    }
    
    .search-result-badge-cdnjs {
        background-color: #e0e7ff;
        color: #4f46e5;
    }
    
    .search-result-badge-jsdelivr {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .search-result-badge-unpkg {
        background-color: #d1fae5;
        color: #059669;
    }
    
    /* Loading Indicator */
    .loading-indicator {
        text-align: center;
        padding: 2.5rem 0;
    }
    
    .spinner {
        width: 2.5rem;
        height: 2.5rem;
        border: 0.25rem solid #e5e7eb;
        border-top-color: var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Info Card */
    .info-card {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    
    .info-header {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .info-icon {
        color: var(--primary-color);
        flex-shrink: 0;
    }
    
    .info-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
    }
    
    .info-list {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    
    .info-list-item {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .info-bullet {
        width: 0.5rem;
        height: 0.5rem;
        background-color: var(--primary-color);
        border-radius: 9999px;
        margin-top: 0.5rem;
        flex-shrink: 0;
    }
    
    /* Popular libraries section */
    .popular-libraries {
        margin-top: 2rem;
    }
    
    .popular-libraries h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
    }
    
    .library-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    @media (min-width: 768px) {
        .library-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (min-width: 1024px) {
        .library-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    .library-card {
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .library-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .library-card-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .library-card-description {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Library Details Styles */
    .library-header {
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .library-header-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    @media (min-width: 768px) {
        .library-header-row {
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
        }
    }
    
    .library-name {
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .library-description {
        color: #4b5563;
        margin-bottom: 1rem;
    }
    
    .breadcrumb-back {
        display: inline-flex;
        align-items: center;
        color: #4f46e5;
        margin-bottom: 1rem;
        text-decoration: none;
        position: relative;
        z-index: 10; /* Ensure this is clickable */
    }
    
    .breadcrumb-back:hover {
        text-decoration: underline;
    }
    
    .version-selector {
        min-width: 200px;
    }
    
    /* Library Metadata */
    .library-meta {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-top: 1.25rem;
    }
    
    @media (min-width: 640px) {
        .library-meta {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    @media (min-width: 1024px) {
        .library-meta {
            grid-template-columns: 1fr 1fr 1fr 1fr;
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
    
    .meta-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        font-size: 1.2rem;
        background-color: #f5f5f5;
        color: #333;
        transition: all 0.2s;
        text-align: center;
        margin-right: 0.5rem;
        position: relative;
    }
    
    .meta-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .meta-link-github {
        background-color: #24292e;
        color: white;
    }
    
    .meta-link-github:hover {
        background-color: #3a434c;
    }
    
    .meta-link-npm {
        background-color: #cb3837;
        color: white;
    }
    
    .meta-link-npm:hover {
        background-color: #db4b4a;
    }
    
    .meta-link-website {
        background-color: #4f46e5;
        color: white;
    }
    
    .meta-link-website:hover {
        background-color: #6366f1;
    }
    
    .meta-links-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    
    .meta-link .tooltip {
        visibility: hidden;
        width: auto;
        background-color: #333;
        color: white;
        text-align: center;
        padding: 5px 10px;
        border-radius: 6px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        white-space: nowrap;
        font-size: 0.75rem;
    }
    
    .meta-link:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }
    
    /* Provider badges */
    .provider-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.625rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .provider-badge-cdnjs {
        background-color: #e0e7ff;
        color: #4f46e5;
    }
    
    .provider-badge-jsdelivr {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .provider-badge-unpkg {
        background-color: #d1fae5;
        color: #059669;
    }
    
    /* Tabs */
    .tabs-container {
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 5;
    }
    
    .nav-tabs {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        position: relative;
        z-index: 10;
    }
    
    .nav-tab {
        position: relative;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4b5563;
        cursor: pointer;
        border: none;
        background: none;
        transition: color 0.2s;
        outline: none;
        z-index: 10;
        pointer-events: auto !important;
    }
    
    .nav-tab:hover {
        color: #4f46e5;
    }
    
    .nav-tab.active {
        color: #4f46e5;
    }
    
    .nav-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #4f46e5;
    }
    
    .tab-content {
        padding: 1.5rem 0;
        position: relative;
        z-index: 1;
    }
    
    .tab-pane {
        display: none;
    }
    
    .tab-pane.active {
        display: block;
    }
    
    /* CDN Links */
    .cdn-links-section {
        margin-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 1.5rem;
    }
    
    .cdn-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
    }
    
    .cdn-links-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    @media (min-width: 768px) {
        .cdn-links-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    .cdn-category {
        margin-bottom: 1rem;
    }
    
    .cdn-category-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
    }
    
    /* Provider list */
    .provider-list {
        margin-bottom: 1rem;
    }
    
    .provider-item {
        margin-bottom: 1rem;
    }
    
    .provider-name {
        font-weight: 500;
        color: #4b5563;
        margin-bottom: 0.5rem;
    }
    
    .cdn-url {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.875rem;
        word-break: break-all;
        background-color: #f9fafb;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    
    .btn-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
        position: relative;
        z-index: 10;
    }
    
    /* Files Tab */
    .files-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .files-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        color: #6b7280;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .files-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
        font-size: 0.875rem;
    }
    
    .files-table tr:hover {
        background-color: #f9fafb;
    }
    
    .files-table tr:last-child td {
        border-bottom: none;
    }
    
    /* GitHub-style markdown */
    .markdown-body {
        box-sizing: border-box;
        min-width: 200px;
        max-width: 980px;
        margin: 0 auto;
        padding: 45px;
        color: #24292f;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
        font-size: 16px;
        line-height: 1.5;
        word-wrap: break-word;
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    @media (max-width: 767px) {
        .markdown-body {
            padding: 15px;
        }
    }
    
    /* Links in markdown */
    .markdown-body a {
        color: #0969da;
        text-decoration: none;
        position: relative;
        z-index: 2;
        pointer-events: auto !important;
    }
    
    .markdown-body a:hover {
        text-decoration: underline;
    }
    
    /* Headings in markdown */
    .markdown-body h1, 
    .markdown-body h2, 
    .markdown-body h3, 
    .markdown-body h4, 
    .markdown-body h5, 
    .markdown-body h6 {
        margin-top: 24px;
        margin-bottom: 16px;
        font-weight: 600;
        line-height: 1.25;
    }
    
    .markdown-body h1 {
        font-size: 2em;
        border-bottom: 1px solid #eaecef;
        padding-bottom: 0.3em;
    }
    
    .markdown-body h2 {
        font-size: 1.5em;
        border-bottom: 1px solid #eaecef;
        padding-bottom: 0.3em;
    }
    
    .markdown-body h3 {
        font-size: 1.25em;
    }
    
    .markdown-body h4 {
        font-size: 1em;
    }
    
    /* Paragraphs in markdown */
    .markdown-body p {
        margin-top: 0;
        margin-bottom: 16px;
    }
    
    /* Images in markdown */
    .markdown-body img {
        max-width: 100%;
        box-sizing: border-box;
        background-color: #fff;
        border-style: none;
    }
    
    /* Code in markdown */
    .markdown-body pre {
        padding: 16px;
        overflow: auto;
        font-size: 85%;
        line-height: 1.45;
        background-color: #f6f8fa;
        border-radius: 6px;
        margin-bottom: 16px;
    }
    
    .markdown-body code {
        font-family: ui-monospace, SFMono-Regular, SF Mono, Menlo, Consolas, Liberation Mono, monospace;
        padding: 0.2em 0.4em;
        margin: 0;
        font-size: 85%;
        background-color: rgba(175, 184, 193, 0.2);
        border-radius: 6px;
    }
    
    .markdown-body pre code {
        padding: 0;
        margin: 0;
        background-color: transparent;
        border: 0;
        font-size: 100%;
        word-break: normal;
        white-space: pre;
        display: inline;
        overflow: visible;
    }
    
    /* Lists in markdown */
    .markdown-body ul,
    .markdown-body ol {
        padding-left: 2em;
        margin-top: 0;
        margin-bottom: 16px;
    }
    
    .markdown-body li {
        word-wrap: break-all;
    }
    
    .markdown-body li+li {
        margin-top: 0.25em;
    }
    
    /* Tables in markdown */
    .markdown-body table {
        display: block;
        width: 100%;
        overflow: auto;
        border-spacing: 0;
        border-collapse: collapse;
        margin-top: 0;
        margin-bottom: 16px;
    }
    
    .markdown-body table tr {
        background-color: #fff;
        border-top: 1px solid #d0d7de;
    }
    
    .markdown-body table tr:nth-child(2n) {
        background-color: #f6f8fa;
    }
    
    .markdown-body table th,
    .markdown-body table td {
        padding: 6px 13px;
        border: 1px solid #d0d7de;
    }
    
    .markdown-body table th {
        font-weight: 600;
    }
    
    /* Blockquotes in markdown */
    .markdown-body blockquote {
        padding: 0 1em;
        color: #57606a;
        border-left: 0.25em solid #d0d7de;
        margin: 0 0 16px 0;
    }
    
    /* Horizontal rule */
    .markdown-body hr {
        height: 0.25em;
        padding: 0;
        margin: 24px 0;
        background-color: #d0d7de;
        border: 0;
    }
    
    /* Copy button styles */
    .btn-copy {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.5;
        color: #fff;
        background-color: #4f46e5;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: background-color 0.2s;
        position: relative;
        z-index: 10;
        pointer-events: auto !important;
    }
    
    .btn-copy:hover {
        background-color: #4338ca;
    }
    
    .btn-html {
        background-color: #ef4444;
    }
    
    .btn-html:hover {
        background-color: #dc2626;
    }
    
    /* File copy button */
    .btn-file-copy {
        background-color: #4f46e5;
        color: white;
        border: none;
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        cursor: pointer;
        transition: background-color 0.2s;
        position: relative;
        z-index: 10;
        pointer-events: auto !important;
    }
    
    .btn-file-copy:hover {
        background-color: #4338ca;
    }
    
    /* Badge */
    .badge {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    
    .badge-success {
        color: #fff;
        background-color: #10b981;
    }
    
    /* Fix z-index and pointer events for all clickable elements */
    button, .btn, .btn-copy, .nav-tab, a {
        position: relative;
        z-index: 5;
        pointer-events: auto !important;
        cursor: pointer;
    }
    
    /* Ensure card body doesn't block clicks */
    .card-body {
        position: relative;
        z-index: 1;
    }
    
    /* Better font rendering for all text */
    body {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Back to search button in details view */
    .back-to-search {
        display: inline-flex;
        align-items: center;
        font-weight: 500;
        color: #4f46e5;
        margin-bottom: 1rem;
        text-decoration: none;
    }
    
    .back-to-search:hover {
        text-decoration: underline;
    }
    
    /* Active tab highlight */
    .nav-tabs .nav-tab.active {
        color: #4f46e5;
        border-bottom: 2px solid #4f46e5;
    }
    
    /* Loading state overlay */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        transition: opacity 0.3s;
    }
    
    .loading-overlay.hidden {
        display: none;
    }
    
    .loading-text {
        margin-top: 1rem;
        font-size: 1rem;
        color: #4f46e5;
        font-weight: 500;
    }
    
    /* Cache indicator */
    .cache-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        background-color: #bbf7d0;
        color: #16a34a;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.25rem;
        margin-left: 0.5rem;
    }
    
    .cache-badge i {
        margin-right: 0.25rem;
    }

    /* CDN Speed Test Styles */
    .cdn-speed-section {
        margin-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') {{ __('tools.tools') }} @endslot
        @if(!empty($libraryDetails))
            @slot('li_2') <a href="{{ route('tools.cdn-library-finder') }}">{{ __('tools.cdn_library_finder') }}</a> @endslot
            @slot('title') {{ $libraryDetails['library'] ?? __('tools.library') }} @endslot
        @else
            @slot('title') {{ __('tools.cdn_library_finder') }} @endslot
        @endif
    @endcomponent

    <!-- Header Section (always shown) -->
    <div class="header-section text-center">
        <div class="container">
            <h1 class="header-title">{{ __('tools.cdn_library_finder') }}</h1>
            <p class="header-description">{{ __('tools.find_libraries_description') }}</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="bx bx-search-alt me-2"></i>
                        {{ !empty($libraryDetails) ? __('tools.library_details') : __('tools.search_libraries') }}
                    </div>
                    <div class="card-body position-relative">
                        <!-- Loading overlay -->
                        <div id="loadingOverlay" class="loading-overlay hidden">
                            <div class="spinner"></div>
                            <div class="loading-text">{{ __('tools.loading_library_details') }}</div>
                        </div>
                        
                        <div class="tool-container p-4">
                            <!-- Search Component (always shown) -->
                            <div class="search-wrapper">
                                <div class="search-container">
                                    <div class="search-icon">
                                        <i class="bx bx-search"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="librarySearch" 
                                        class="search-input" 
                                        placeholder="{{ __('tools.search_placeholder') }}" 
                                        autocomplete="off"
                                        value="{{ $library ?? '' }}"
                                    >
                                    <button type="button" id="searchClear" class="search-clear {{ empty($library) ? 'd-none' : '' }}">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                                <!-- Search Results -->
                                <div id="searchResults" class="search-results hidden"></div>
                            </div>
                            
                            @if(!empty($libraryDetails))
                                <!-- Library Details Section -->
                                <div class="library-info mt-4" id="libraryDetailsContainer">
                                    <!-- Library Header -->
                                    <div class="library-header">
                                        <div class="library-header-row">
                                            <div>
                                                <a href="{{ route('tools.cdn-library-finder') }}" class="breadcrumb-back">
                                                    <i class="bx bx-arrow-back me-1"></i>
                                                    {{ __('tools.back_to_search') }}
                                                </a>
                                                <h2 class="library-name">
                                                    {{ $libraryDetails['library'] ?? __('tools.library') }}
                                                    <span id="cacheStatus" class="cache-badge hidden">
                                                        <i class="bx bx-history"></i> {{ __('tools.cached_result') }}
                                                    </span>
                                                </h2>
                                                <div class="library-description">
                                                    @if(!empty($libraryDetails['cdnjs']['description']))
                                                        {{ $libraryDetails['cdnjs']['description'] }}
                                                    @elseif(!empty($libraryDetails['jsdelivr']['description']))
                                                        {{ $libraryDetails['jsdelivr']['description'] }}
                                                    @else
                                                        {{ __('tools.no_description') }}
                                                    @endif
                                                </div>
                                                
                                                <!-- Provider badges -->
                                                <div class="mt-2">
                                                    @if(!empty($libraryDetails['cdnLinks']['cdnjs']) && (!empty($libraryDetails['cdnLinks']['cdnjs']['js']) || !empty($libraryDetails['cdnLinks']['cdnjs']['css'])))
                                                        <span class="provider-badge provider-badge-cdnjs">CDNJS</span>
                                                    @endif
                                                    
                                                    @if(!empty($libraryDetails['cdnLinks']['jsdelivr']) && (!empty($libraryDetails['cdnLinks']['jsdelivr']['js']) || !empty($libraryDetails['cdnLinks']['jsdelivr']['css'])))
                                                        <span class="provider-badge provider-badge-jsdelivr">jsDelivr</span>
                                                    @endif
                                                    
                                                    @if(!empty($libraryDetails['cdnLinks']['unpkg']) && (!empty($libraryDetails['cdnLinks']['unpkg']['js']) || !empty($libraryDetails['cdnLinks']['unpkg']['css'])))
                                                        <span class="provider-badge provider-badge-unpkg">Unpkg</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="version-selector">
                                                <form method="get" action="{{ route('tools.cdn-library-finder') }}" id="versionForm">
                                                    <input type="hidden" name="library" value="{{ $libraryDetails['library'] ?? '' }}">
                                                    <label for="version">{{ __('tools.version') }}</label>
                                                    <select id="version" name="version" class="form-select" onchange="loadLibraryVersion()">
                                                        @if(!empty($libraryDetails['versions']))
                                                            @foreach($libraryDetails['versions'] as $v)
                                                                <option value="{{ $v }}" {{ ($libraryDetails['version'] ?? '') == $v ? 'selected' : '' }}>
                                                                    {{ $v }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                            
                                        <!-- Library Metadata -->
                                        <div class="library-meta">
                                            <div class="meta-item">
                                                <i class="bx bx-star meta-icon"></i>
                                                <span>
                                                    @if(!empty($libraryDetails['jsdelivr']['github']['stargazers_count']))
                                                        {{ number_format($libraryDetails['jsdelivr']['github']['stargazers_count']) }} {{ __('tools.stars') }}
                                                    @elseif(!empty($libraryDetails['github']['stars']))
                                                        {{ number_format($libraryDetails['github']['stars']) }} {{ __('tools.stars') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <div class="meta-item">
                                                <i class="bx bx-certification meta-icon"></i>
                                                <span>
                                                    @if(!empty($libraryDetails['jsdelivr']['license']))
                                                        {{ $libraryDetails['jsdelivr']['license'] }}
                                                    @elseif(!empty($libraryDetails['cdnjs']['license']))
                                                        {{ $libraryDetails['cdnjs']['license'] }}
                                                    @elseif(!empty($libraryDetails['license']))
                                                        {{ $libraryDetails['license'] }}
                                                    @else
                                                        {{ __('tools.unknown_license') }}
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <!-- External links section -->
                                            <div class="meta-links-container">
                                                <!-- GitHub Link -->
                                                @if(!empty($libraryDetails['github']) && !empty($libraryDetails['github']['url']))
                                                    <a href="{{ $libraryDetails['github']['url'] }}" target="_blank" rel="noopener noreferrer" class="meta-link meta-link-github" title="GitHub Repository">
                                                        <i class="fa-brands fa-github"></i>
                                                        <span class="tooltip">GitHub</span>
                                                    </a>
                                                @elseif($isGitHub && !empty($libraryDetails['githubUser']) && !empty($libraryDetails['githubRepo']))
                                                    <a href="https://github.com/{{ $libraryDetails['githubUser'] }}/{{ $libraryDetails['githubRepo'] }}" target="_blank" rel="noopener noreferrer" class="meta-link meta-link-github" title="GitHub Repository">
                                                        <i class="fa-brands fa-github"></i>
                                                        <span class="tooltip">GitHub</span>
                                                    </a>
                                                @elseif(!empty($libraryDetails['repository']) && !empty($libraryDetails['repository']['url']) && strpos($libraryDetails['repository']['url'], 'github.com') !== false)
                                                    <a href="{{ $libraryDetails['repository']['url'] }}" target="_blank" rel="noopener noreferrer" class="meta-link meta-link-github" title="GitHub Repository">
                                                        <i class="fa-brands fa-github"></i>
                                                        <span class="tooltip">GitHub</span>
                                                    </a>
                                                @endif
                                                
                                                <!-- NPM Link -->
                                                <a href="https://www.npmjs.com/package/{{ $libraryDetails['library'] }}" target="_blank" rel="noopener noreferrer" class="meta-link meta-link-npm" title="NPM Package">
                                                    <i class="fa-brands fa-npm"></i>
                                                    <span class="tooltip">npm</span>
                                                </a>
                                                
                                                <!-- Website Link -->
                                                @if(!empty($libraryDetails['jsdelivr']['homepage']))
                                                    <a href="{{ $libraryDetails['jsdelivr']['homepage'] }}" target="_blank" rel="noopener noreferrer" class="meta-link meta-link-website" title="Official Website">
                                                        <i class="fa-solid fa-globe"></i>
                                                        <span class="tooltip">{{ parse_url($libraryDetails['jsdelivr']['homepage'], PHP_URL_HOST) }}</span>
                                                    </a>
                                                @elseif(!empty($libraryDetails['cdnjs']['homepage']))
                                                    <a href="{{ $libraryDetails['cdnjs']['homepage'] }}" target="_blank" rel="noopener noreferrer" class="meta-link meta-link-website" title="Official Website">
                                                        <i class="fa-solid fa-globe"></i>
                                                        <span class="tooltip">{{ parse_url($libraryDetails['cdnjs']['homepage'], PHP_URL_HOST) }}</span>
                                                    </a>
                                                @elseif(!empty($libraryDetails['homepage']))
                                                    <a href="{{ $libraryDetails['homepage'] }}" target="_blank" rel="noopener noreferrer" class="meta-link meta-link-website" title="Official Website">
                                                        <i class="fa-solid fa-globe"></i>
                                                        <span class="tooltip">{{ parse_url($libraryDetails['homepage'], PHP_URL_HOST) }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- CDN Links Section (Always visible at the top) -->
                                    <div class="cdn-links-section mb-4">
                                        <h3 class="cdn-section-title">{{ __('tools.cdn_links') }}</h3>
                                        <div class="cdn-links-grid">
                                            <!-- JavaScript Column -->
                                            <div class="cdn-category">
                                                <h4 class="cdn-category-title">{{ __('tools.javascript') }}</h4>
                                                
                                                <div class="provider-list">
                                                    @if(!empty($cdnLinksFormatted['js']))
                                                        @foreach($cdnLinksFormatted['js'] as $jsLink)
                                                            <div class="provider-item">
                                                                <div class="provider-name">{{ $jsLink['provider'] }}</div>
                                                                <div class="cdn-url">{{ $jsLink['url'] }}</div>
                                                                <div class="btn-actions">
                                                                    <button type="button" class="btn-copy" onclick="copyToClipboard('{{ $jsLink['url'] }}')" data-copy-text="{{ $jsLink['url'] }}">
                                                                        <i class="bx bx-copy me-1"></i> {{ __('tools.copy_url') }}
                                                                    </button>
                                                                    <button type="button" class="btn-copy btn-html" onclick="copyToClipboard('{{ $jsLink['htmlTagRaw'] }}')" data-copy-text="{{ $jsLink['htmlTagRaw'] }}">
                                                                        <i class="bx bx-code me-1"></i> HTML
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-muted">{{ __('tools.no_js_files') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- CSS Column -->
                                            <div class="cdn-category">
                                                <h4 class="cdn-category-title">{{ __('tools.css') }}</h4>
                                                
                                                <div class="provider-list">
                                                    @if(!empty($cdnLinksFormatted['css']))
                                                        @foreach($cdnLinksFormatted['css'] as $cssLink)
                                                            <div class="provider-item">
                                                                <div class="provider-name">{{ $cssLink['provider'] }}</div>
                                                                <div class="cdn-url">{{ $cssLink['url'] }}</div>
                                                                <div class="btn-actions">
                                                                    <button type="button" class="btn-copy" onclick="copyToClipboard('{{ $cssLink['url'] }}')" data-copy-text="{{ $cssLink['url'] }}">
                                                                        <i class="bx bx-copy me-1"></i> {{ __('tools.copy_url') }}
                                                                    </button>
                                                                    <button type="button" class="btn-copy btn-html" onclick="copyToClipboard('{{ $cssLink['htmlTagRaw'] }}')" data-copy-text="{{ $cssLink['htmlTagRaw'] }}">
                                                                        <i class="bx bx-code me-1"></i> HTML
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-muted">{{ __('tools.no_css_files') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- CDN Speed Test Section -->
                                    <div class="cdn-speed-section mb-4">
                                        <h3 class="cdn-section-title">
                                            <i class="bx bx-tachometer me-1"></i> {{ __('tools.cdn_speed_test') }}
                                        </h3>
                                        <p class="mb-3">{{ __('tools.cdn_speed_description') }}</p>
                                        
                                        <button type="button" id="runSpeedTest" class="btn btn-primary mb-4">
                                            <i class="bx bx-run me-1"></i> {{ __('tools.run_speed_test') }}
                                        </button>
                                        
                                        <div id="cdnSpeedResults"></div>
                                    </div>
                                    
                                    <!-- Tabs container -->
                                    <div class="tabs-container">
                                        <div class="nav-tabs">
                                            <button type="button" class="nav-tab active" data-tab="readme" onclick="openTab('readme')">
                                                {{ __('tools.readme') }}
                                            </button>
                                            <button type="button" class="nav-tab" data-tab="files" onclick="openTab('files')">
                                                {{ __('tools.files') }}
                                            </button>
                                        </div>
                                        
                                        <div class="tab-content">
                                            <!-- README Tab (active by default) -->
                                            <div id="readme" class="tab-pane active">
                                                @if(!empty($readmeContent))
                                                    {!! $readmeContent !!}
                                                @else
                                                    <div class="alert alert-info">
                                                        {{ __('tools.readme_not_available', ['library' => $libraryDetails['library'] ?? 'library', 'version' => $libraryDetails['version'] ?? 'version']) }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Files Tab -->
                                            <div id="files" class="tab-pane">
                                                <div class="table-responsive">
                                                    {!! $filesTable !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Info Card - Only shown on search page -->
                                <div id="infoCard" class="info-card">
                                    <div class="info-header">
                                        <i class="bx bx-info-circle info-icon fs-4"></i>
                                        <h3 class="info-title">{{ __('tools.about_cdn_finder') }}</h3>
                                    </div>
                                    <p>{{ __('tools.cdn_finder_help') }}</p>
                                    <div class="info-list">
                                        <div class="info-list-item">
                                            <div class="info-bullet"></div>
                                            <div>
                                                <strong>jsDelivr:</strong> {{ __('tools.jsdelivr_description') }}
                                            </div>
                                        </div>
                                        <div class="info-list-item">
                                            <div class="info-bullet"></div>
                                            <div>
                                                <strong>CDNJS:</strong> {{ __('tools.cdnjs_description') }}
                                            </div>
                                        </div>
                                        <div class="info-list-item">
                                            <div class="info-bullet"></div>
                                            <div>
                                                <strong>Unpkg:</strong> {{ __('tools.unpkg_description') }}
                                            </div>
                                        </div>
                                    </div>
                                    <p>{{ __('tools.usage_hint') }}</p>
                                </div>
                                
                                <!-- Popular Libraries - Only shown on search page -->
                                <div class="popular-libraries">
                                    <h3>
                                        <i class="bx bx-star text-warning me-2"></i>
                                        {{ __('tools.popular_libraries') }}
                                    </h3>
                                    <div class="library-grid">
                                        @foreach($popularLibraries as $lib)
                                            <div class="library-card" data-library="{{ $lib['name'] }}">
                                                <div class="library-card-name">{{ $lib['name'] }}</div>
                                                <div class="library-card-description">{{ $lib['description'] }}</div>
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
    </div>
@endsection

@section('script')
<!-- Highlight.js for syntax highlighting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<!-- SweetAlert2 for notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/**
 * CDN Library Finder script with improved provider display and speed testing
 * Fixes:
 * 1. Search results not properly displaying Unpkg provider
 * 2. Speed test running against all providers when only some are available
 */
(function() {
    'use strict';
    
    // Configuration
    const appConfig = {
        endpoints: {
            search: '{{ url("tools/search-libraries") }}',
            library: '{{ route("tools.cdn-library-finder") }}',
            copyUrl: '{{ route("tools.cdn-copy") }}',
            speedTest: '{{ route("tools.cdn-test-speed") }}'
        },
        baseUrl: '{{ url("/") }}',
        routeUrl: '{{ route("tools.cdn-library-finder") }}',
        csrfToken: '{{ csrf_token() }}',
        cacheSettings: {
            enabled: true,              // Enable caching
            searchExpiry: 24 * 60 * 60, // Search results cache for 24 hours
            libraryExpiry: 7 * 24 * 60 * 60, // Library details cache for 1 week
            prefix: 'cdn_finder_'       // Cache key prefix
        },
        translations: {
            noDescription: '{{ __("tools.no_description") }}',
            availableOnJsdelivr: '{{ __("tools.available_on_jsdelivr") }}',
            availableOnUnpkg: '{{ __("tools.available_on_unpkg") }}',
            noLibrariesFound: '{{ __("tools.no_libraries_found") }}',
            errorSearching: '{{ __("tools.error_searching") }}',
            searching: '{{ __("tools.searching") }}',
            copied: '{{ __("tools.copied_to_clipboard") }}',
            nothingToCopy: '{{ __("tools.nothing_to_copy") }}',
            loadingDetails: '{{ __("tools.loading_library_details") }}',
            errorLoadingDetails: '{{ __("tools.error_loading_details") }}',
            cachedResult: '{{ __("tools.cached_result") }}',
            allProviders: '{{ __("tools.all_providers") }}',
            cdnjsdelivr_only: '{{ __("tools.cdnjsdelivr_only") }}',
            cdnjsunpkg_only: '{{ __("tools.cdnjsunpkg_only") }}',
            jsdelivrunpkg_only: '{{ __("tools.jsdelivrunpkg_only") }}',
            tryAnotherQuery: '{{ __("tools.try_another_query") }}'
        }
    };
    
    // Store repository information for use in image handling
    window.libraryRepoInfo = {
        user: '{{ $libraryDetails["githubUser"] ?? "" }}',
        repo: '{{ $libraryDetails["githubRepo"] ?? "" }}',
        isGitHub: {{ isset($isGitHub) && $isGitHub ? 'true' : 'false' }}
    };
    
    // DOM elements
    const elements = {
        searchInput: document.getElementById('librarySearch'),
        searchClear: document.getElementById('searchClear'),
        searchResults: document.getElementById('searchResults'),
        libraryCards: document.querySelectorAll('.library-card'),
        tabButtons: document.querySelectorAll('.nav-tab'),
        tabPanes: document.querySelectorAll('.tab-pane'),
        libraryDetailsContainer: document.getElementById('libraryDetailsContainer'),
        versionSelector: document.getElementById('version'),
        versionForm: document.getElementById('versionForm'),
        loadingOverlay: document.getElementById('loadingOverlay'),
        cacheStatusBadge: document.getElementById('cacheStatus'),
        runSpeedTest: document.getElementById('runSpeedTest'),
        cdnSpeedResults: document.getElementById('cdnSpeedResults')
    };
    
    // Application state
    let appState = {
        searchTimeout: null,
        activeTab: 'readme',
        currentLibrary: '{{ $library ?? "" }}',
        currentVersion: '{{ $libraryDetails["version"] ?? "" }}',
        availableProviders: [] // Track available providers for the current library
    };
    
    // Cache helper functions
    const cache = {
        /**
         * Set an item in localStorage with expiration
         * @param {string} key - Cache key
         * @param {any} value - Value to store
         * @param {number} expirySeconds - Expiration time in seconds
         */
        set: function(key, value, expirySeconds) {
            if (!appConfig.cacheSettings.enabled) return;
            
            const now = new Date();
            const item = {
                value: value,
                expiry: now.getTime() + (expirySeconds * 1000),
            };
            
            try {
                localStorage.setItem(
                    appConfig.cacheSettings.prefix + key, 
                    JSON.stringify(item)
                );
            } catch (e) {
                console.warn('Cache storage failed:', e);
                this.clearOldItems(); // Attempt to clear space
            }
        },
        
        /**
         * Get an item from localStorage if it exists and is not expired
         * @param {string} key - Cache key
         * @returns {any|null} The stored value or null if expired/not found
         */
        get: function(key) {
            if (!appConfig.cacheSettings.enabled) return null;
            
            const itemStr = localStorage.getItem(appConfig.cacheSettings.prefix + key);
            if (!itemStr) return null;
            
            try {
                const item = JSON.parse(itemStr);
                const now = new Date();
                
                // Check if expired
                if (now.getTime() > item.expiry) {
                    localStorage.removeItem(appConfig.cacheSettings.prefix + key);
                    return null;
                }
                
                return item.value;
            } catch (e) {
                console.warn('Cache retrieval failed:', e);
                return null;
            }
        },
        
        /**
         * Remove an item from cache
         * @param {string} key - Cache key
         */
        remove: function(key) {
            localStorage.removeItem(appConfig.cacheSettings.prefix + key);
        },
        
        /**
         * Clear all items that start with the cache prefix
         */
        clear: function() {
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key.startsWith(appConfig.cacheSettings.prefix)) {
                    localStorage.removeItem(key);
                }
            }
        },
        
        /**
         * Clear older items when storage is full
         */
        clearOldItems: function() {
            const keysToRemove = [];
            const now = new Date().getTime();
            
            // Identify old items
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key.startsWith(appConfig.cacheSettings.prefix)) {
                    try {
                        const itemStr = localStorage.getItem(key);
                        const item = JSON.parse(itemStr);
                        
                        // Mark old items for removal
                        if (now > item.expiry || now > (item.expiry - 24 * 60 * 60 * 1000)) {
                            keysToRemove.push(key);
                        }
                    } catch (e) {
                        keysToRemove.push(key); // Remove invalid items
                    }
                }
            }
            
            // Remove items
            keysToRemove.forEach(key => localStorage.removeItem(key));
            
            // If no items were removed, remove the oldest
            if (keysToRemove.length === 0) {
                let oldestKey = null;
                let oldestTime = Infinity;
                
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key.startsWith(appConfig.cacheSettings.prefix)) {
                        try {
                            const itemStr = localStorage.getItem(key);
                            const item = JSON.parse(itemStr);
                            
                            if (item.expiry < oldestTime) {
                                oldestTime = item.expiry;
                                oldestKey = key;
                            }
                        } catch (e) {
                            // Skip invalid items
                        }
                    }
                }
                
                if (oldestKey) {
                    localStorage.removeItem(oldestKey);
                }
            }
        }
    };
    
    // Initialize the application
    document.addEventListener('DOMContentLoaded', function() {
        initializeSearch();
        initializeLibraryDetails();
        
        // Apply syntax highlighting to code blocks
        document.querySelectorAll('pre code').forEach(block => {
            hljs.highlightElement(block);
        });
        
        // Handle clicks outside search results to close them
        document.addEventListener('click', function(e) {
            if (!elements.searchResults.contains(e.target) && e.target !== elements.searchInput) {
                elements.searchResults.classList.add('hidden');
            }
        });
        
        // Check if current view is from cache
        if (appState.currentLibrary && elements.cacheStatusBadge) {
            const cacheKey = `library_${appState.currentLibrary}_${appState.currentVersion}`;
            const isFromCache = sessionStorage.getItem(cacheKey + '_from_cache');
            
            if (isFromCache === 'true') {
                elements.cacheStatusBadge.classList.remove('hidden');
            }
        }

        // Initialize CDN speed test button
        if (elements.runSpeedTest) {
            elements.runSpeedTest.addEventListener('click', function() {
                testCdnSpeed();
            });
        }
        
        // Extract current library info if on details page
        extractCurrentLibraryInfo();
    });
    
    /**
     * Extract current library information from page, including available providers
     */
    function extractCurrentLibraryInfo() {
        // Extract current library and version from URL or page elements
        const urlParams = new URLSearchParams(window.location.search);
        appState.currentLibrary = urlParams.get('library') || '';
        appState.currentVersion = urlParams.get('version') || '';
        
        // Extract available providers from the badges
        if (appState.currentLibrary) {
            appState.availableProviders = [];
            document.querySelectorAll('.provider-badge').forEach(badge => {
                if (badge.classList.contains('provider-badge-cdnjs')) {
                    appState.availableProviders.push('cdnjs');
                } else if (badge.classList.contains('provider-badge-jsdelivr')) {
                    appState.availableProviders.push('jsdelivr');
                } else if (badge.classList.contains('provider-badge-unpkg')) {
                    appState.availableProviders.push('unpkg');
                }
            });
            
            console.log('Current library:', appState.currentLibrary);
            console.log('Current version:', appState.currentVersion);
            console.log('Available providers:', appState.availableProviders);
        }
    }
    
    /**
     * Initialize search functionality
     */
    function initializeSearch() {
        if (!elements.searchInput) return;
        
        // Search input events
        elements.searchInput.addEventListener('input', handleSearchInput);
        elements.searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSearch();
            }
        });
        
        // Search clear button
        if (elements.searchClear) {
            elements.searchClear.addEventListener('click', clearSearch);
        }
        
        // Library cards click event
        elements.libraryCards.forEach(card => {
            card.addEventListener('click', function() {
                const library = this.getAttribute('data-library');
                if (library) {
                    loadLibraryDetails(library);
                }
            });
        });
    }
    
    /**
     * Initialize library details functionality
     */
    function initializeLibraryDetails() {
        // Initialize tab functionality
        elements.tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                openTab(tabId);
            });
        });
        
        // Version selector change event
        if (elements.versionSelector) {
            elements.versionSelector.addEventListener('change', loadLibraryVersion);
        }
        
        // Make sure links in README open in new tabs
        document.querySelectorAll('.markdown-body a').forEach(link => {
            link.style.pointerEvents = 'auto';
            link.style.position = 'relative';
            link.style.zIndex = '2';
            
            if (link.hostname && link.hostname !== window.location.hostname) {
                link.setAttribute('target', '_blank');
                link.setAttribute('rel', 'noopener noreferrer');
            }
        });
        
        // Fix any broken images with enhanced error handling
        document.querySelectorAll('.markdown-body img').forEach(img => {
            img.onerror = function() {
                handleImageError(img);
            };
        });
    }
    
    /**
     * Handle search input with debounce
     */
    function handleSearchInput() {
        const query = elements.searchInput.value.trim();
        
        // Toggle clear button
        if (query.length > 0) {
            elements.searchClear.classList.remove('d-none');
        } else {
            elements.searchClear.classList.add('d-none');
            elements.searchResults.classList.add('hidden');
            return;
        }
        
        // Debounce search
        clearTimeout(appState.searchTimeout);
        
        if (query.length >= 2) {
            appState.searchTimeout = setTimeout(function() {
                searchLibraries(query);
            }, 300);
        } else {
            elements.searchResults.classList.add('hidden');
        }
    }
    
    /**
     * Clear search input and results
     */
    function clearSearch() {
        elements.searchInput.value = '';
        elements.searchClear.classList.add('d-none');
        elements.searchResults.classList.add('hidden');
    }
    
    /**
     * Search libraries using AJAX
     * @param {string} query - Search query string
     */
    function searchLibraries(query) {
        // Check cache first
        const cacheKey = `search_${query.toLowerCase().replace(/\W+/g, '_')}`;
        const cachedResults = cache.get(cacheKey);
        
        if (cachedResults) {
            console.log('Using cached search results for:', query);
            displaySearchResults(cachedResults);
            return;
        }
        
        // Show loading indicator
        elements.searchResults.innerHTML = `
            <div class="p-3 text-center">
                <div class="spinner" style="width: 1.5rem; height: 1.5rem; margin-bottom: 0.5rem;"></div>
                <p class="mb-0">${appConfig.translations.searching}</p>
            </div>
        `;
        elements.searchResults.classList.remove('hidden');
        
        // Create an XMLHttpRequest
        const xhr = new XMLHttpRequest();
        xhr.open('POST', appConfig.endpoints.search, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', appConfig.csrfToken);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    
                    if (data.success) {
                        // Cache the search results
                        cache.set(cacheKey, data, appConfig.cacheSettings.searchExpiry);
                        
                        // Display results
                        displaySearchResults(data);
                    } else {
                        showToast(data.error || appConfig.translations.errorSearching, 'error');
                        elements.searchResults.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    showToast(appConfig.translations.errorSearching, 'error');
                    elements.searchResults.classList.add('hidden');
                }
            } else {
                showToast(appConfig.translations.errorSearching, 'error');
                elements.searchResults.classList.add('hidden');
            }
        };
        
        xhr.onerror = function() {
            console.error('Network error during search');
            showToast(appConfig.translations.errorSearching, 'error');
            elements.searchResults.classList.add('hidden');
        };
        
        // Send the request
        xhr.send(JSON.stringify({
            query: query,
            limit: 10
        }));
    }
    
   /**
    * Display search results in the dropdown with improved provider handling
    * @param {Object} data - Search results data
    */
   function displaySearchResults(data) {
       let resultsHTML = '';
       
       // Map to store all libraries
       const libraries = {};
       
       console.log('Raw search results:', data);

       // Process CDNJS results
       if (data.cdnjs && data.cdnjs.results && data.cdnjs.results.length > 0) {
           for (const library of data.cdnjs.results) {
               const name = library.name;
               
               if (!libraries[name]) {
                   libraries[name] = {
                       name: name,
                       description: library.description || data.translations.noDescription,
                       providers: ['cdnjs']
                   };
               } else if (!libraries[name].providers.includes('cdnjs')) {
                   libraries[name].providers.push('cdnjs');
               }
           }
       }
       
       // Process jsDelivr results
       if (data.jsdelivr && Array.isArray(data.jsdelivr) && data.jsdelivr.length > 0) {
           for (const item of data.jsdelivr) {
               if (!item || !item.name) continue;
               
               const name = item.name;
               
               if (!libraries[name]) {
                   libraries[name] = {
                       name: name,
                       description: item.description || data.translations.availableOnJsdelivr,
                       providers: ['jsdelivr']
                   };
               } else if (!libraries[name].providers.includes('jsdelivr')) {
                   libraries[name].providers.push('jsdelivr');
               }
           }
       }
       
       // Process unpkg results - ensure unpkg is properly processed
       if (data.unpkg && Array.isArray(data.unpkg) && data.unpkg.length > 0) {
           for (const item of data.unpkg) {
               if (!item || !item.name) continue;
               
               const name = item.name;
               
               if (!libraries[name]) {
                   libraries[name] = {
                       name: name,
                       description: item.description || data.translations.availableOnUnpkg,
                       providers: ['unpkg']
                   };
               } else if (!libraries[name].providers.includes('unpkg')) {
                   libraries[name].providers.push('unpkg');
               }
           }
       }
       
       // Convert libraries object to array and sort
       const librariesArray = Object.values(libraries).sort((a, b) => {
           // Sort by number of providers first (more providers first)
           if (a.providers.length !== b.providers.length) {
               return b.providers.length - a.providers.length;
           }
           
           // Then alphabetically
           return a.name.localeCompare(b.name);
       });
       
       console.log('Processed search results:', librariesArray);
       
       // Generate HTML for each library with explicit providers display
       for (const library of librariesArray) {
           // Create badges for providers - ensure ALL providers are displayed
           let badgesHTML = '';
           
           // Make sure to generate badges for all available providers
           if (library.providers.includes('cdnjs')) {
               badgesHTML += `<span class="search-result-badge search-result-badge-cdnjs">CDNJS</span> `;
           }
           
           if (library.providers.includes('jsdelivr')) {
               badgesHTML += `<span class="search-result-badge search-result-badge-jsdelivr">JSDELIVR</span> `;
           }
           
           if (library.providers.includes('unpkg')) {
               badgesHTML += `<span class="search-result-badge search-result-badge-unpkg">UNPKG</span> `;
           }
           
           // Add a special message for availability description based on providers
           let availabilityDesc = library.description;
           
           // Explicit message about provider availability
           if (library.providers.length === 3) {
               availabilityDesc += ` <small>(${data.translations.allProviders})</small>`;
           } else if (library.providers.length === 2) {
               // Create provider-specific messages for combinations
               const providerSet = library.providers.sort().join(',');
               if (providerSet === 'cdnjs,jsdelivr') {
                   availabilityDesc += ` <small>(${data.translations.cdnjsdelivr_only})</small>`;
               } else if (providerSet === 'cdnjs,unpkg') {
                   availabilityDesc += ` <small>(${data.translations.cdnjsunpkg_only})</small>`;
               } else if (providerSet === 'jsdelivr,unpkg') {
                   availabilityDesc += ` <small>(${data.translations.jsdelivrunpkg_only})</small>`;
               }
           }
           
           resultsHTML += `
               <div class="search-result-item" data-type="library" data-name="${library.name}" data-providers="${library.providers.join(',')}">
                   <div class="search-result-name">${library.name}</div>
                   <div class="search-result-description">${availabilityDesc}</div>
                   <div class="search-result-badges">${badgesHTML}</div>
               </div>
           `;
       }
       
       if (librariesArray.length === 0) {
           resultsHTML = `
               <div class="p-3 text-center">
                   <p class="mb-1">${data.translations.noLibrariesFound}</p>
                   <p class="small text-muted mb-0">${data.translations.tryAnotherQuery}</p>
               </div>
           `;
       }
       
       elements.searchResults.innerHTML = resultsHTML;
       elements.searchResults.classList.remove('hidden');
       
       // Add click event listeners to search results
       document.querySelectorAll('.search-result-item').forEach(item => {
           item.addEventListener('click', function() {
               const name = this.getAttribute('data-name');
               const providers = this.getAttribute('data-providers');
               
               // Pass providers information when loading library details
               loadLibraryDetails(name, '', providers);
           });
       });
   }
    
    /**
     * Load library details
     * @param {string} libraryName - Name of the library
     * @param {string} version - Optional specific version
     * @param {string} providers - Optional string with comma-separated providers
     */
    function loadLibraryDetails(libraryName, version = '', providers = '') {
        // Update browser history
        const url = new URL(appConfig.routeUrl);
        url.searchParams.set('library', libraryName);
        if (version) {
            url.searchParams.set('version', version);
        }
        if (providers) {
            url.searchParams.set('providers', providers);
        }
        
        // Use History API for smooth navigation
        window.history.pushState({ 
            library: libraryName, 
            version, 
            providers 
        }, '', url.toString());
        
        // Show loading overlay
        if (elements.loadingOverlay) {
            elements.loadingOverlay.classList.remove('hidden');
        }
        
        // Navigate to the URL
        window.location.href = url.toString();
    }
    
    /**
     * Load a different version of the current library
     */
    function loadLibraryVersion() {
        if (!elements.versionSelector || !elements.versionForm) return;
        
        const version = elements.versionSelector.value;
        const library = elements.versionForm.querySelector('input[name="library"]').value;
        
        // Load new version details
        loadLibraryDetails(library, version);
    }
    
    /**
     * Switch between tabs in library details view
     * @param {string} tabId - ID of the tab to open
     */
    function openTab(tabId) {
        // Update application state
        appState.activeTab = tabId;
        
        // Hide all tabs
        elements.tabPanes.forEach(function(tab) {
            tab.style.display = 'none';
            tab.classList.remove('active');
        });
        
        // Show selected tab
        const selectedTab = document.getElementById(tabId);
        if (selectedTab) {
            selectedTab.style.display = 'block';
            selectedTab.classList.add('active');
        }
        
        // Update active tab state
        elements.tabButtons.forEach(function(tab) {
            tab.classList.remove('active');
            if (tab.getAttribute('data-tab') === tabId) {
                tab.classList.add('active');
            }
        });
    }
    
    /**
     * Test CDN connection speed only for providers that are available for the current library
     */
    function testCdnSpeed() {
        // Show loading state
        const speedTestContainer = document.getElementById('cdnSpeedResults');
        if (!speedTestContainer) return;
        
        const library = appState.currentLibrary;
        const version = appState.currentVersion;
        
        if (!library) {
            speedTestContainer.innerHTML = '<div class="alert alert-warning">Library information not available</div>';
            return;
        }
        
        // Get available providers for this library
        const availableProviders = appState.availableProviders.length > 0 
            ? appState.availableProviders 
            : ['cdnjs', 'jsdelivr', 'unpkg']; // Fallback to all if not detected
        
        // Show loading state
        speedTestContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner mb-2"></div>
                <p>Testing connection speed to available CDN providers...</p>
                <p class="small text-muted">Testing: ${availableProviders.map(p => p.toUpperCase()).join(', ')}</p>
            </div>
        `;
        
        // Make API request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', appConfig.endpoints.speedTest, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', appConfig.csrfToken);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    
                    if (data.success) {
                        // Filter results to only include available providers
                        const filteredResults = {};
                        for (const provider of availableProviders) {
                            if (data.results[provider]) {
                                filteredResults[provider] = data.results[provider];
                            }
                        }
                        
                        displaySpeedResults(filteredResults);
                    } else {
                        speedTestContainer.innerHTML = `<div class="alert alert-danger">${data.error || 'Error testing CDN speed'}</div>`;
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    speedTestContainer.innerHTML = '<div class="alert alert-danger">Error processing speed test results</div>';
                }
            } else {
                speedTestContainer.innerHTML = '<div class="alert alert-danger">Error communicating with server</div>';
            }
        };
        
        xhr.onerror = function() {
            speedTestContainer.innerHTML = '<div class="alert alert-danger">Network error during speed test</div>';
        };
        
        // Add available providers to the request
        xhr.send(JSON.stringify({
            library: library,
            version: version,
            providers: availableProviders
        }));
    }

    /**
     * Display CDN speed test results
     * @param {Object} results - Speed test results from server
     */
    function displaySpeedResults(results) {
        const speedTestContainer = document.getElementById('cdnSpeedResults');
        if (!speedTestContainer) return;
        
        // Use this to track the fastest provider
        let fastestProvider = null;
        let fastestTime = Number.MAX_VALUE;
        
        // Determine fastest provider
        for (const [provider, data] of Object.entries(results)) {
            if (data.available && data.time && data.time < fastestTime) {
                fastestTime = data.time;
                fastestProvider = provider;
            }
        }
        
        // Create results HTML
        let html = `
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bx bx-tachometer me-1"></i> {{ __('tools.speed_test_results') }}
                </div>
                <div class="card-body">
                    <p class="card-text mb-3">{{ __('tools.connection_speed') }}</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('tools.provider') }}</th>
                                    <th>{{ __('tools.status') }}</th>
                                    <th>{{ __('tools.response_time') }}</th>
                                    <th>{{ __('tools.recommendation') }}</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        // Add each provider's results
        for (const [provider, data] of Object.entries(results)) {
            // Status badge
            let statusBadge = data.available 
                ? `<span class="badge bg-success">{{ __('tools.available') }}</span>` 
                : `<span class="badge bg-danger">{{ __('tools.unavailable') }}</span>`;
            
            // Response time
            let responseTime = data.time ? `${data.time} ms` : 'N/A';
            
            html += `
                <tr>
                    <td>
                        <strong class="text-capitalize">${provider}</strong>
                    </td>
                    <td>${statusBadge}</td>
                    <td>${responseTime}</td>
                    <td id="recommendation-${provider}"></td>
                </tr>
            `;
        }
        
        html += `
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="bx bx-info-circle me-1"></i> 
                    {{ __('tools.speed_disclaimer') }}
                </div>
            </div>
        `;
        
        // Display the results
        speedTestContainer.innerHTML = html;
        
        // Add recommendations after the table is rendered
        for (const [provider, data] of Object.entries(results)) {
            const recommendationCell = document.getElementById(`recommendation-${provider}`);
            if (!recommendationCell) continue;
            
            if (provider === fastestProvider) {
                recommendationCell.innerHTML = `
                    <span class="badge bg-success">{{ __('tools.fastest') }}</span>
                    <small class="d-block mt-1">{{ __('tools.best_choice') }}</small>
                `;
            } else if (data.available) {
                const percentSlower = data.time && fastestTime 
                    ? Math.round(((data.time - fastestTime) / fastestTime) * 100) 
                    : null;
                
                if (percentSlower !== null) {
                    if (percentSlower < 20) {
                        recommendationCell.innerHTML = `
                            <span class="badge bg-primary">{{ __('tools.good') }}</span>
                            <small class="d-block mt-1">${percentSlower}% {{ __('tools.slower_than_fastest') }}</small>
                        `;
                    } else if (percentSlower < 50) {
                        recommendationCell.innerHTML = `
                            <span class="badge bg-warning text-dark">{{ __('tools.acceptable') }}</span>
                            <small class="d-block mt-1">${percentSlower}% {{ __('tools.slower_than_fastest') }}</small>
                        `;
                    } else {
                        recommendationCell.innerHTML = `
                            <span class="badge bg-danger">{{ __('tools.slow') }}</span>
                            <small class="d-block mt-1">${percentSlower}% {{ __('tools.slower_than_fastest') }}</small>
                        `;
                    }
                } else {
                    recommendationCell.innerHTML = `
                        <span class="badge bg-secondary">{{ __('tools.unknown_speed') }}</span>
                        <small class="d-block mt-1">{{ __('tools.couldnt_determine_speed') }}</small>
                    `;
                }
            } else {
                recommendationCell.innerHTML = `
                    <span class="badge bg-danger">{{ __('tools.not_recommended') }}</span>
                    <small class="d-block mt-1">{{ __('tools.service_unavailable') }}</small>
                `;
            }
        }
    }
    
    /**
     * Handle image loading errors with multiple fallback strategies
     * @param {HTMLImageElement} img - The image element that failed to load
     */
    function handleImageError(img) {
        const src = img.getAttribute('src');
        const library = '{{ $libraryDetails["library"] ?? "" }}';
        const version = '{{ $libraryDetails["version"] ?? "" }}';
        let newSrc = null;
        
        // Check if this is a GitHub repository (has a slash in it)
        const isGitHub = window.libraryRepoInfo.isGitHub || library.includes('/');
        
        console.log('Attempting to fix broken image:', src);
        
        // Strategy 1: Handle GitHub raw content URLs
        if (src && src.includes('raw.githubusercontent.com')) {
            const match = src.match(/https:\/\/raw\.githubusercontent\.com\/([^\/]+)\/([^\/]+)\/([^\/]+)\/(.*)/);
            if (match) {
                const [, user, repo, branch, path] = match;
                newSrc = `https://cdn.jsdelivr.net/gh/${user}/${repo}@${branch}/${path}`;
                console.log('Strategy 1: Converting GitHub raw URL:', newSrc);
            }
        }
        
        // Strategy 2: Library-specific handling for known libraries
        else if (library === 'sweetalert2' && (src.includes('logo') || src.includes('swal2'))) {
            newSrc = `https://cdn.jsdelivr.net/gh/sweetalert2/sweetalert2@${version}/assets/swal2-logo.png`;
            console.log('Strategy 2: Using sweetalert2 specific path:', newSrc);
        }
        
        // Strategy 3: Converting relative paths based on library type
        else if (!src.startsWith('http') && !src.startsWith('data:')) {
            const imgPath = src.startsWith('/') ? src.substring(1) : src;
            
            if (isGitHub && window.libraryRepoInfo.user && window.libraryRepoInfo.repo) {
                // GitHub repository format
                newSrc = `https://cdn.jsdelivr.net/gh/${window.libraryRepoInfo.user}/${window.libraryRepoInfo.repo}@${version}/${imgPath}`;
                console.log('Strategy 3A: Converting relative path using GitHub format:', newSrc);
            } else {
                // NPM package format
                newSrc = `https://cdn.jsdelivr.net/npm/${library}@${version}/${imgPath}`;
                console.log('Strategy 3B: Converting relative path using NPM format:', newSrc);
            }
        }
        
        // Strategy 4: Handle converting npm URLs to GitHub URLs
        else if (src && src.includes('cdn.jsdelivr.net/npm/') && isGitHub) {
            // Extract GitHub user/repo from library name
            const parts = library.split('/');
            if (parts.length === 2) {
                const user = parts[0];
                const repo = parts[1];
                
                // Extract path after version
                const pathMatch = src.match(/cdn\.jsdelivr\.net\/npm\/[^@]+@[^\/]+\/(.+)/);
                if (pathMatch && pathMatch[1]) {
                    newSrc = `https://cdn.jsdelivr.net/gh/${user}/${repo}@${version}/${pathMatch[1]}`;
                    console.log('Strategy 4: Converting npm to GitHub URL:', newSrc);
                }
            }
        }
        
        // Strategy 5: Try comprehensive path search for common image locations
        if (!newSrc) {
            console.log('Strategy 5: Attempting comprehensive path search');
            findImageInCommonLocations(library, version, img);
            return; // Early return as we're handling this asynchronously
        }
        
        // Apply new source if one was determined
        if (newSrc) {
            img.src = newSrc;
            
            // If the new source also fails, try one final fallback approach
            img.onerror = function() {
                console.log('New source failed, attempting final fallback strategy');
                findImageInCommonLocations(library, version, img);
            };
        } else {
            showImageError(img);
        }
    }
    
    /**
     * Search for images in common library locations
     * @param {string} library - The library name
     * @param {string} version - The library version
     * @param {HTMLImageElement} img - The image element
     */
    async function findImageInCommonLocations(library, version, img) {
        const isGitHub = window.libraryRepoInfo.isGitHub || library.includes('/');
        const repoUser = window.libraryRepoInfo.user || (isGitHub ? library.split('/')[0] : '');
        const repoName = window.libraryRepoInfo.repo || (isGitHub ? library.split('/')[1] : '');
        
        // Generate possible image filename from alt text or original src
        const originalSrc = img.getAttribute('src');
        const fileName = originalSrc.split('/').pop();
        const altText = img.alt || (fileName ? fileName.split('.')[0] : '') || 'logo';
        
        // Common locations where images might be found
        const commonPaths = [
            // Alt-text specific paths
            `/assets/${altText}.png`, 
            `/assets/images/${altText}.png`,
            `/assets/img/${altText}.png`,
            `/dist/images/${altText}.png`,
            `/dist/img/${altText}.png`,
            `/images/${altText}.png`,
            `/img/${altText}.png`,
            `/docs/images/${altText}.png`,
            `/docs/img/${altText}.png`,
            
            // Generic logo paths
            `/assets/logo.png`,
            `/assets/images/logo.png`, 
            `/assets/img/logo.png`,
            `/dist/logo.png`,
            `/logo.png`,
            `/images/logo.png`,
            `/img/logo.png`
        ];
        
        // Add SVG variations
        const svgPaths = commonPaths.map(path => path.replace('.png', '.svg'));
        const allPaths = [...commonPaths, ...svgPaths];
        
        let imageFound = false;
        
        // Try GitHub paths if we have GitHub info
        if (isGitHub && repoUser && repoName) {
            for (const path of allPaths) {
                if (imageFound) break;
                
                const testSrc = `https://cdn.jsdelivr.net/gh/${repoUser}/${repoName}@${version}${path}`;
                try {
                    const response = await fetch(testSrc, { method: 'HEAD' });
                    if (response.ok) {
                        console.log('Found image at GitHub path:', testSrc);
                        img.src = testSrc;
                        imageFound = true;
                        break;
                    }
                } catch (e) {
                    // Continue to next path
                }
            }
        }
        
        // Try npm paths if not found or no GitHub info
        if (!imageFound) {
            for (const path of allPaths) {
                if (imageFound) break;
                
                const testSrc = `https://cdn.jsdelivr.net/npm/${library}@${version}${path}`;
                try {
                    const response = await fetch(testSrc, { method: 'HEAD' });
                    if (response.ok) {
                        console.log('Found image at npm path:', testSrc);
                        img.src = testSrc;
                        imageFound = true;
                        break;
                    }
                } catch (e) {
                    // Continue to next path
                }
            }
        }
        
        // If no image is found, show error message
        if (!imageFound) {
            showImageError(img);
        }
    }
    
    /**
     * Display error placeholder when an image cannot be found
     * @param {HTMLImageElement} img - The image element that failed to load
     */
    function showImageError(img) {
        img.style.display = 'none';
        const errorText = document.createElement('span');
        errorText.className = 'image-error';
        errorText.textContent = '[Image not found]';
        errorText.style.color = '#ee0000';
        errorText.style.fontStyle = 'italic';
        img.parentNode.insertBefore(errorText, img.nextSibling);
    }
    
    /**
     * Copy text to clipboard
     * @param {string} text - Text to copy
     */
    window.copyToClipboard = function(text) {
        // Create a temporary textarea
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';  // Avoid scrolling to bottom
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            // Execute copy command
            document.execCommand('copy');
            
            // Show toast notification
            showToast(appConfig.translations.copied, 'success');
        } catch (err) {
            console.error('Failed to copy text: ', err);
            showToast(appConfig.translations.nothingToCopy, 'error');
        }
        
        // Remove the temporary element
        document.body.removeChild(textarea);
    };
    
    /**
     * Show toast notification
     * @param {string} message - Message to show
     * @param {string} type - Toast type (success or error)
     */
    function showToast(message, type = 'success') {
        // Use SweetAlert2 toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.library) {
            loadLibraryDetails(event.state.library, event.state.version || '');
        } else {
            window.location.reload(); // Fallback to reload
        }
    });
})();
</script>
@endsection