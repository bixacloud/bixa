@extends('layouts.master')

@section('title') @lang('translation.Search_Results') - @lang('translation.Knowledge_Base') @endsection

@section('css')
<!-- Boxicons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
    /* Search box styling (giống với index) */
    .kb-search-wrapper {
        position: relative;
        max-width: 100%;
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
    
    /* Article styling */
    .kb-search-result-item {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
        background-color: var(--bs-card-bg);
    }
    
    .kb-search-result-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }
    
    /* Article title - giới hạn chiều dài trên mobile */
    .kb-article-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .kb-article-title a {
        color: var(--bs-body-color);
        text-decoration: none;
    }
    
    .kb-article-title a:hover {
        color: var(--bs-primary);
    }
    
    /* Article metadata */
    .kb-article-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 0.75rem;
        gap: 0.5rem;
    }
    
    /* Article excerpt - giới hạn chiều dài */
    .kb-article-excerpt {
        margin-bottom: 1rem;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
    
    /* Footer với author và actions */
    .kb-article-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    /* Author styling */
    .kb-article-author {
        display: flex;
        align-items: center;
    }
    
    .kb-article-author img {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-right: 8px;
    }
    
    /* Actions styling */
    .kb-article-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Search term highlight */
    .highlight {
        background-color: rgba(81, 86, 190, 0.15);
        border-radius: 2px;
        padding: 0 3px;
        font-weight: 500;
    }
    
    /* Live search results */
    #live-search-results {
        border: 1px solid var(--bs-border-color);
        border-radius: 4px;
        background-color: var(--bs-card-bg);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1050;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        .kb-article-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .kb-article-actions {
            margin-top: 0.5rem;
            width: 100%;
            justify-content: space-between;
        }
        
        .kb-article-meta {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') 
            <a href="{{ route('knowledge.index') }}">@lang('translation.Knowledge_Base')</a> 
        @endslot
        @slot('title') @lang('translation.Search_Results') @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Search form - đồng bộ với index -->
                    <div class="row mb-4">
                        <div class="col-lg-8 mx-auto">
                            <div class="kb-search-wrapper">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                    <input type="text" id="live-search-input" class="form-control" value="{{ $query }}" placeholder="@lang('translation.Search_knowledge_base')">
                                    <button type="button" id="search-btn" class="btn btn-primary kb-search-btn">@lang('translation.Search')</button>
                                </div>
                                
                                <!-- Live Search Results Container -->
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
                    </div>
                    
                    <!-- Search results header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">@lang('translation.Search_Results_for') "{{ $query }}"</h4>
                        <span class="badge bg-primary">{{ $articles->total() }} @lang('translation.Results')</span>
                    </div>

                    <!-- Search results list -->
                    @if($articles->count() > 0)
                        @foreach($articles as $article)
                            <div class="kb-search-result-item">
                                <h5 class="kb-article-title">
                                    <a href="{{ route('knowledge.article', ['category' => $article->category->slug, 'article' => $article->slug]) }}">
                                        {{ $article->title }}
                                    </a>
                                </h5>
                                
                                <div class="kb-article-meta">
                                    <a href="{{ route('knowledge.category', $article->category->slug) }}" class="badge bg-light text-dark">
                                        {{ $article->category->name }}
                                    </a>
                                    <span class="text-muted small">
                                        <i class="mdi mdi-calendar-outline"></i> {{ $article->published_at->format('M d, Y') }}
                                    </span>
                                    <span class="text-muted small">
                                        <i class="mdi mdi-eye-outline"></i> {{ $article->view_count }} @lang('translation.views')
                                    </span>
                                </div>
                                
                                @php
                                    $excerpt = $article->excerpt 
                                        ? $article->excerpt 
                                        : Str::limit(strip_tags($article->content), 180);
                                    
                                    // Highlight search term
                                    $highlightedExcerpt = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="highlight">$1</span>', $excerpt);
                                @endphp
                                
                                <div class="kb-article-excerpt">
                                    {!! $highlightedExcerpt !!}
                                </div>
                                
                                <div class="kb-article-footer">
                                    <div class="kb-article-author">
                                        @if($article->author)
                                            <img src="{{ $article->author->getGravatarUrl(24) }}" alt="{{ $article->author->name }}">
                                            <div>
                                                <span class="fw-medium">{{ $article->author->name }}</span>
                                                @if($article->author->isAdmin())
                                                    <span class="badge bg-soft-danger text-danger ms-1 fw-normal">@lang('translation.Admin')</span>
                                                @elseif($article->author->isSupport())
                                                    <span class="badge bg-soft-success text-success ms-1 fw-normal">@lang('translation.Support')</span>
                                                @endif
                                            </div>
                                        @else
                                            <span>@lang('translation.Unknown_Author')</span>
                                        @endif
                                    </div>
                                    <div class="kb-article-actions">
                                        <a href="{{ route('knowledge.article', ['category' => $article->category->slug, 'article' => $article->slug]) }}" class="btn btn-primary btn-sm">
                                            @lang('translation.Read_More')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $articles->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-file-search-outline" style="font-size: 48px; opacity: 0.5;"></i>
                            <h5 class="mt-3">@lang('translation.No_results_found')</h5>
                            <p class="text-muted">
                                @lang('translation.No_match_for_query', ['query' => $query])
                            </p>
                            <a href="{{ route('knowledge.index') }}" class="btn btn-primary mt-3">
                                <i class="mdi mdi-home-outline me-1"></i> @lang('translation.Back_to_Knowledge_Base')
                            </a>
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
    const searchInput = document.getElementById('live-search-input');
    const searchButton = document.getElementById('search-btn');
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
    
    // Tìm kiếm khi nhập
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