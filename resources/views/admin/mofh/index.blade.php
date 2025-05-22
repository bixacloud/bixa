@extends('layouts.master')

@section('title') MOFH Settings @endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Settings @endslot
        @slot('title') MOFH Settings @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.mofh.settings.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">API Username</label>
                                    <input type="text" name="api_username" class="form-control" value="{{ old('api_username', $settings->api_username) }}" 
                                        placeholder="Enter API username">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">API Password</label>
                                    <input type="text" name="api_password" class="form-control" value="{{ old('api_password', $settings->api_password) }}"
                                        placeholder="Enter API password">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">cPanel URL</label>
                                    <input type="text" name="cpanel_url" class="form-control" value="{{ old('cpanel_url', $settings->cpanel_url) }}"
                                        placeholder="Enter cPanel URL">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Plan/Package</label>
                                    <input type="text" name="plan" class="form-control" value="{{ old('plan', $settings->plan) }}"
                                        placeholder="Enter hosting plan name">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Server IP</label>
                                    <input type="text" class="form-control" value="{{ $server_ip }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Callback URL</label>
                                    <input type="text" class="form-control" value="{{ $callback_url }}" readonly>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-1">Update Settings</button>
                                <a href="{{ route('admin.mofh.settings.test') }}" class="btn btn-success waves-effect waves-light">
                                    <i class="bx bx-check-shield font-size-16 align-middle me-1"></i> Test Connection
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

