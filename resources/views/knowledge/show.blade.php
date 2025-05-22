@extends('layouts.master')

@section('title') {{ $article->title }} - @lang('translation.Knowledge_Base') @endsection

@section('css')
<!-- Boxicons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
    /* Main container */
    .kb-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* Article header */
    .kb-article-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .kb-breadcrumb {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
        color: #6c757d;
    }
    
    .kb-breadcrumb a {
        color: #0556b3;
        text-decoration: none;
        transition: color 0.15s ease-in-out;
    }
    
    .kb-breadcrumb a:hover {
        color: #033b7a;
        text-decoration: underline;
    }
    
    .kb-breadcrumb .separator {
        margin: 0 0.5rem;
        color: #adb5bd;
    }
    
    /* Article title and metadata */
    .kb-article-title {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        color: #212529;
        line-height: 1.2;
    }
    
    .kb-article-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.25rem;
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    .kb-article-meta-item {
        display: flex;
        align-items: center;
    }
    
    .kb-article-meta-item i {
        margin-right: 0.5rem;
    }
    
    .kb-article-author {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .kb-article-author-img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        margin-right: 0.75rem;
        object-fit: cover;
        border: 2px solid #f8f9fa;
    }
    
    .kb-article-author-info {
        display: flex;
        flex-direction: column;
    }
    
    .kb-article-author-name {
        font-weight: 600;
        line-height: 1.2;
        color: #212529;
    }
    
    .kb-admin-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        border-radius: 0.25rem;
        background-color: #dc3545;
        color: #fff;
        margin-left: 0.5rem;
    }
    
    .kb-support-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        border-radius: 0.25rem;
        background-color: #198754;
        color: #fff;
        margin-left: 0.5rem;
    }
    
    .kb-author-updated {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    /* Article content */
    .kb-article-content {
        background-color: #ffffff;
        padding: 2rem;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        line-height: 1.7;
        font-size: 1.05rem;
        color: #212529;
    }
    
    .kb-article-content h1, 
    .kb-article-content h2, 
    .kb-article-content h3, 
    .kb-article-content h4, 
    .kb-article-content h5, 
    .kb-article-content h6 {
        margin-top: 1.75rem;
        margin-bottom: 1rem;
        font-weight: 600;
        color: #212529;
    }
    
    .kb-article-content h2 {
        font-size: 1.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .kb-article-content h3 {
        font-size: 1.5rem;
    }
    
    .kb-article-content p {
        margin-bottom: 1.25rem;
    }
    
    .kb-article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 1.5rem 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    
    .kb-article-content pre {
        background-color: #f8f9fa;
        padding: 1.25rem;
        border-radius: 4px;
        overflow-x: auto;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
    }
    
    .kb-article-content code {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.875em;
        color: #d63384;
        background-color: #f8f9fa;
        padding: 0.2em 0.4em;
        border-radius: 3px;
    }
    
    .kb-article-content ul, 
    .kb-article-content ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }
    
    .kb-article-content li {
        margin-bottom: 0.5rem;
    }
    
    .kb-article-content a {
        color: #0556b3;
        text-decoration: none;
    }
    
    .kb-article-content a:hover {
        text-decoration: underline;
    }
    
    .kb-article-content blockquote {
        border-left: 4px solid #0556b3;
        padding: 0.5rem 0 0.5rem 1.5rem;
        margin: 1.5rem 0;
        color: #495057;
        font-style: italic;
    }
    
    /* Responsive YouTube Embeds */
    .responsive-embed-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        max-width: 100%;
        margin-bottom: 20px;
    }
    
    .responsive-embed-container iframe,
    .responsive-embed-container object,
    .responsive-embed-container embed {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
    
    /* Fix for YouTube iframes */
    .kb-article-content iframe[src*="youtube.com/embed"],
    .kb-article-content iframe[src*="youtu.be"],
    .kb-article-content iframe[src*="youtube-nocookie.com/embed"] {
        max-width: 100%;
    }
    
    @media (max-width: 767.98px) {
        .kb-article-content iframe[src*="youtube.com/embed"],
        .kb-article-content iframe[src*="youtu.be"],
        .kb-article-content iframe[src*="youtube-nocookie.com/embed"] {
            width: 100% !important;
            height: auto !important;
            aspect-ratio: 16/9;
        }
    }
    
    /* Rating section */
    .kb-rating-section {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 4px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .kb-rating-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #212529;
    }
    
    .kb-rating-buttons {
        display: flex;
        gap: 1rem;
    }
    
    .kb-rating-button {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.25rem;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        background-color: #fff;
        color: #495057;
        font-weight: 500;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
    }
    
    .kb-rating-button:hover {
        background-color: #f8f9fa;
        border-color: #ced4da;
    }
    
    .kb-rating-button i {
        margin-right: 0.5rem;
        font-size: 1.25rem;
    }
    
    .kb-rating-button.active-like {
        background-color: #d1e7dd;
        color: #0f5132;
        border-color: #a3cfbb;
    }
    
    .kb-rating-button.active-dislike {
        background-color: #f8d7da;
        color: #842029;
        border-color: #f5c2c7;
    }
    
    .kb-rating-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.5rem;
        height: 1.5rem;
        padding: 0 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        background-color: rgba(0, 0, 0, 0.1);
        color: inherit;
        border-radius: 0.75rem;
        margin-left: 0.5rem;
    }
    
    /* Related articles and search */
    .kb-sidebar-section {
        background-color: #ffffff;
        padding: 1.5rem;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .kb-sidebar-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #212529;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    /* Sidebar search box */
    .kb-sidebar-search {
        position: relative;
        margin-bottom: 0.75rem;
    }
    
    .kb-sidebar-search .input-group {
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    
    .kb-sidebar-search .input-group-text {
        background-color: #f8f9fa;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        border-right: none;
    }
    
    .kb-sidebar-search .form-control {
        height: 40px;
        background-color: #f8f9fa;
        border-left: none;
    }
    
    /* Live search results styling */
    #live-search-results {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        right: 0;
        z-index: 1050;
        max-height: 400px;
        overflow-y: auto;
        border-radius: 4px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .kb-related-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .kb-related-item {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .kb-related-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .kb-related-link {
        display: block;
        color: #212529;
        font-weight: 500;
        text-decoration: none;
        margin-bottom: 0.375rem;
        transition: color 0.15s ease-in-out;
    }
    
    .kb-related-link:hover {
        color: #0556b3;
    }
    
    .kb-related-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    /* Search section */
    .search-section-title {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    
    .search-result-item:last-child {
        margin-bottom: 0 !important;
    }
    
    .search-category-item h6 {
        font-weight: 600;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 767.98px) {
        .kb-article-title {
            font-size: 1.75rem;
        }
        
        .kb-article-meta {
            gap: 1rem;
        }
        
        .kb-article-content {
            padding: 1.5rem;
        }
        
        #live-search-results {
            position: absolute;
            width: 100%;
            max-height: 300px;
        }
        
        .kb-rating-buttons {
            flex-direction: column;
        }
    }
    /* Hide Froala branding - Phiên bản nâng cao */
    a[id="fr-logo"] { display: none !important; }
    p[data-f-id="pbf"] { display: none !important; }
    a[href*="www.froala.com"] { display: none !important; }
    div[style*="z-index: 9999"] { display: none !important; }
    .fr-wrapper > div[style*="z-index:9999"] { display: none !important; }
    .fr-element + p { display: none !important; }
    .fr-wrapper > p[class=""] { display: none !important; }
    .fr-box p[data-f-id="pbf"] { display: none !important; }
    .fr-view p[data-f-id="pbf"] { display: none !important; }
    body p[data-f-id="pbf"] { display: none !important; }
</style>
@endsection

@section('content')
    <div class="kb-container">
        <div class="row">
            <!-- Main Content Column -->
            <div class="col-lg-8">
                <!-- Article Header -->
                <div class="kb-article-header">
                    <!-- Breadcrumb -->
                    <div class="kb-breadcrumb">
                        <a href="{{ route('knowledge.index') }}">@lang('translation.Knowledge_Base')</a>
                        <span class="separator">
                            <i class="bx bx-chevron-right"></i>
                        </span>
                        <a href="{{ route('knowledge.category', $category->slug) }}">{{ $category->name }}</a>
                        <span class="separator">
                            <i class="bx bx-chevron-right"></i>
                        </span>
                        <span>{{ $article->title }}</span>
                    </div>
                    
                    <!-- Article Title -->
                    <h1 class="kb-article-title">{{ $article->title }}</h1>
                    
                    <!-- Article Meta -->
                    <div class="kb-article-meta">
                        <div class="kb-article-meta-item">
                            <i class="bx bx-calendar"></i>
                            <span>{{ $article->published_at->format('M d, Y') }}</span>
                        </div>
                        <div class="kb-article-meta-item">
                            <i class="bx bx-show"></i>
                            <span>{{ $article->view_count }} @lang('translation.views')</span>
                        </div>
                        <div class="kb-article-meta-item">
                            <i class="bx bx-folder"></i>
                            <a href="{{ route('knowledge.category', $category->slug) }}" class="text-reset">
                                {{ $category->name }}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Author Information -->
                    @if($article->author)
                    <div class="kb-article-author">
                        <img src="{{ $article->author->getGravatarUrl(80) }}" alt="{{ $article->author->name }}" class="kb-article-author-img">
                        <div class="kb-article-author-info">
                            <div>
                                <span class="kb-article-author-name">{{ $article->author->name }}</span>
                                @if($article->author->isAdmin())
                                    <span class="kb-admin-badge">@lang('translation.Admin')</span>
                                @elseif($article->author->isSupport())
                                    <span class="kb-support-badge">@lang('translation.Support')</span>
                                @endif
                            </div>
                            <span class="kb-author-updated">@lang('translation.Updated') {{ $article->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Article Content -->
                <div class="kb-article-content">
                    {!! $article->content !!}
                </div>
                
                <!-- Rating Section -->
                <div class="kb-rating-section">
                    <h3 class="kb-rating-title">@lang('translation.Was_article_helpful')</h3>
                    <div class="kb-rating-buttons">
                        @if(auth()->check())
                            <button type="button" class="kb-rating-button {{ $userRating && $userRating->is_helpful ? 'active-like' : '' }}" id="likeButton" data-rating="1">
                                <i class="bx bx-like"></i> @lang('translation.Yes_helpful')
                                <span class="kb-rating-count" id="likesCount">{{ $article->getLikesCountAttribute() }}</span>
                            </button>
                            <button type="button" class="kb-rating-button {{ $userRating && !$userRating->is_helpful ? 'active-dislike' : '' }}" id="dislikeButton" data-rating="0">
                                <i class="bx bx-dislike"></i> @lang('translation.No_not_helpful')
                                <span class="kb-rating-count" id="dislikesCount">{{ $article->getDislikesCountAttribute() }}</span>
                            </button>
                        @else
                            <div class="alert alert-info mb-0 w-100">
                                <i class="bx bx-info-circle me-1"></i> @lang('translation.Please') <a href="{{ route('login') }}">@lang('translation.login')</a> @lang('translation.to_rate_article').
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Column -->
            <div class="col-lg-4">
                <!-- Search Section -->
                <div class="kb-sidebar-section">
                    <h3 class="kb-sidebar-title">@lang('translation.Search_Knowledge_Base')</h3>
                    <div class="kb-sidebar-search">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bx bx-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control bg-light border-start-0" id="live-search-input" placeholder="@lang('translation.Search_for_articles_placeholder')" autocomplete="off">
                        </div>
                        
                        <!-- Live Search Results -->
                        <div id="live-search-results" class="d-none">
                            <div class="card shadow">
                                <div class="card-body p-0">
                                    <div id="search-results-content" class="p-3">
                                        <!-- Results will be loaded here -->
                                    </div>
                                    <div id="search-results-footer" class="border-top p-2 text-center bg-light d-none">
                                        <a href="#" id="view-all-results" class="text-primary">@lang('translation.View_all_results') <i class="bx bx-right-arrow-alt"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Articles -->
                @if($relatedArticles->count() > 0)
                    <div class="kb-sidebar-section">
                        <h3 class="kb-sidebar-title">@lang('translation.Related_Articles')</h3>
                        <ul class="kb-related-list">
                            @foreach($relatedArticles as $relatedArticle)
                                <li class="kb-related-item">
                                    <a href="{{ route('knowledge.article', ['category' => $category->slug, 'article' => $relatedArticle->slug]) }}" class="kb-related-link">
                                        {{ $relatedArticle->title }}
                                    </a>
                                    <div class="kb-related-meta">
                                        <span>
                                            <i class="bx bx-calendar me-1"></i> {{ $relatedArticle->published_at->format('M d, Y') }}
                                        </span>
                                        <span>
                                            <i class="bx bx-show me-1"></i> {{ $relatedArticle->view_count }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Categories Section -->
                <div class="kb-sidebar-section">
                    <h3 class="kb-sidebar-title">@lang('translation.Categories')</h3>
                    <div class="list-group">
                        @foreach(\App\Models\KnowledgeCategory::where('is_active', true)->orderBy('sort_order')->get() as $cat)
                            <a href="{{ route('knowledge.category', $cat->slug) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $cat->id === $category->id ? 'active' : '' }}">
                                {{ $cat->name }}
                                <span class="badge bg-primary rounded-pill">
                                    {{ $cat->activeArticles()->count() }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix YouTube videos to be responsive
    function makeYouTubeVideosResponsive() {
        // Find all YouTube iframes that aren't already in responsive containers
        const youtubeIframes = document.querySelectorAll('.kb-article-content iframe[src*="youtube.com/embed"], .kb-article-content iframe[src*="youtu.be"], .kb-article-content iframe[src*="youtube-nocookie.com/embed"]');
        
        youtubeIframes.forEach(function(iframe) {
            // Skip if already in a responsive container
            if (iframe.parentNode.className === 'responsive-embed-container') {
                return;
            }
            
            // Create responsive container
            const container = document.createElement('div');
            container.className = 'responsive-embed-container';
            
            // Get iframe's parent
            const parent = iframe.parentNode;
            
            // Replace iframe with container
            parent.replaceChild(container, iframe);
            
            // Add iframe to container
            container.appendChild(iframe);
            
            // Ensure iframe has proper mobile attributes if not already present
            if (!iframe.getAttribute('loading')) {
                iframe.setAttribute('loading', 'lazy');
            }
            
            if (!iframe.getAttribute('allowfullscreen')) {
                iframe.setAttribute('allowfullscreen', '');
            }
            
            // Update URL to use youtube-nocookie.com if not already
            let src = iframe.getAttribute('src');
            if (src.includes('youtube.com/embed') && !src.includes('youtube-nocookie.com')) {
                src = src.replace('youtube.com/embed', 'youtube-nocookie.com/embed');
                
                // Add mobile-friendly parameters if not present
                if (!src.includes('playsinline')) {
                    src = src.includes('?') ? 
                          src + '&playsinline=1&fs=1' : 
                          src + '?playsinline=1&fs=1';
                }
                
                iframe.setAttribute('src', src);
            }
        });
    }
    
    // Call the function to fix YouTube videos
    makeYouTubeVideosResponsive();

    const searchUrl = "{{ route('knowledge.search') }}";
    const ajaxSearchUrl = "{{ route('knowledge.ajax-search') }}";

    @if(auth()->check())
        // Rating buttons
        const likeButton = document.getElementById('likeButton');
        const dislikeButton = document.getElementById('dislikeButton');
        const likesCount = document.getElementById('likesCount');
        const dislikesCount = document.getElementById('dislikesCount');
        
        // Add click events to rating buttons
        if (likeButton && dislikeButton) {
            likeButton.addEventListener('click', function() {
                rateArticle(1);
            });
            
            dislikeButton.addEventListener('click', function() {
                rateArticle(0);
            });
        }
        
        // Function to handle article rating
        function rateArticle(isHelpful) {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Send AJAX request
            fetch('{{ route("knowledge.rate", $article) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    is_helpful: isHelpful === 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    likesCount.textContent = data.likes_count;
                    dislikesCount.textContent = data.dislikes_count;
                    
                    // Update button styles
                    if (isHelpful === 1) {
                        likeButton.classList.add('active-like');
                        dislikeButton.classList.remove('active-dislike');
                    } else {
                        dislikeButton.classList.add('active-dislike');
                        likeButton.classList.remove('active-like');
                    }
                }
            })
            .catch(error => {
                console.error('@lang("translation.Error_rating_article"):', error);
            });
        }
    @endif
    
    // Live Search
    const searchInput = document.getElementById('live-search-input');
    const searchResults = document.getElementById('live-search-results');
    const searchResultsContent = document.getElementById('search-results-content');
    const searchResultsFooter = document.getElementById('search-results-footer');
    const viewAllResults = document.getElementById('view-all-results');
    
    let searchTimeout;
    
    // Don't proceed if search elements aren't present
    if (!searchInput || !searchResults || !searchResultsContent) {
        console.error('@lang("translation.Search_elements_not_found")');
        return;
    }
    
    // Perform search when typing
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.classList.add('d-none');
            return;
        }
        
        // Set new timeout to prevent too many requests
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, 300);
    });
    
    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (searchResults && !searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('d-none');
        }
    });
    
    // Handle enter key press
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = searchUrl + '?query=' + encodeURIComponent(query);
            }
        }
    });
    
    // Update view all link
    function updateViewAllLink(query) {
        if (viewAllResults) {
            viewAllResults.href = searchUrl + '?query=' + encodeURIComponent(query);
        }
    }
    
    // Perform the search via AJAX
    function performSearch(query) {
        // Show loading state
        searchResultsContent.innerHTML = '<div class="text-center py-3"><i class="bx bx-loader-alt bx-spin me-1"></i> @lang("translation.Searching")</div>';
        searchResults.classList.remove('d-none');
        searchResults.style.display = 'block';
        
        // Fetch results
        fetch(ajaxSearchUrl + '?query=' + encodeURIComponent(query))
            .then(response => {
                if (!response.ok) {
                    throw new Error('@lang("translation.Network_response_error"): ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateViewAllLink(query);
                    displayResults(data);
                } else {
                    let errorMessage = '@lang("translation.No_results_found")';
                    if (data.error) {
                        errorMessage = data.error;
                    }
                    searchResultsContent.innerHTML = '<div class="text-center py-3">' + errorMessage + '</div>';
                    
                    if (searchResultsFooter) {
                        searchResultsFooter.classList.add('d-none');
                    }
                }
            })
            .catch(error => {
                console.error('@lang("translation.Search_error"):', error);
                searchResultsContent.innerHTML = '<div class="text-center py-3">@lang("translation.An_error_occurred")</div>';
                
                if (searchResultsFooter) {
                    searchResultsFooter.classList.add('d-none');
                }
            });
    }
    
    // Display search results
    function displayResults(data) {
        // Force the container to be visible
        searchResults.style.display = 'block';
        searchResults.classList.remove('d-none');
        
        const hasArticles = data.results.articles && data.results.articles.length > 0;
        const hasCategories = data.results.categories && data.results.categories.length > 0;
        
        if (!hasArticles && !hasCategories) {
            searchResultsContent.innerHTML = '<div class="text-center py-3">@lang("translation.No_results_found_for") "' + data.query + '"</div>';
            
            if (searchResultsFooter) {
                searchResultsFooter.classList.add('d-none');
            }
            return;
        }
        
        let html = '';
        
        // Add categories section if categories exist
        if (hasCategories) {
            html += '<div class="search-categories-section mb-2">';
            html += '<h6 class="search-section-title text-muted mb-2">@lang("translation.Categories")</h6>';
            
            data.results.categories.forEach(category => {
                html += `
                <div class="search-category-item mb-2">
                    <h6 class="mb-1"><a href="${category.url}" class="text-primary fw-bold">${category.name}</a> <span class="badge bg-light text-dark">${category.article_count} @lang("translation.articles")</span></h6>
                    <p class="small text-muted mb-0">${category.description || '@lang("translation.Browse_articles_in_category")'}</p>
                </div>
                `;
            });
            
            html += '</div>';
            
            // Add separator if both categories and articles exist
            if (hasArticles) {
                html += '<hr class="my-2">';
            }
        }
        
        // Add articles section if articles exist
        if (hasArticles) {
            html += '<div class="search-articles-section">';
            
            if (hasCategories) {
                html += '<h6 class="search-section-title text-muted mb-2">@lang("translation.Articles")</h6>';
            }
            
            data.results.articles.forEach((article, index) => {
                html += `
                <div class="search-result-item mb-2">
                    <h6 class="mb-1"><a href="${article.url}" class="text-primary">${article.title}</a></h6>
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-light text-dark me-2">
                            <a href="${article.category.url}" class="text-dark">${article.category.name}</a>
                        </span>
                        <small class="text-muted">${article.published_at}</small>
                    </div>
                    <p class="small text-muted mb-0">${article.excerpt}</p>
                </div>
                `;
                
                // Add divider except for the last item
                if (data.results.articles.length > 1 && index < data.results.articles.length - 1) {
                    html += '<hr class="my-2">';
                }
            });
            
            html += '</div>';
        }
        
        searchResultsContent.innerHTML = html;
        
        // Show "View all" if there might be more results
        if (searchResultsFooter) {
            if (data.total >= 5) {
                searchResultsFooter.classList.remove('d-none');
            } else {
                searchResultsFooter.classList.add('d-none');
            }
        }
    }
});
</script>
@endsection