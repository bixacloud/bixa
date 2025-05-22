@extends('layouts.master')

@section('title') @lang('translation.Knowledge_Base') @endsection

@section('css')
<!-- Boxicons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
    /* Knowledge Base specific styles */
    .kb-search-input {
        background-color: var(--bs-body-bg) !important;
        border: 1px solid var(--bs-border-color);
    }
    
    /* Search box fix */
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
    
    /* Section styles */
    .kb-section {
        margin-bottom: 40px;
    }
    
    .kb-section-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--bs-border-color);
    }
    
    .kb-article-list {
        list-style-type: none;
        padding-left: 0;
    }
    
    .kb-article-list li {
        margin-bottom: 15px;
    }
    
    .kb-article-icon {
        margin-right: 10px;
        color: var(--bs-primary);
    }
    
    .kb-article-link {
        color: var(--bs-body-color);
        text-decoration: none;
    }
    
    .kb-article-link:hover {
        color: var(--bs-primary);
        text-decoration: none;
    }
    
    .kb-view-all {
        display: inline-flex;
        align-items: center;
        margin-top: 10px;
        color: var(--bs-primary);
        font-weight: 500;
        text-decoration: none;
    }
    
    .kb-view-all:hover {
        text-decoration: underline;
    }
    
    .kb-view-all i {
        margin-left: 5px;
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
        @slot('li_1') @lang('translation.Pages') @endslot
        @slot('title') @lang('translation.Knowledge_Base') @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Knowledge_Base')</h4>
                    
                    <!-- Search Box (Fixed) -->
                    <div class="row mb-4">
                        <div class="col-lg-8 mx-auto">
                            <div class="kb-search-wrapper">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-magnify"></i>
                                    </span>
                                    <input type="text" id="live-search-input" class="form-control kb-search-input" placeholder="@lang('translation.Search_for_articles')">
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
                    </div>
                    
                    <!-- Categories and Articles in Grid Layout -->
                    <div class="row">
                        @php
                            // Group categories into rows
                            $groupedCategories = $categories->chunk(3);
                        @endphp
                        
                        @foreach($groupedCategories as $categoryRow)
                            <div class="row">
                                @foreach($categoryRow as $category)
                                    <div class="col-lg-4">
                                        <div class="kb-section">
                                            <h5 class="kb-section-title">
                                                <a href="{{ route('knowledge.category', $category->slug) }}" class="text-dark">
                                                    {{ $category->name }}
                                                </a>
                                            </h5>
                                            
                                            @php
                                                $categoryArticles = \App\Models\KnowledgeArticle::where('category_id', $category->id)
                                                    ->where('is_published', true)
                                                    ->orderBy('published_at', 'desc')
                                                    ->take(5)
                                                    ->get();
                                            @endphp
                                            
                                            <ul class="kb-article-list">
                                                @foreach($categoryArticles as $article)
                                                    <li>
                                                        <i class="mdi mdi-file-document-outline kb-article-icon"></i>
                                                        <a href="{{ route('knowledge.article', ['category' => $category->slug, 'article' => $article->slug]) }}" class="kb-article-link">
                                                            {{ $article->title }}
                                                        </a>
                                                        @if($article->is_featured)
                                                            <span class="kb-badge kb-featured-badge ms-2">@lang('translation.Featured')</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            @if($category->active_articles_count > 5)
                                                <a href="{{ route('knowledge.category', $category->slug) }}" class="kb-view-all">
                                                    @lang('translation.See_all_articles', ['count' => $category->active_articles_count])
                                                    <i class="mdi mdi-arrow-right"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Need help section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>@lang('translation.Still_need_help')</h5>
                                    <p class="text-muted mb-0">@lang('translation.Cannot_find_answer')</p>
                                    <div class="text-end mt-3">
                                        <a href="{{ route('user.tickets.create') ?? '#' }}" class="btn btn-primary">@lang('translation.Contact_Support')</a>
                                    </div>
                                </div>
                            </div>
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
    
    // Đóng kết quả tìm kiếm khi click bên ngoài
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