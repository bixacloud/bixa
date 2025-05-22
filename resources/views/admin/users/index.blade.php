@extends('layouts.master')

@section('title') User Management @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Dashboard @endslot
        @slot('title') User Management @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">All Users</h4>
                        
                        <div class="d-flex gap-2">
                            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
                                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>
                                        Admin ({{ $roleCounts['admin'] ?? 0 }})
                                    </option>
                                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>
                                        User ({{ $roleCounts['user'] ?? 0 }})
                                    </option>
                                </select>
                            </form>

                            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" name="search" 
                                           placeholder="Search name, email..." value="{{ request('search') }}">
                                    <button class="btn btn-sm btn-primary" type="submit">
                                        <i data-feather="search" class="icon-sm"></i>
                                    </button>
                                </div>
                            </form>

                            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-success">
                                <i data-feather="plus" class="icon-sm"></i> Add User
                            </a>
                        </div>
                    </div>

                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
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
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <img src="{{ $user->getGravatarUrl(40) }}" alt="{{ $user->name }}" 
                                             class="rounded-circle" width="40" height="40">
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-primary">Admin</span>
                                        @elseif($user->role === 'support')
                                            <span class="badge bg-success">Support Staff</span>
                                        @else
                                            <span class="badge bg-info">User</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                                <i data-feather="more-horizontal" class="icon-sm"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">
                                                    <i data-feather="eye" class="icon-sm me-2"></i> View Details
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                                    <i data-feather="edit" class="icon-sm me-2"></i> Edit
                                                </a>
                                                @if(auth()->id() !== $user->id)
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i data-feather="trash-2" class="icon-sm me-2"></i> Delete
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No users found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <nav>
                            <ul class="pagination justify-content-center">
                                @for ($i = 1; $i <= $users->lastPage(); $i++)
                                    <li class="page-item {{ ($users->currentPage() == $i) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
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