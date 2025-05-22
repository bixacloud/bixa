@extends('layouts.master')

@section('title') SMTP Settings @endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">SMTP Settings</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i data-feather="check-circle" class="me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i data-feather="alert-circle" class="me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.settings.smtp.update') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="server" class="icon-xs me-1"></i>
                                    Service Type
                                </label>
                                <select class="form-control" name="type">
                                    <option value="SMTP" selected>SMTP</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="globe" class="icon-xs me-1"></i>
                                    Hostname
                                </label>
                                <input type="text" name="hostname" class="form-control" value="{{ old('hostname', $smtp->hostname) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="user" class="icon-xs me-1"></i>
                                    Username
                                </label>
                                <input type="text" name="username" class="form-control" value="{{ old('username', $smtp->username) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="key" class="icon-xs me-1"></i>
                                    Password
                                </label>
                                <input type="password" name="password" class="form-control" value="{{ old('password', $smtp->password) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="mail" class="icon-xs me-1"></i>
                                    From Email
                                </label>
                                <input type="email" name="from_email" class="form-control" value="{{ old('from_email', $smtp->from_email) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="tag" class="icon-xs me-1"></i>
                                    From Name
                                </label>
                                <input type="text" name="from_name" class="form-control" value="{{ old('from_name', $smtp->from_name) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="hash" class="icon-xs me-1"></i>
                                    SMTP Port
                                </label>
                                <input type="number" name="port" class="form-control" value="{{ old('port', $smtp->port) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="lock" class="icon-xs me-1"></i>
                                    SMTP Encryption
                                </label>
                                <select class="form-control" name="encryption">
                                    <option value="ssl" @selected(old('encryption', $smtp->encryption) === 'ssl')>SSL</option>
                                    <option value="tls" @selected(old('encryption', $smtp->encryption) === 'tls')>TLS</option>
                                    <option value="none" @selected(old('encryption', $smtp->encryption) === 'none')>None</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i data-feather="toggle-right" class="icon-xs me-1"></i>
                                    SMTP Status
                                </label>
                                <select class="form-control" name="status">
                                    <option value="1" @selected(old('status', $smtp->status) == true)>Active</option>
                                    <option value="0" @selected(old('status', $smtp->status) == false)>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i data-feather="save" class="me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.settings.smtp.test') }}" class="btn btn-success waves-effect waves-light ms-2">
                            <i data-feather="send" class="me-1"></i> Test Connection
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

