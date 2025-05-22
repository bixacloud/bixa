@extends('layouts.master')

@section('title') Support Tickets @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Admin @endslot
        @slot('title') Support Tickets @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">All Tickets</h4>
                        <div>
                            <a href="{{ route('admin.tickets.categories.index') }}" class="btn btn-sm btn-info">
                                <i data-feather="list" class="icon-sm me-1"></i> Categories
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>
                                            {{ $ticket->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $ticket->user->email }}</small>
                                        </td>
                                        <td>{{ Str::limit($ticket->title, 30) }}</td>
                                        <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($ticket->status === 'open')
                                                <span class="badge bg-success">Open</span>
                                            @elseif($ticket->status === 'answered')
                                                <span class="badge bg-info">Answered</span>
                                            @elseif($ticket->status === 'customer-reply')
                                                <span class="badge bg-warning">Customer Reply</span>
                                            @elseif($ticket->status === 'pending')
                                                <span class="badge bg-secondary">Pending</span>
                                            @else
                                                <span class="badge bg-dark">Closed</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $ticket->updated_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
                                                <i data-feather="eye" class="icon-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No tickets found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <nav>
                            <ul class="pagination justify-content-center">
                                @for ($i = 1; $i <= $tickets->lastPage(); $i++)
                                    <li class="page-item {{ ($tickets->currentPage() == $i) ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $tickets->url($i) }}">{{ $i }}</a>
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