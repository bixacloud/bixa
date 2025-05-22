@extends('layouts.master')

@section('title') License Agreement @endsection

@section('css')
<style>
    .license-section {
        margin-bottom: 2rem;
    }
    .license-section h5 {
        color: #5156be;
        margin-bottom: 1rem;
    }
    .license-content {
        background: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1.5rem;
    }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1') License @endslot
@slot('title') License Agreement @endslot
@endcomponent

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Header -->
                <div class="text-center mb-5">
                    <i data-feather="file" class="text-primary" style="width: 50px; height: 50px;"></i>
                    <h3 class="mt-4">License Agreement</h3>
                    <p class="text-muted">Effective Date: {{ date('F d, Y') }}</p>
                </div>

                <!-- Definitions -->
                <div class="license-section">
                    <h5><i data-feather="book" class="icon-sm me-2"></i>1. Definitions</h5>
                    <div class="license-content">
                        <ul class="mb-0">
                            <li class="mb-2">"Software" refers to Bixa Hosting Management System</li>
                            <li class="mb-2">"License" means the terms and conditions for use, reproduction, and distribution</li>
                            <li class="mb-2">"Licensor" refers to BixaCloud, the copyright owner</li>
                            <li>"Licensee" refers to any person or entity exercising permissions under this License</li>
                        </ul>
                    </div>
                </div>

                <!-- Grant of License -->
                <div class="license-section">
                    <h5><i data-feather="check-circle" class="icon-sm me-2"></i>2. Grant of License</h5>
                    <div class="license-content">
                        <p class="mb-3">Subject to the terms of this agreement, BixaCloud grants you:</p>
                        <ul class="mb-0">
                            <li class="mb-2">A non-exclusive license to use the software</li>
                            <li class="mb-2">Right to install on one domain</li>
                            <li>Right to create backups</li>
                        </ul>
                    </div>
                </div>

                <!-- Restrictions -->
                <div class="license-section">
                    <h5><i data-feather="slash" class="icon-sm me-2"></i>3. Restrictions</h5>
                    <div class="license-content">
                        <p class="mb-3">You may not:</p>
                        <ul class="mb-0">
                            <li class="mb-2">Modify or create derivative works</li>
                            <li class="mb-2">Reverse engineer the software</li>
                            <li class="mb-2">Remove any copyright notices</li>
                            <li>Redistribute the software</li>
                        </ul>
                    </div>
                </div>

                <!-- Support and Updates -->
                <div class="license-section">
                    <h5><i data-feather="refresh-cw" class="icon-sm me-2"></i>4. Support and Updates</h5>
                    <div class="license-content">
                        <ul class="mb-0">
                            <li class="mb-2">Technical support provided via ticket system</li>
                            <li class="mb-2">Regular updates and security patches</li>
                            <li>Documentation and guides included</li>
                        </ul>
                    </div>
                </div>

                <!-- Disclaimer -->
                <div class="license-section mb-0">
                    <h5><i data-feather="alert-triangle" class="icon-sm me-2"></i>5. Disclaimer</h5>
                    <div class="license-content">
                        <p class="mb-0">THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

