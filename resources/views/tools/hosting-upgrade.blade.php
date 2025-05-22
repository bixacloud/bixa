@extends('layouts.master')

@section('title') Upgrade Your Hosting Plan @endsection

@section('css')
<style>
    .pricing-card {
        position: relative;
        overflow: hidden;
    }

    .corner-ribbon {
        position: absolute;
        top: 8px;
        right: -34px;
        width: 120px;
        padding: 8px 0;
        transform: rotate(45deg);
        text-align: center;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        z-index: 1;
    }

    .current-plan-badge {
        background: #0ab39c;
        color: #fff;
    }

    .popular-badge {
        background: #405189;
        color: #fff;
    }

    .plan-features {
        list-style: none;
        padding: 0;
        margin: 2rem 0;
    }

    .plan-features li {
        padding: 0.75rem 0;
        display: flex;
        align-items: center;
        color: #495057;
        border-bottom: 1px solid #e9e9ef;
    }

    .plan-features li:last-child {
        border-bottom: none;
    }

    .feature-check {
        color: #0ab39c;
        margin-right: 10px;
    }

    .feature-cross {
        color: #f06548;
        margin-right: 10px;
    }

    .price-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #405189;
    }

    .price-duration {
        font-size: 0.9rem;
        color: #74788d;
    }
    
    .btn.btn-primary.active {
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Choose Your Perfect Plan</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <!-- Free Plan -->
                                <div class="col-xl-3 col-sm-6">
                                    <div class="card pricing-card mb-xl-0">
                                        <div class="card-body p-4">
                                            <div class="corner-ribbon current-plan-badge">Current</div>
                                            <div class="text-center">
                                                <h5 class="font-size-16 mb-3">Free Hosting</h5>
                                                <h1 class="fw-semibold mb-3">$0 <span class="text-muted font-size-16 fw-normal">/ Forever</span></h1>
                                                
                                                <ul class="list-unstyled plan-features mt-4">
                                                    <li><i class="fas fa-check feature-check"></i> <strong>5 GB</strong> Disk Space</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>Unlimited</strong> Bandwidth</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>Unlimited</strong> Hosted Domains</li>
                                                    <li><i class="fas fa-times feature-cross"></i> Email Accounts</li>
                                                    <li><i class="fas fa-times feature-cross"></i> cPanel Control Panel</li>
                                                    <li><i class="fas fa-times feature-cross"></i> PHP Version Selection</li>
                                                </ul>

                                                <div class="mt-4">
                                                    <a href="https://ifastnet.com/portal/?aff={{ $affiliateId }}" class="btn btn-light w-100">Continue with Free Plan</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Basic Plan -->
                                <div class="col-xl-3 col-sm-6">
                                    <div class="card pricing-card mb-xl-0">
                                        <div class="card-body p-4">
                                            <div class="text-center">
                                                <h5 class="font-size-16 mb-3">Basic</h5>
                                                <h1 class="fw-semibold mb-3">$2.99 <span class="text-muted font-size-16 fw-normal">/ Month</span></h1>

                                                <ul class="list-unstyled plan-features mt-4">
                                                    <li><i class="fas fa-check feature-check"></i> <strong>5 GB</strong> Disk Space</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>250 GB</strong> Bandwidth</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>1</strong> Hosted Domain</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>1</strong> Email Account</li>
                                                    <li><i class="fas fa-check feature-check"></i> cPanel Control Panel</li>
                                                    <li><i class="fas fa-check feature-check"></i> PHP Version Selection</li>
                                                </ul>

                                                <div class="mt-4">
                                                    <a href="https://ifastnet.com/portal/cart.php?a=add&pid=78&aff={{ $affiliateId }}" 
                                                       class="btn btn-primary w-100">Upgrade Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Premium Plan -->
                                <div class="col-xl-3 col-sm-6">
                                    <div class="card pricing-card mb-xl-0">
                                        <div class="card-body p-4">
                                            <div class="corner-ribbon popular-badge">Popular</div>
                                            <div class="text-center">
                                                <h5 class="font-size-16 mb-3">Premium</h5>
                                                <h1 class="fw-semibold mb-3">$5.99 <span class="text-muted font-size-16 fw-normal">/ Month</span></h1>

                                                <ul class="list-unstyled plan-features mt-4">
                                                    <li><i class="fas fa-check feature-check"></i> <strong>Unlimited</strong> Disk Space</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>250 GB</strong> Bandwidth</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>20</strong> Hosted Domains</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>100</strong> Email Accounts</li>
                                                    <li><i class="fas fa-check feature-check"></i> cPanel Control Panel</li>
                                                    <li><i class="fas fa-check feature-check"></i> PHP Version Selection</li>
                                                </ul>

                                                <div class="mt-4">
                                                    <a href="https://ifastnet.com/portal/cart.php?a=add&pid=3&aff={{ $affiliateId }}" 
                                                       class="btn btn-primary w-100">Upgrade Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ultimate Plan -->
                                <div class="col-xl-3 col-sm-6">
                                    <div class="card pricing-card mb-xl-0">
                                        <div class="card-body p-4">
                                            <div class="text-center">
                                                <h5 class="font-size-16 mb-3">Ultimate</h5>
                                                <h1 class="fw-semibold mb-3">$8.99 <span class="text-muted font-size-16 fw-normal">/ Month</span></h1>

                                                <ul class="list-unstyled plan-features mt-4">
                                                    <li><i class="fas fa-check feature-check"></i> <strong>Unlimited</strong> Disk Space</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>Unlimited</strong> Bandwidth</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>Unlimited</strong> Hosted Domains</li>
                                                    <li><i class="fas fa-check feature-check"></i> <strong>Unlimited</strong> Email Accounts</li>
                                                    <li><i class="fas fa-check feature-check"></i> Premium Support</li>
                                                    <li><i class="fas fa-check feature-check"></i> All Advanced Features</li>
                                                </ul>

                                                <div class="mt-4">
                                                    <a href="https://ifastnet.com/portal/cart.php?a=add&pid=4&aff={{ $affiliateId }}" 
                                                       class="btn btn-primary w-100">Upgrade Now</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparison Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Detailed Feature Comparison</h4>
                            <div class="table-responsive comparison-table">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-nowrap" style="width: 25%">Features</th>
                                            <th class="text-center">Free</th>
                                            <th class="text-center">Basic</th>
                                            <th class="text-center">Premium</th>
                                            <th class="text-center">Ultimate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Disk Space</td>
                                            <td class="text-center">5 GB</td>
                                            <td class="text-center">5 GB</td>
                                            <td class="text-center">Unlimited</td>
                                            <td class="text-center">Unlimited</td>
                                        </tr>
                                        <tr>
                                            <td>Bandwidth</td>
                                            <td class="text-center">Unlimited</td>
                                            <td class="text-center">250 GB</td>
                                            <td class="text-center">250 GB</td>
                                            <td class="text-center">Unlimited</td>
                                        </tr>
                                        <tr>
                                            <td>Hosted Domains</td>
                                            <td class="text-center">Unlimited</td>
                                            <td class="text-center">1</td>
                                            <td class="text-center">20</td>
                                            <td class="text-center">Unlimited</td>
                                        </tr>
                                        <tr>
                                            <td>Email Accounts</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center">1</td>
                                            <td class="text-center">100</td>
                                            <td class="text-center">Unlimited</td>
                                        </tr>
                                        <tr>
                                            <td>cPanel Control Panel</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td>PHP Version Selection</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td>PHP mail() Support</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td>Full DNS Management</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td>Remote MySQL Support</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td>Python/Node.js Support</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Frequently Asked Questions</h4>
                            
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                            Can I upgrade my plan later?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Yes, you can upgrade your hosting plan at any time. Your files and settings will be preserved during the upgrade process.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                            What happens to my current hosting after upgrade?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Your hosting account will be automatically upgraded with the new features and resources. There will be minimal to no downtime during the transition.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                            Is there a money-back guarantee?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Yes, we offer a 30-day money-back guarantee for all paid hosting plans. If you're not satisfied, you can request a full refund within this period.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Note -->
            <div class="row mt-4 mb-4">
                <div class="col-12">
                    <div class="alert alert-info border-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i data-feather="info" class="me-2 icon-lg"></i>
                            <div>
                                <h5 class="alert-heading">Need Help Choosing?</h5>
                                <p class="mb-0">Not sure which plan is right for you? Contact our support team for personalized assistance in selecting the perfect hosting solution for your needs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Add hover effects to pricing cards
        const pricingCards = document.querySelectorAll('.pricing-card');
        pricingCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                pricingCards.forEach(c => c.classList.remove('shadow-lg'));
                this.classList.add('shadow-lg');
            });
            card.addEventListener('mouseleave', function() {
                this.classList.remove('shadow-lg');
            });
        });
    });
</script>
@endsection