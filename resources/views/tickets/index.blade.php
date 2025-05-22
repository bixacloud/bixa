@extends('layouts.master')

@section('title') @lang('translation.My_Support_Tickets') @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') @lang('translation.Home') @endslot
        @slot('title') @lang('translation.Support_Tickets') @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">@lang('translation.My_Support_Tickets')</h4>
                        <a href="{{ route('user.tickets.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="icon-sm me-1"></i> @lang('translation.Open_New_Ticket')
                        </a>
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
                                    <th>@lang('translation.ID')</th>
                                    <th>@lang('translation.Title')</th>
                                    <th>@lang('translation.Category')</th>
                                    <th>@lang('translation.Priority')</th>
                                    <th>@lang('translation.Status')</th>
                                    <th>@lang('translation.Created')</th>
                                    <th>@lang('translation.Last_Updated')</th>
                                    <th>@lang('translation.Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>{{ Str::limit($ticket->title, 30) }}</td>
                                        <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($ticket->status === 'open')
                                                <span class="badge bg-success">@lang('translation.Open')</span>
                                            @elseif($ticket->status === 'answered')
                                                <span class="badge bg-info">@lang('translation.Answered_by_Staff')</span>
                                            @elseif($ticket->status === 'customer-reply')
                                                <span class="badge bg-warning">@lang('translation.Your_Reply_Sent')</span>
                                            @elseif($ticket->status === 'pending')
                                                <span class="badge bg-secondary">@lang('translation.Pending')</span>
                                            @else
                                                <span class="badge bg-dark">@lang('translation.Closed')</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $ticket->updated_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('user.tickets.show', $ticket) }}" class="btn btn-sm btn-primary">
                                                <i data-feather="eye" class="icon-sm"></i> @lang('translation.View')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">@lang('translation.No_tickets_found')</td>
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

@section('script')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection