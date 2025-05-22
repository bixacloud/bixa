@extends('layouts.master')

@section('title') Manage Hosting Accounts @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Dashboard @endslot
        @slot('title') Manage Hosting Accounts @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">All Hosting Accounts</h4>
                        
                        <div class="d-flex gap-2">
                            <form method="GET" action="{{ route('admin.hosting.index') }}" class="d-flex gap-2">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                        Active ({{ $statusCounts['active'] ?? 0 }})
                                    </option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        Pending ({{ $statusCounts['pending'] ?? 0 }})
                                    </option>
                                    <option value="deactivated" {{ request('status') == 'deactivated' ? 'selected' : '' }}>
                                        Deactivated ({{ $statusCounts['deactivated'] ?? 0 }})
                                    </option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>
                                        Suspended ({{ $statusCounts['suspended'] ?? 0 }})
                                    </option>
                                </select>
                            </form>

                            <form method="GET" action="{{ route('admin.hosting.index') }}" class="d-flex">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" name="search" 
                                           placeholder="Search username, domain, email..." value="{{ request('search') }}">
                                    <button class="btn btn-sm btn-primary" type="submit">
                                        <i data-feather="search" class="icon-sm"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Label</th>
                                    <th>Domain</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($accounts as $account)
                                <tr>
                                    <td>{{ $account->id }}</td>
                                    <td>{{ $account->username }}</td>
                                    <td>{{ $account->label }}</td>
                                    <td>{{ $account->domain }}</td>
                                    <td>{{ $account->user->email }}</td>
                                    <td>
                                        @if(in_array($account->status, ['pending', 'deactivating', 'reactivating']))
                                            <span class="badge bg-warning">
                                                <i data-feather="loader" class="font-size-14 align-middle me-1"></i>
                                                {{ ucfirst($account->status) }}
                                            </span>
                                        @elseif($account->status === 'active')
                                            <span class="badge bg-success">
                                                <i data-feather="check-circle" class="font-size-14 align-middle me-1"></i>
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i data-feather="x-circle" class="font-size-14 align-middle me-1"></i>
                                                {{ ucfirst($account->status) }}
                                            </span>
                                        @endif

                                        @if($account->admin_deactivated)
                                            <span class="badge bg-dark">
                                                <i data-feather="shield" class="font-size-14 align-middle me-1"></i>
                                                Admin
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $account->created_at->format('Y-m-d') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.hosting.view', $account->username) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i data-feather="eye" class="font-size-14 align-middle me-1"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No accounts found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <nav>
                            <ul class="pagination justify-content-center">
                                @for ($i = 1; $i <= $accounts->lastPage(); $i++)
                                    <li class="page-item {{ ($accounts->currentPage() == $i) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $accounts->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection