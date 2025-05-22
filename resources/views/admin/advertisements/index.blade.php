@extends('layouts.master')

@section('title') Advertisements @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Home @endslot
@slot('title') Advertisements @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Advertisements</h4>
                    <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary waves-effect waves-light">
                        <i data-feather="plus" class="icon-sm me-1"></i> Create New Ad
                    </a>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                        <label class="form-check-label" for="selectAll">&nbsp;</label>
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Impressions</th>
                                <th>Clicks</th>
                                <th>CTR</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($advertisements as $ad)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="ad-{{ $ad->id }}">
                                        <label class="form-check-label" for="ad-{{ $ad->id }}">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.advertisements.edit', $ad) }}" class="text-body fw-medium">
                                        {{ $ad->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($ad->slot)
                                        <span class="badge bg-primary">{{ $ad->slot->name }}</span>
                                    @else
                                        <span class="badge bg-warning">Unknown Slot</span>
                                    @endif
                                </td>
                                <td>{{ number_format($ad->impressions) }}</td>
                                <td>{{ number_format($ad->clicks) }}</td>
                                <td>
                                    @if($ad->impressions > 0)
                                        {{ number_format(($ad->clicks / $ad->impressions) * 100, 2) }}%
                                    @else
                                        0.00%
                                    @endif
                                </td>
                                <td>
                                    @if($ad->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.advertisements.edit', $ad) }}" class="btn btn-sm btn-info waves-effect waves-light">
                                            <i data-feather="edit-2" class="icon-sm"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this advertisement?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger waves-effect waves-light">
                                                <i data-feather="trash-2" class="icon-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i data-feather="inbox" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                                        <h5 class="text-muted font-weight-normal">No advertisements found</h5>
                                        <p class="text-muted mb-2">Create your first advertisement to get started</p>
                                        <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary waves-effect waves-light">
                                            <i data-feather="plus" class="icon-sm me-1"></i> Create Advertisement
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all functionality
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endsection