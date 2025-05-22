@extends('layouts.master')

@section('title') User Details @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Dashboard @endslot
        @slot('li_2') User Management @endslot
        @slot('title') User Details @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src="{{ $user->getGravatarUrl(150) }}" alt="{{ $user->name }}" 
                             class="rounded-circle avatar-xl mb-3">
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                        
                        <div class="mb-3">
                            @if($user->role === 'admin')
                                <span class="badge rounded-pill bg-primary px-3 py-2">Admin</span>
                            @elseif($user->role === 'support')
                                <span class="badge rounded-pill bg-success px-3 py-2">Support Staff</span>
                            @else
                                <span class="badge rounded-pill bg-info px-3 py-2">User</span>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                                <i data-feather="edit" class="icon-sm me-1"></i> Edit User
                            </a>
                            
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i data-feather="trash-2" class="icon-sm me-1"></i> Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">User Information</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">ID:</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Full Name:</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Email:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Role:</th>
                                    <td>
                                        @if($user->role === 'admin')
                                            Admin
                                        @elseif($user->role === 'support')
                                            Support Staff
                                        @else
                                            User
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Email Verified:</th>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Verified at {{ $user->email_verified_at->format('m/d/Y H:i') }}</span>
                                        @else
                                            <span class="badge bg-warning">Not verified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Created At:</th>
                                    <td>{{ $user->created_at->format('m/d/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Last Updated:</th>
                                    <td>{{ $user->updated_at->format('m/d/Y H:i') }}</td>
                                </tr>
                                @if($user->social_type)
                                <tr>
                                    <th scope="row">Social Login:</th>
                                    <td>{{ ucfirst($user->social_type) }}</td>
                                </tr>
                                @endif
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
        // Initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection