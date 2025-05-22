@extends('layouts.master')

@section('title') ACME Settings @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ACME SSL Settings</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.acme.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Let's Encrypt Settings -->
                        <div class="mb-4">
                            <h5>Let's Encrypt</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Directory URL</label>
                                        <input type="text" name="letsencrypt_url" 
                                               class="form-control"
                                               value="{{ $settings->acme_letsencrypt }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ZeroSSL Settings -->
                        <div class="mb-4">
                            <h5>ZeroSSL</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Directory URL</label>
                                        <input type="text" name="zerossl_url" 
                                               class="form-control"
                                               value="{{ $settings->acme_zerossl['url'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">EAB Key ID</label>
                                        <input type="text" name="zerossl_kid" 
                                               class="form-control"
                                               value="{{ $settings->acme_zerossl['eab_kid'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">EAB HMAC Key</label>
                                        <input type="text" name="zerossl_hmac" 
                                               class="form-control"
                                               value="{{ $settings->acme_zerossl['eab_hmac_key'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Google Trust Settings -->
                        <div class="mb-4">
                            <h5>Google Trust Services</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Directory URL</label>
                                        <input type="text" name="googletrust_url" 
                                               class="form-control"
                                               value="{{ $settings->acme_googletrust['url'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">EAB Key ID</label>
                                        <input type="text" name="googletrust_kid" 
                                               class="form-control"
                                               value="{{ $settings->acme_googletrust['eab_kid'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">EAB HMAC Key</label>
                                        <input type="text" name="googletrust_hmac" 
                                               class="form-control"
                                               value="{{ $settings->acme_googletrust['eab_hmac_key'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DNS Settings -->
                        <div class="mb-4">
                            <h5>DNS Settings</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">DNS Resolver</label>
                                        <input type="text" name="dns_resolver" 
                                               class="form-control"
                                               value="{{ $settings->acme_dns['resolver'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">DNS over HTTPS</label>
                                        <select name="dns_doh" class="form-select">
                                            <option value="active" 
                                                {{ ($settings->acme_dns['doh'] ?? '') === 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive"
                                                {{ ($settings->acme_dns['doh'] ?? '') === 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <h5>General Settings</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" 
                                                {{ $settings->acme_status === 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive"
                                                {{ $settings->acme_status === 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection