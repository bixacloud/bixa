@extends('layouts.master')

@section('title') Support Bixa @endsection

@section('css')
<style>
    .donation-tier:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }
    .feature-card {
        min-height: 160px;
    }
    .milestone-card {
        border-radius: 10px;
        overflow: hidden;
    }
</style>
@endsection

@section('content')

@component('components.breadcrumb')
    @slot('li_1') Admin @endslot
    @slot('title') Support Bixa @endslot
@endcomponent

<div class="row">
    <!-- Goals Section -->
    <div class="col-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Current Development Goals</h4>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card border mb-0">
                            <div class="card-body text-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-block mb-3">
                                    <i data-feather="server" class="text-primary"></i>
                                </div>
                                <h5>Infrastructure</h5>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                                </div>
                                <p class="text-muted mb-0">Upgrading our servers and infrastructure</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border mb-0">
                            <div class="card-body text-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-block mb-3">
                                    <i data-feather="code" class="text-success"></i>
                                </div>
                                <h5>New Features</h5>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 45%"></div>
                                </div>
                                <p class="text-muted mb-0">Developing advanced management features</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border mb-0">
                            <div class="card-body text-center">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3 d-inline-block mb-3">
                                    <i data-feather="life-buoy" class="text-info"></i>
                                </div>
                                <h5>Support System</h5>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 60%"></div>
                                </div>
                                <p class="text-muted mb-0">Enhancing support system with AI automation</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Options -->
    <div class="col-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Support Options</h4>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="d-flex flex-column gap-3">
                            <a href="https://github.com/sponsors/bixacloud" target="_blank" 
                                class="btn btn-lg btn-primary d-flex align-items-center justify-content-center">
                                <i data-feather="github" class="me-2"></i>
                                <span>
                                    <span class="d-block fw-bold">Sponsor on GitHub</span>
                                    <small>Support us with monthly donations</small>
                                </span>
                            </a>

                            <a href="https://www.buymeacoffee.com/bixacloud" target="_blank" 
                                class="btn btn-lg btn-warning d-flex align-items-center justify-content-center">
                                <i data-feather="coffee" class="me-2"></i>
                                <span>
                                    <span class="d-block fw-bold">Buy us a coffee</span>
                                    <small>Quick one-time support</small>
                                </span>
                            </a>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <div class="d-flex align-items-center">
                                <i data-feather="info" class="icon-sm me-2"></i>
                                <div>All donations help us maintain and improve Bixa for everyone. Thank you for your support!</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Impact Stats -->
    <div class="col-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Impact Statistics</h4>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-sm-3">
                        <div class="card border mb-0">
                            <div class="card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                        <i data-feather="code"></i>
                                    </div>
                                </div>
                                <h3>50+</h3>
                                <p class="text-muted mb-0">Features Released</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card border mb-0">
                            <div class="card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title rounded-circle bg-success-subtle text-success">
                                        <i data-feather="users"></i>
                                    </div>
                                </div>
                                <h3>1000+</h3>
                                <p class="text-muted mb-0">Active Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card border mb-0">
                            <div class="card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                        <i data-feather="git-pull-request"></i>
                                    </div>
                                </div>
                                <h3>200+</h3>
                                <p class="text-muted mb-0">Code Contributions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card border mb-0">
                            <div class="card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title rounded-circle bg-warning-subtle text-warning">
                                        <i data-feather="star"></i>
                                    </div>
                                </div>
                                <h3>95%</h3>
                                <p class="text-muted mb-0">Satisfaction Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="col-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Frequently Asked Questions</h4>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How is the donation used?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="text-muted mb-0">Your donations directly support Bixa's development, server costs, and continuous improvements to the platform. We use these funds to:</p>
                                <ul class="text-muted mb-0">
                                    <li>Maintain and upgrade infrastructure</li>
                                    <li>Develop new features</li>
                                    <li>Improve security and performance</li>
                                    <li>Provide better support services</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Can I cancel my monthly sponsorship?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="text-muted mb-0">Yes, you can cancel your monthly sponsorship at any time through your GitHub Sponsors dashboard. There are no long-term commitments, and you're free to adjust your support level whenever you need.</p>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Do you offer custom sponsorship packages?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p class="text-muted mb-0">Yes! For businesses or larger organizations interested in custom sponsorship packages, please contact us at hi@bixa.us to discuss your requirements.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

