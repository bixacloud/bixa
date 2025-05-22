@extends('layouts.master')

@section('title') Knowledge Base Articles @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') Knowledge Base @endslot
        @slot('title') Articles @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Knowledge Base Articles</h4>
                        <a href="{{ route('admin.knowledge.articles.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Add New Article
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form action="{{ route('admin.knowledge.articles.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search articles..." name="search" value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit"><i class="bx bx-search"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('admin.knowledge.articles.index') }}" method="GET">
                                <div class="input-group">
                                    <select class="form-select" name="category_id" onchange="this.form.submit()">
                                        <option value="all" {{ request('category_id') == 'all' ? 'selected' : '' }}>All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Views</th>
                                    <th>Published At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                    <tr>
                                        <td>{{ $article->id }}</td>
                                        <td>
                                            <div class="d-flex">
                                                @if($article->is_featured)
                                                    <span class="badge bg-warning me-1" title="Featured"><i class="bx bx-star"></i></span>
                                                @endif
                                                {{ Str::limit($article->title, 40) }}
                                            </div>
                                        </td>
                                        <td>{{ $article->category->name }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="{{ $article->author->isAdmin() ? 'text-danger' : ($article->author->isSupport() ? 'text-success' : '') }}">
                                                    {{ $article->author->name }}
                                                </span>
                                                @if($article->author->isAdmin())
                                                    <span class="badge bg-danger ms-1">Admin</span>
                                                @elseif($article->author->isSupport())
                                                    <span class="badge bg-success ms-1">Support</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($article->is_published)
                                                <span class="badge bg-success">Published</span>
                                            @else
                                                <span class="badge bg-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td>{{ $article->view_count }}</td>
                                        <td>{{ $article->published_at ? $article->published_at->format('Y-m-d') : 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('knowledge.article', ['category' => $article->category->slug, 'article' => $article->slug]) }}" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="{{ route('admin.knowledge.articles.edit', $article) }}" class="btn btn-sm btn-primary">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.knowledge.articles.destroy', $article) }}" method="POST" class="d-inline" id="delete-form">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No articles found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $articles->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this article? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
