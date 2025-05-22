@extends('layouts.master')

@section('title') Email Templates @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Settings @endslot
        @slot('title') Email Templates @endslot
    @endcomponent

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject</th>
                            <th>Used For</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $template['subject'] }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $template['used_for'] }}</span>
                                    <div class="small text-muted">{{ $template['mailable'] }}</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.email.edit', $template['id']) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i data-feather="edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No templates found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

