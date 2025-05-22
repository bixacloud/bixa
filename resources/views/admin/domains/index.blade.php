@extends('layouts.master')

@section('title') Domain Extensions @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Settings @endslot
        @slot('title') Domain Extensions @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            {{-- Add Domain Form --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Add Extension</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.domains.store') }}" method="POST">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col">
                                <input type="text" name="domain" 
                                    class="form-control @error('domain') is-invalid @enderror"
                                    placeholder="Enter domain extension (e.g. .example.com)"
                                    value="{{ old('domain') }}">
                                @error('domain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Domains List --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Total Extensions</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Domain</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($domains as $index => $domain)
                                    <tr id="domain-row-{{ $index }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $domain->domain_name }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.domains.destroy', $domain->domain_name) }}" 
                                                method="POST" 
                                                style="display: inline-block;"
                                                onsubmit="return confirm('Are you sure you want to delete this domain?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bx bx-trash font-size-16 align-middle"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No domain extensions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="text-muted">{{ count($domains) }} Domains</span>
                </div>
            </div>
        </div>
    </div>
@endsection