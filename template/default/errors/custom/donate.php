<!DOCTYPE html>
<html lang="en" xml:lang="en">

<head>
    <meta charset="utf-8" />
    <title>Support Bixa Development - <?= $this->base->get_hostname() ?></title>
    <meta name="description" content="Support Bixa's development and help us create better hosting management solutions for everyone.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/img/fav.png">
    <link href="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/css/app.min.css" rel="stylesheet" type="text/css" />
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
        .sponsor-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="dark">
    <div class="container-fluid">
        <!-- Hero Section -->
        <div class="row">
            <div class="col-12 px-0">
                <div class="bg-primary text-white text-center py-5">
                    <div class="container">
                                        <img src="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/images/logo-sm.svg" alt="Bixa" height="50" class="auth-logo-dark">
                        <h1 class="display-5 fw-bold mb-3">Support Bixa Development</h1>
                        <p class="lead mb-4">Help us build the future of hosting management solutions</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="#donate" class="btn btn-lg btn-warning">
                                <i class="bx bx-heart me-1"></i> Support Now
                            </a>
                            <a href="#about" class="btn btn-lg btn-light">
                                <i class="bx bx-info-circle me-1"></i> Learn More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-5">
            <!-- Current Goals Section -->
            <div class="row mb-5" id="about">
                <div class="col-12 text-center mb-4">
                    <h2>Current Development Goals</h2>
                    <p class="text-muted">Help us reach our next milestones</p>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 milestone-card">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-block mb-3">
                                <i class="bx bx-server text-primary display-6"></i>
                            </div>
                            <h4>Infrastructure</h4>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                            </div>
                            <p class="text-muted">Upgrading our servers and infrastructure to handle growing demand</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 milestone-card">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-block mb-3">
                                <i class="bx bx-code-alt text-success display-6"></i>
                            </div>
                            <h4>New Features</h4>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 45%"></div>
                            </div>
                            <p class="text-muted">Developing advanced hosting management features and integrations</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 milestone-card">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 d-inline-block mb-3">
                                <i class="bx bx-support text-info display-6"></i>
                            </div>
                            <h4>Support System</h4>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 60%"></div>
                            </div>
                            <p class="text-muted">Enhancing our support system with AI and automation</p>
                        </div>
                    </div>
                </div>
            </div>

           <!-- Donation Options -->
            <div class="row justify-content-center mb-5" id="donate">
                <div class="col-12 text-center mb-4">
                    <h2>Support Options</h2>
                    <p class="text-muted">Choose how you'd like to support Bixa</p>
                </div>
                
                <!-- Donation Buttons -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="d-flex flex-column gap-3">
                                <!-- GitHub Sponsors -->
                                <a href="https://github.com/sponsors/bixacloud" target="_blank" 
                                   class="btn btn-lg btn-primary d-flex align-items-center justify-content-center">
                                    <i class="bx bxl-github fs-3 me-2"></i>
                                    <span>
                                        <span class="d-block fw-bold">Sponsor on GitHub</span>
                                        <small>Support us with monthly donations</small>
                                    </span>
                                </a>


                                <!-- Buy Me a Coffee -->
                                <a href="https://www.buymeacoffee.com/bixacloud" target="_blank" 
                                   class="btn btn-lg btn-warning d-flex align-items-center justify-content-center">
                                    <i class="bx bx-coffee fs-3 me-2"></i>
                                    <span>
                                        <span class="d-block fw-bold">Buy us a coffee</span>
                                        <small>Quick one-time support</small>
                                    </span>
                                </a>
                            </div>

                            <div class="mt-4">
                                <div class="alert alert-light border mb-0">
                                    <i class="bx bx-info-circle me-1"></i>
                                    All donations help us maintain and improve Bixa for everyone. Thank you for your support!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Impact Section -->
            <div class="row mb-5">
                <div class="col-12 text-center mb-4">
                    <h2>Your Impact</h2>
                    <p class="text-muted">See how your support helps us grow</p>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card border-0 text-center feature-card">
                        <div class="card-body">
                            <div class="avatar-sm mx-auto mb-4">
                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary font-size-24">
                                    <i class="bx bx-code-block"></i>
                                </div>
                            </div>
                            <h3 class="counter-value" data-target="50">50+</h3>
                            <p class="text-muted">Features Released</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card border-0 text-center feature-card">
                        <div class="card-body">
                            <div class="avatar-sm mx-auto mb-4">
                                <div class="avatar-title rounded-circle bg-success-subtle text-success font-size-24">
                                    <i class="bx bx-user-check"></i>
                                </div>
                            </div>
                            <h3 class="counter-value" data-target="1000">1000+</h3>
                            <p class="text-muted">Active Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card border-0 text-center feature-card">
                        <div class="card-body">
                            <div class="avatar-sm mx-auto mb-4">
                                <div class="avatar-title rounded-circle bg-info-subtle text-info font-size-24">
                                    <i class="bx bx-git-pull-request"></i>
                                </div>
                            </div>
                            <h3 class="counter-value" data-target="200">200+</h3>
                            <p class="text-muted">Code Contributions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card border-0 text-center feature-card">
                        <div class="card-body">
                            <div class="avatar-sm mx-auto mb-4">
                                <div class="avatar-title rounded-circle bg-warning-subtle text-warning font-size-24">
                                    <i class="bx bx-star"></i>
                                </div>
                            </div>
                            <h3 class="counter-value" data-target="95">95%</h3>
                            <p class="text-muted">Satisfaction Rate</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Frequently Asked Questions</h3>
                            
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            How is the donation used?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Your donations directly support Bixa's development, server costs, and continuous improvements to the platform.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            Is my donation tax-deductible?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Currently, donations are not tax-deductible. We recommend consulting with your tax advisor for specific guidance.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
									<div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            Can I cancel my monthly sponsorship?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Yes, you can cancel your monthly sponsorship at any time through your GitHub Sponsors dashboard or PayPal settings.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                            Do you offer custom sponsorship packages?
                                        </button>
                                    </h2>
                                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Yes! For businesses or larger organizations interested in custom sponsorship packages, please contact us at sponsors@bixacloud.com
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sponsors Section -->
            <div class="row mb-5">
                <div class="col-12 text-center mb-4">
                    <h2>Our Amazing Sponsors</h2>
                    <p class="text-muted">Join these awesome supporters</p>
                </div>
                
                <!-- Gold Sponsors -->
                <div class="col-12 mb-4">
                    <h5 class="text-center mb-4">Gold Sponsors</h5>
                    <div class="row justify-content-center">
                        <div class="col-md-3 col-sm-4 col-6 text-center mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <img src="/api/placeholder/100/100" alt="Sponsor" class="img-fluid rounded-circle mb-2" style="width: 64px;">
                                    <h6 class="mb-1">Company Name</h6>
                                    <small class="text-muted">Since Jan 2024</small>
                                </div>
                            </div>
                        </div>
                        <!-- Add more gold sponsors here -->
                    </div>
                </div>

                <!-- Silver Sponsors -->
                <div class="col-12">
                    <h5 class="text-center mb-4">Silver Sponsors</h5>
                    <div class="row justify-content-center">
                        <div class="col-md-2 col-sm-3 col-4 text-center mb-3">
                            <img src="/api/placeholder/80/80" alt="Sponsor" class="img-fluid rounded-circle mb-2">
                            <h6 class="mb-0 small">Sponsor Name</h6>
                        </div>
                        <!-- Add more silver sponsors here -->
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3>Need Help?</h3>
                            <p class="text-muted mb-4">Have questions about donations or sponsorships? We're here to help!</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="mailto:support@bixa.app" class="btn btn-soft-primary">
                                    <i class="bx bx-envelope me-1"></i> Email Us
                                </a>
                                <a href="https://discord.gg/bixacloud" target="_blank" class="btn btn-soft-info">
                                    <i class="bx bxl-discord me-1"></i> Join Discord
                                </a>
                                <a href="https://twitter.com/bixacloud" target="_blank" class="btn btn-soft-secondary">
                                    <i class="bx bxl-twitter me-1"></i> Follow Updates
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-4">
                        © <?= date('Y') ?> Bixa. Crafted with <i class="mdi mdi-heart text-danger"></i> by BixaCloud
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/libs/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/libs/metismenu/metisMenu.min.js"></script>
    <script src="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/libs/node-waves/waves.min.js"></script>
    <script src="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/libs/feather-icons/feather.min.js"></script>

    <!-- Counter Animation -->
    <script>
        // Simple counter animation
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.counter-value');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                let count = 0;
                const speed = 2000 / target; // 2 seconds duration

                const updateCount = () => {
                    if(count < target) {
                        count++;
                        counter.innerText = count + (counter.innerText.includes('%') ? '%' : '+');
                        setTimeout(updateCount, speed);
                    }
                };

                updateCount();
            });
        });
    </script>
</body>
</html>