@extends('layouts.master')

@section('title') About Bixa @endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') About @endslot
@slot('title') About Bixa @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Introduction Section -->
                <div class="text-center mb-5">
                    <i data-feather="box" class="text-primary" style="width: 50px; height: 50px;"></i>
                    <h3 class="mt-4">Welcome to Bixa</h3>
                    <p class="text-muted">A Modern Hosting Management System</p>
                </div>

                <!-- Version Info -->
                <div class="row mb-5">
                    <div class="col-lg-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h4 class="mb-3">Version</h4>
                                <p class="mb-0">{{ $systemInfo['version'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h4 class="mb-3">Release Date</h4>
                                <p class="mb-0">{{ date('F Y') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h4 class="mb-3">License</h4>
                                <p class="mb-0">Commercial License</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Features -->
                <div class="mb-5">
                    <h4 class="mb-4">Key Features</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <i data-feather="users" class="text-primary me-3"></i>
                                <div>
                                    <h5>User Management</h5>
                                    <p class="text-muted mb-0">Complete user administration system</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <i data-feather="server" class="text-success me-3"></i>
                                <div>
                                    <h5>Hosting Control</h5>
                                    <p class="text-muted mb-0">Efficient hosting account management</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <i data-feather="shield" class="text-info me-3"></i>
                                <div>
                                    <h5>SSL Integration</h5>
                                    <p class="text-muted mb-0">Automated SSL certificate management</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <i data-feather="life-buoy" class="text-warning me-3"></i>
                                <div>
                                    <h5>Support System</h5>
                                    <p class="text-muted mb-0">Integrated ticket support system</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credits Section -->
                <div class="mb-5">
                    <h4 class="mb-4">Credits</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Version</th>
                                    <th>License</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Laravel</td>
                                    <td>{{ $systemInfo['laravel_version'] }}</td>
                                    <td>MIT</td>
                                </tr>
                                <tr>
                                    <td>Bootstrap</td>
                                    <td>5.3.3</td>
                                    <td>MIT</td>
                                </tr>
                                <tr>
                                    <td>Feather Icons</td>
                                    <td>3.1.3</td>
                                    <td>MIT</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h4 class="mb-4">Contact Us</h4>
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <i data-feather="mail" class="me-3"></i>
                            <div>
                                <h5 class="mb-1">Support Email</h5>
                                <p class="mb-0">hi@bixa.app</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

