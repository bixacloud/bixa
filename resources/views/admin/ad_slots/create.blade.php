@extends('layouts.master')

@section('title') Create Ad Slot @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Home @endslot
@slot('li_2') Ad Slots @endslot
@slot('title') Create Ad Slot @endslot
@endcomponent

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Create New Ad Slot</h4>
                    <a href="{{ route('admin.ad-slots.index') }}" class="btn btn-secondary waves-effect">
                        <i data-feather="arrow-left" class="icon-sm me-1"></i> Back to List
                    </a>
                </div>

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.ad-slots.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Name -->
                    <div class="row mb-3">
                        <label for="name" class="col-sm-3 col-form-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            <small class="form-text text-muted">Example: "Header Banner", "Sidebar Top Ad"</small>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Code -->
                    <div class="row mb-3">
                        <label for="code" class="col-sm-3 col-form-label">Code</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                            <small class="form-text text-muted">Example: "header_banner", "sidebar_top". Use only letters, numbers, and underscores.</small>
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Display Page -->
                    <div class="row mb-3">
                        <label for="page" class="col-sm-3 col-form-label">Display Page</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control @error('page') is-invalid @enderror" id="page" name="page" value="{{ old('page') }}" required>
                            <small class="form-text text-muted">Example: "home", "product_detail", "all"</small>
                            @error('page')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Slot Type -->
                    <div class="row mb-3">
                        <label for="type" class="col-sm-3 col-form-label">Slot Type</label>
                        <div class="col-sm-9">
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="predefined" {{ old('type') == 'predefined' ? 'selected' : '' }}>Predefined</option>
                                <option value="dynamic" {{ old('type') == 'dynamic' ? 'selected' : '' }}>Dynamic</option>
                            </select>
                            <small class="form-text text-muted">
                                <strong>Predefined:</strong> Position already defined in code.<br>
                                <strong>Dynamic:</strong> Position created automatically based on CSS selector.
                            </small>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Dynamic Options -->
                    <div id="dynamic-options" style="display: none;">
                        <div class="card mb-3 bg-light">
                            <div class="card-header">
                                Dynamic Position Configuration
                            </div>
                            <div class="card-body">
                                <!-- CSS Selector -->
                                <div class="row mb-3">
                                    <label for="selector" class="col-sm-3 col-form-label">CSS Selector</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control @error('selector') is-invalid @enderror" id="selector" name="selector" value="{{ old('selector') }}">
                                        <small class="form-text text-muted">
                                            Example: "#product-container", ".sidebar", "header .navigation".<br>
                                            This selector will be used to find where to insert the ad.
                                        </small>
                                        @error('selector')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Insert Position -->
                                <div class="row mb-3">
                                    <label for="position" class="col-sm-3 col-form-label">Insert Position</label>
                                    <div class="col-sm-9">
                                        <select class="form-select @error('position') is-invalid @enderror" id="position" name="position">
                                            <option value="before" {{ old('position') == 'before' ? 'selected' : '' }}>Before element</option>
                                            <option value="after" {{ old('position') == 'after' ? 'selected' : '' }}>After element</option>
                                            <option value="prepend" {{ old('position') == 'prepend' ? 'selected' : '' }}>Inside (Beginning)</option>
                                            <option value="append" {{ old('position') == 'append' ? 'selected' : '' }}>Inside (End)</option>
                                        </select>
                                        <small class="form-text text-muted">Where to insert the ad relative to the selected element.</small>
                                        @error('position')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="row mb-3">
                        <label for="description" class="col-sm-3 col-form-label">Description</label>
                        <div class="col-sm-9">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">Detailed description of this position for other admins to understand.</small>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Position Image -->
                    <div class="row mb-3">
                        <label for="image" class="col-sm-3 col-form-label">Position Image</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                            <small class="form-text text-muted">Upload an image showing where this ad will appear.</small>
                            @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Status</label>
                        <div class="col-sm-9">
                            <div class="form-check form-switch form-switch-md">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <small class="form-text text-muted">Only active slots will display advertisements.</small>
                        </div>
                    </div>
                    
                    <div class="row justify-content-end">
                        <div class="col-sm-9">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i data-feather="save" class="icon-sm me-1"></i> Create Ad Slot
                                </button>
                                <a href="{{ route('admin.ad-slots.index') }}" class="btn btn-secondary waves-effect">
                                    Cancel
                                </a>
                            </div>
                        </div>
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
        const typeSelect = document.getElementById('type');
        const dynamicOptions = document.getElementById('dynamic-options');
        
        function toggleDynamicOptions() {
            if (typeSelect.value === 'dynamic') {
                dynamicOptions.style.display = 'block';
                document.getElementById('selector').setAttribute('required', 'required');
                document.getElementById('position').setAttribute('required', 'required');
            } else {
                dynamicOptions.style.display = 'none';
                document.getElementById('selector').removeAttribute('required');
                document.getElementById('position').removeAttribute('required');
            }
        }
        
        toggleDynamicOptions();
        typeSelect.addEventListener('change', toggleDynamicOptions);
    });
</script>
@endsection