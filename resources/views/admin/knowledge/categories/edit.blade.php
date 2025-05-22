@extends('layouts.master')

@section('title') Edit Knowledge Category @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('li_2') Knowledge Base @endslot
        @slot('li_3') 
            <a href="{{ route('admin.knowledge.categories.index') }}">Categories</a>
        @endslot
        @slot('title') Edit Category @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Knowledge Category</h4>
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.knowledge.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category->slug) }}">
                            <small class="text-muted">Leave empty to generate automatically from name</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
                            <small class="text-muted">Categories with lower values will appear first</small>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Category</button>
                            <a href="{{ route('admin.knowledge.categories.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        // Track if user has manually changed the slug
        let slugManuallyChanged = false;
        
        // Check if slug already exists and matches the pattern from the name
        // This helps determine if the slug was manually set or auto-generated
        const currentName = nameInput.value;
        const currentSlug = slugInput.value;
        const generatedSlug = currentName
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
            
        // If current slug doesn't match what would be generated from name, consider it manually changed
        if (currentSlug && currentSlug !== generatedSlug) {
            slugManuallyChanged = true;
        }
        
        // When user types in the name field
        nameInput.addEventListener('input', function() {
            // Only auto-update slug if user hasn't manually changed it
            if (!slugManuallyChanged) {
                // Convert to lowercase, replace spaces with hyphens, remove special characters
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            }
        });
        
        // Track when user manually changes the slug
        slugInput.addEventListener('input', function() {
            // If user is typing in the slug field, mark as manually changed
            if (document.activeElement === slugInput) {
                slugManuallyChanged = true;
            }
        });
        
        // Add a button to regenerate slug from name if needed
        const slugContainer = slugInput.parentElement;
        const regenerateButton = document.createElement('button');
        regenerateButton.type = 'button';
        regenerateButton.className = 'btn btn-sm btn-outline-secondary mt-2';
        regenerateButton.innerHTML = '<i class="bx bx-refresh me-1"></i> Regenerate slug from name';
        regenerateButton.addEventListener('click', function() {
            slugInput.value = nameInput.value
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
            
            // Reset the tracking state after manual regeneration
            slugManuallyChanged = false;
        });
        slugContainer.appendChild(regenerateButton);
    });
</script>
@endsection