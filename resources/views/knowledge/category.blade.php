@extends('layouts.master')

@section('title') {{ $category->name }} - @lang('translation.Knowledge_Base') @endsection

@section('css')
<!-- Boxicons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
    /* Knowledge Base specific styles */
    .kb-search-wrapper {
        position: relative;
    }
    
    .kb-search-wrapper .input-group-text {
        background-color: transparent;
        border-right: 0;
    }
    
    .kb-search-wrapper .form-control {
        border-left: 0;
    }
    
    .kb-search-btn {
        border-radius: 0 4px 4px 0 !important;
    }
    
    /* Badge styles with solid colors */
    .kb-badge {
        font-size: 10px;
        padding: 4px 8px;
        font-weight: 500;
        border-radius: 3px;
    }
    
    .kb-featured-badge {
        background-color: #f6645b;
        color: #ffffff;
    }
    
    .kb-popular-badge {
        background-color: #ffbf53;
        color: #ffffff;
    }
    
    .kb-article-link {
        color: var(--bs-body-color);
        text-decoration: none;
    }
    
    .kb-article-link:hover {
        color: #5156be;
    }
    
    /* Stat cards */
    .kb-stat-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 4px;
        padding: 1.5rem;
        margin-bottom: 15px;
    }
    
    .kb-stat-icon {
        color: #5156be;
        margin-right: 15px;
        font-size: 24px;
    }
    
    .kb-stat-number {
        font-size: 28px;
        font-weight: 600;
        color: var(--bs-body-color);
        margin-bottom: 0;
    }
    
    .kb-stat-label {
        font-size: 16px;
        color: var(--bs-secondary-color);
    }
    
    /* Article card */
    .kb-article-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 4px;
        margin-bottom: 15px;
    }
    
    .kb-article-card .card-body {
        padding: 1.25rem;
    }
    
    .kb-article-preview {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    /* Live search */
    #live-search-results {
        border: 1px solid var(--bs-border-color);
        border-radius: 4px;
        background: var(--bs-card-bg);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1050;
    }
    
    .search-result-item {
        padding: 10px;
        border-bottom: 1px solid var(--bs-border-color);
    }
    
    .search-result-item:hover {
        background-color: var(--bs-light);
    }
    
    .search-section-title {
        font-size: 12px;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') <a href="{{ route('knowledge.index') }}">@lang('translation.Knowledge_Base')</a> @endslot
        @slot('title') {{ $category->name }} @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Category Header -->
                    <div class="d-flex align-items-center mb-4">
                        <i class="mdi {{ $category->icon ?? 'mdi-folder' }} text-primary me-3" style="font-size: 28px;"></i>
                        <div>
                            <h4 class="mb-1">{{ $category->name }}</h4>
                            @if($category->description)
                                <p class="text-muted mb-0">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Search Box -->
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="kb-search-wrapper">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                    <input type="text" id="live-search-input" class="form-control" placeholder="@lang('translation.Search_within_category', ['category' => $category->name])">
                                    <button type="button" class="btn btn-primary kb-search-btn">@lang('translation.Search')</button>
                                </div>
                                
                                <!-- Live Search Results -->
                                <div id="live-search-results" class="position-absolute start-0 end-0 mt-1 d-none">
                                    <div id="search-results-content" class="p-3">
                                        <!-- Results will be loaded here -->
                                    </div>
                                    <div id="search-results-footer" class="border-top p-2 text-center bg-light d-none">
                                        <a href="#" id="view-all-results" class="text-primary">@lang('translation.View_all_results') <i class="mdi mdi-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="text-lg-end mt-3 mt-lg-0">
                                <div class="d-inline-block">
                                    <div class="dropdown">
                                        <button class="btn btn-soft-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-filter-outline me-1"></i> @lang('translation.Sort_by')
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">@lang('translation.Latest')</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}">@lang('translation.Oldest')</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}">@lang('translation.Popular')</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'a-z']) }}">@lang('translation.A_Z')</a></li>
                                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'z-a']) }}">@lang('translation.Z_A')</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Category Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="kb-stat-card">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-file-document-outline kb-stat-icon"></i>
                                    <div>
                                        <h3 class="kb-stat-number">{{ $articles->total() }}</h3>
                                        <p class="kb-stat-label">@lang('translation.Total_Articles')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kb-stat-card">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-eye-outline kb-stat-icon"></i>
                                    <div>
                                        @php
                                            $totalViews = \App\Models\KnowledgeArticle::where('category_id', $category->id)
                                                ->where('is_published', true)
                                                ->sum('view_count');
                                        @endphp
                                        <h3 class="kb-stat-number">{{ number_format($totalViews) }}</h3>
                                        <p class="kb-stat-label">@lang('translation.Total_Views')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kb-stat-card">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-star-outline kb-stat-icon"></i>
                                    <div>
                                        @php
                                            $featuredCount = \App\Models\KnowledgeArticle::where('category_id', $category->id)
                                                ->where('is_published', true)
                                                ->where('is_featured', true)
                                                ->count();
                                        @endphp
                                        <h3 class="kb-stat-number">{{ $featuredCount }}</h3>
                                        <p class="kb-stat-label">@lang('translation.Featured_Articles')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="kb-stat-card">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-clock-outline kb-stat-icon"></i>
                                    <div>
                                        @php
                                            $latestArticle = \App\Models\KnowledgeArticle::where('category_id', $category->id)
                                                ->where('is_published', true)
                                                ->latest('published_at')
                                                ->first();
                                                
                                            $lastUpdated = $latestArticle ? $latestArticle->published_at->diffForHumans() : 'N/A';
                                        @endphp
                                        <h3 class="kb-stat-number">{{ $lastUpdated }}</h3>
                                        <p class="kb-stat-label">@lang('translation.Last_Updated')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Articles List -->
                    <div class="row">
                        @if($articles->count() > 0)
                            @foreach($articles as $article)
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card kb-article-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                @if($article->is_featured)
                                                    <span class="kb-badge kb-featured-badge me-2">@lang('translation.Featured')</span>
                                                @endif
                                                
                                                @if($article->view_count > 100)
                                                    <span class="kb-badge kb-popular-badge me-2">@lang('translation.Popular')</span>
                                                @endif
                                            </div>
                                            
                                            <h5 class="card-title">
                                                <a href="{{ route('knowledge.article', ['category' => $category->slug, 'article' => $article->slug]) }}" class="kb-article-link">
                                                    {{ $article->title }}
                                                </a>
                                            </h5>
                                            
                                            <p class="text-muted kb-article-preview mb-3">
                                                {{ $article->excerpt ? $article->excerpt : Str::limit(strip_tags($article->content), 120) }}
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="mdi mdi-calendar me-1"></i>
                                                    <span>{{ $article->published_at->format('M d, Y') }}</span>
                                                </div>
                                                <div>
                                                    <i class="mdi mdi-eye-outline me-1"></i>
                                                    <span>{{ $article->view_count }} @lang('translation.views')</span>
                                                </div>
                                            </div>
                                            
                                            @if($article->author)
                                                <div class="mt-3 pt-2 border-top">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $article->author->getGravatarUrl(28) }}" alt="{{ $article->author->name }}" class="rounded-circle me-2" width="28">
                                                        <div>
                                                            <span class="fw-medium">{{ $article->author->name }}</span>
                                                            @if($article->author->isAdmin())
                                                                <span class="badge bg-soft-danger text-danger ms-1 fw-normal">@lang('translation.Admin')</span>
                                                            @elseif($article->author->isSupport())
                                                                <span class="badge bg-soft-success text-success ms-1 fw-normal">@lang('translation.Support')</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        
                            <!-- Pagination -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $articles->links() }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="col-12 text-center py-5">
                                <div class="card">
                                    <div class="card-body">
                                        <i class="mdi mdi-file-document-outline" style="font-size: 48px; color: #adb5bd; margin-bottom: 1rem;"></i>
                                        <h4>@lang('translation.No_articles_found')</h4>
                                        <p class="text-muted">@lang('translation.No_articles_in_category')</p>
                                        <a href="{{ route('knowledge.index') }}" class="btn btn-primary mt-2">
                                            <i class="mdi mdi-arrow-left me-1"></i> @lang('translation.Back_to_Knowledge_Base')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Related Categories -->
                    <div class="mt-4">
                        <h5 class="mb-3">@lang('translation.Related_Categories')</h5>
                        <div class="row">
                            @foreach(\App\Models\KnowledgeCategory::where('is_active', true)
                                ->where('id', '!=', $category->id)
                                ->withCount(['activeArticles'])
                                ->orderBy('sort_order')
                                ->take(3)
                                ->get() as $relatedCategory)
                                <div class="col-lg-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <i class="mdi {{ $relatedCategory->icon ?? 'mdi-folder' }} text-primary me-3" style="font-size: 24px;"></i>
                                                <div>
                                                    <h5 class="mb-0">
                                                        <a href="{{ route('knowledge.category', $relatedCategory->slug) }}" class="kb-article-link fw-medium">
                                                            {{ $relatedCategory->name }}
                                                        </a>
                                                    </h5>
                                                    <p class="text-muted mb-0 fs-13">{{ $relatedCategory->active_articles_count }} @lang('translation.Articles')</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('live-search-input');
    const searchButton = document.querySelector('.kb-search-btn');
    const searchResults = document.getElementById('live-search-results');
    const searchResultsContent = document.getElementById('search-results-content');
    const searchResultsFooter = document.getElementById('search-results-footer');
    const viewAllResults = document.getElementById('view-all-results');
    const searchUrl = "{{ route('knowledge.search') }}";
    const ajaxSearchUrl = "{{ route('knowledge.ajax-search') }}";
    
    let searchTimeout;
    
    if (!searchInput || !searchResults || !searchResultsContent) {
        console.error('@lang("translation.Search_elements_not_found")');
        return;
    }
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchResults.classList.add('d-none');
            return;
        }
        
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, 300);
    });
    
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query) {
                window.location.href = searchUrl + '?query=' + encodeURIComponent(query);
            }
        });
    }
    
    document.addEventListener('click', function(e) {
        if (searchResults && !searchInput.contains(e.target) && !searchResults.contains(e.target) && !searchButton.contains(e.target)) {
            searchResults.classList.add('d-none');
        }
    });
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = searchUrl + '?query=' + encodeURIComponent(query);
            }
        }
    });
    
    function updateViewAllLink(query) {
        if (viewAllResults) {
            viewAllResults.href = searchUrl + '?query=' + encodeURIComponent(query);
        }
    }
    
    function performSearch(query) {
        searchResultsContent.innerHTML = '<div class="text-center py-3"><i class="mdi mdi-loading mdi-spin me-1"></i> @lang("translation.Searching")</div>';
        searchResults.classList.remove('d-none');
        
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
    
    function displayResults(data) {
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
        
        if (hasCategories) {
            html += '<div class="search-categories-section mb-2">';
            html += '<h6 class="search-section-title text-muted mb-2">@lang("translation.Categories")</h6>';
            
            data.results.categories.forEach(category => {
                html += `
                <div class="search-result-item">
                    <div class="mb-1"><a href="${category.url}" class="text-primary fw-bold">${category.name}</a> <span class="badge bg-light text-dark">${category.article_count} @lang("translation.articles")</span></div>
                    <p class="small text-muted mb-0">${category.description || '@lang("translation.Browse_articles_in_category")'}</p>
                </div>
                `;
            });
            
            html += '</div>';
            
            if (hasArticles) {
                html += '<hr class="my-2">';
            }
        }
        
        if (hasArticles) {
            html += '<div class="search-articles-section">';
            
            if (hasCategories) {
                html += '<h6 class="search-section-title text-muted mb-2">@lang("translation.Articles")</h6>';
            }
            
            data.results.articles.forEach((article, index) => {
                html += `
                <div class="search-result-item">
                    <div class="mb-1"><a href="${article.url}" class="text-primary">${article.title}</a></div>
                    <div class="d-flex align-items-center mb-1">
                        <span class="badge bg-light text-dark me-2">
                            <a href="${article.category.url}" class="text-dark">${article.category.name}</a>
                        </span>
                        <small class="text-muted">${article.published_at}</small>
                    </div>
                    <p class="small text-muted mb-0">${article.excerpt}</p>
                </div>
                `;
            });
            
            html += '</div>';
        }
        
        searchResultsContent.innerHTML = html;
        
        if (searchResultsFooter) {
            if (data.count >= 5) {
                searchResultsFooter.classList.remove('d-none');
            } else {
                searchResultsFooter.classList.add('d-none');
            }
        }
    }
});
</script>
@endsection