@extends('layouts.master')

@section('title') Ad Slots @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') Home @endslot
@slot('title') Ad Slots @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Ad Placement Areas</h4>
                    <a href="{{ route('admin.ad-slots.create') }}" class="btn btn-primary waves-effect waves-light">
                        <i data-feather="plus" class="icon-sm me-1"></i> Create New Ad Slot
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

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Page</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Ads</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($slots as $slot)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.ad-slots.edit', $slot) }}" class="text-body fw-medium">
                                        {{ $slot->name }}
                                    </a>
                                </td>
                                <td><code>{{ $slot->code }}</code></td>
                                <td>{{ $slot->page }}</td>
                                <td>
                                    @if($slot->type === 'predefined')
                                        <span class="badge bg-primary">Predefined</span>
                                    @else
                                        <span class="badge bg-info">Dynamic</span>
                                    @endif
                                </td>
                                <td>
                                    @if($slot->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $slot->advertisements_count }}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.ad-slots.edit', $slot) }}" class="btn btn-sm btn-info waves-effect waves-light">
                                            <i data-feather="edit-2" class="icon-sm"></i>
                                        </a>
                                        
                                        @if($slot->advertisements_count == 0)
                                            <form action="{{ route('admin.ad-slots.destroy', $slot) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ad slot?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger waves-effect waves-light">
                                                    <i data-feather="trash-2" class="icon-sm"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i data-feather="inbox" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                                        <h5 class="text-muted font-weight-normal">No ad slots found</h5>
                                        <p class="text-muted mb-2">Create your first ad slot to get started</p>
                                        <a href="{{ route('admin.ad-slots.create') }}" class="btn btn-primary waves-effect waves-light">
                                            <i data-feather="plus" class="icon-sm me-1"></i> Create Ad Slot
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

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">About Ad Placement Areas</h4>
                
                <div class="alert alert-info" role="alert">
                    <div class="d-flex">
                        <i data-feather="info" class="me-2"></i>
                        <div>
                            <h5 class="alert-heading">How Ad Slots Work</h5>
                            <p class="mb-0">
                                Ad slots are predefined areas in your application where advertisements can be displayed. 
                                There are two types of ad slots:
                            </p>
                            <ul class="mt-2 mb-0">
                                <li><strong>Predefined</strong>: Fixed positions in the application layout, defined by developers.</li>
                                <li><strong>Dynamic</strong>: Positions created on-the-fly based on CSS selectors, more flexible but requires care.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

