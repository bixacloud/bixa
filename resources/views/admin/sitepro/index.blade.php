@extends('layouts.master')

@section('title') Site.pro Settings @endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Site.pro Builder Settings</h4>
                </div>
                <div class="card-body">

                    @if(session('success'))
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

                    <form action="{{ route('admin.sitepro.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Hostname</label>
                                    <input type="text" name="hostname" class="form-control" 
                                           value="{{ old('hostname', $settings->hostname) }}"
                                           placeholder="https://site.pro">
                                    <small class="text-muted">Full URL to Site.pro builder installation</small>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">API Username</label>
                                    <input type="text" name="username" class="form-control"
                                           value="{{ old('username', $settings->username) }}"
                                           placeholder="API username">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">API Password</label>
                                    <input type="password" name="password" class="form-control"
                                           value="{{ old('password', $settings->password) }}"
                                           placeholder="API password">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="1" {{ $settings->status ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !$settings->status ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection