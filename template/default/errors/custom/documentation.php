<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
    <meta charset="utf-8" />
    <title>Documentation - <?= $this->base->get_hostname() ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Complete documentation for Bixa Hosting Management System">
    <link rel="icon" type="image/png" href="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/img/fav.png">
    <link href="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/css/icons.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/css/app.min.css" rel="stylesheet">
    <style>
        .doc-nav {
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .doc-content {
            font-size: 16px;
            line-height: 1.8;
        }
        .doc-section {
            margin-bottom: 3rem;
            padding-top: 1rem;
        }
        .doc-section h2 {
            color: #1a3066;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        .doc-section h3 {
            color: #2a4a8a;
            margin: 2rem 0 1rem;
        }
        .code-block {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 1.5rem;
            margin: 1rem 0;
            border-left: 4px solid #4b70dd;
        }
        .step-guide {
            position: relative;
            padding-left: 45px;
            margin-bottom: 1.5rem;
        }
        .step-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 32px;
            height: 32px;
            background: #4b70dd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .pro-tip {
            background-color: #fff8e8;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        .nav-pills .nav-link {
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            color: #495057;
            border-radius: 6px;
        }
        .nav-pills .nav-link:hover {
            background-color: #f8f9fa;
        }
        .nav-pills .nav-link.active {
            background-color: #4b70dd;
            color: white;
        }
        .doc-content pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 1.5rem;
            border-radius: 6px;
            margin: 1rem 0;
			
        /* Print Styles */
        @media print {
            .doc-nav,
            .navbar {
                display: none !important;
            }
            
            .doc-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .doc-section {
                page-break-inside: avoid;
            }
        }

        /* Additional Mobile Responsiveness */
        @media (max-width: 991.98px) {
            .doc-nav {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                z-index: 1030;
                background: var(--bs-body-bg);
                transition: .3s;
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
            }
            
            .doc-nav.show {
                left: 0;
            }
            
            .doc-content {
                margin-left: 0 !important;
            }
        }
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="dark">
    <!-- Top Navigation -->
    <header class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                                        <img src="<?= base_url() ?>assets/<?= $this->base->get_template() ?>/images/logo-sm.svg" alt="Bixa" height="50" class="auth-logo-dark">
                <span class="ms-2 fw-semibold">Documentation</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>"><i class="bx bx-home me-1"></i> Home</a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </header>

    <div class="container-fluid" style="margin-top: 70px">
        <div class="row g-0">
            <!-- Left Sidebar -->
            <div class="col-lg-3 border-end">
                <div class="doc-nav p-3">
                  
                    <nav class="nav flex-column nav-pills">
                        <div class="nav-heading mb-2 text-muted fw-bold small text-uppercase">Getting Started</div>
                        <a class="nav-link active" href="#introduction">Introduction</a>
                        <a class="nav-link" href="#quick-start">Quick Start Guide</a>

                        <div class="nav-heading mb-2 mt-4 text-muted fw-bold small text-uppercase">Core Features</div>
                        <a class="nav-link" href="#dashboard">Dashboard</a>
                        <a class="nav-link" href="#accounts">Account Management</a>
                        <a class="nav-link" href="#domains">Domain Management</a>
                        <a class="nav-link" href="#ssl">SSL Certificates</a>

                        <div class="nav-heading mb-2 mt-4 text-muted fw-bold small text-uppercase">Configuration</div>
                        <a class="nav-link" href="#mofh">MOFH Integration</a>
                        <a class="nav-link" href="#email">Email Settings</a>
                        <a class="nav-link" href="#security">Security</a>

                        <div class="nav-heading mb-2 mt-4 text-muted fw-bold small text-uppercase">Support</div>
                        <a class="nav-link" href="#tickets">Ticket System</a>
                        <a class="nav-link" href="#faq">FAQ</a>
                        <a class="nav-link" href="#troubleshooting">Troubleshooting</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="doc-content p-4 p-xl-5">
                    <!-- Introduction -->
                    <section class="doc-section" id="introduction">
                        <h2>
                            <i class="bx bx-book-open me-2"></i>
                            Introduction to Bixa
                        </h2>
                        <p class="lead">Welcome to the comprehensive documentation for Bixa Hosting Management System.</p>
                        
                        <div class="alert alert-info mb-4">
                            <div class="d-flex">
                                <i class="bx bx-info-circle fs-4 me-2"></i>
                                <div>
                                    <strong>Latest Version:</strong> v<?= get_version() ?> <?= get_tag() ?>
                                    <p class="mb-0">Make sure you're running the latest version to access all features.</p>
                                </div>
                            </div>
                        </div>

                        <h3>System Requirements</h3>
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <h6><i class="bx bx-server me-2"></i>Server Requirements</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="bx bx-check text-success me-2"></i>PHP 7.4 or higher</li>
                                            <li><i class="bx bx-check text-success me-2"></i>MySQL 5.7+</li>
                                            <li><i class="bx bx-check text-success me-2"></i>Apache/Nginx</li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-6">
                                        <h6><i class="bx bx-window-alt me-2"></i>Browser Support</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="bx bx-check text-success me-2"></i>Chrome (Latest)</li>
                                            <li><i class="bx bx-check text-success me-2"></i>Firefox (Latest)</li>
                                            <li><i class="bx bx-check text-success me-2"></i>Safari (Latest)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
 <!-- Quick Start Guide -->
                        <section class="doc-section" id="quick-start">
                            <h2>
                                <i class="bx bx-rocket me-2"></i>
                                Quick Start Guide
                            </h2>
                            
                            <div class="step-guide">
                                <div class="step-number">1</div>
                                <h5>Dashboard Overview</h5>
                                <p>Access your admin dashboard at <code>https://your-domain.com/admin</code>. The dashboard provides a quick overview of:</p>
                                <ul>
                                    <li>Active clients and accounts</li>
                                    <li>System status</li>
                                    <li>Recent activities</li>
                                    <li>Support tickets</li>
                                </ul>
                            </div>

                            <div class="step-guide">
                                <div class="step-number">2</div>
                                <h5>Basic Configuration</h5>
                                <p>Configure your essential settings:</p>
                                <div class="code-block">
                                    <ol>
                                        <li>Set your hosting name</li>
                                        <li>Configure admin email</li>
                                        <li>Set up SMTP for notifications</li>
                                        <li>Configure reCAPTCHA protection</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="step-guide">
                                <div class="step-number">3</div>
                                <h5>MOFH Integration</h5>
                                <p>Connect your MOFH account:</p>
                                <div class="code-block">
                                    <ol>
                                        <li>Get API credentials from MOFH panel</li>
                                        <li>Enter API username and password</li>
                                        <li>Configure nameservers</li>
                                        <li>Set package details</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="pro-tip">
                                <i class="bx bx-bulb me-2"></i>
                                <strong>Pro Tip:</strong> Test your configuration with a sample account before going live.
                            </div>
                        </section>
<!-- Dashboard Section -->
<section class="doc-section" id="dashboard">
    <h2>
        <i class="bx bx-home-circle me-2"></i>
        Dashboard
    </h2>
    
    <div class="alert alert-info mb-4">
        <i class="bx bx-info-circle me-2"></i>
        The dashboard provides a comprehensive overview of your hosting service and key metrics.
    </div>

    <h3>Overview Cards</h3>
    <div class="card mb-4">
        <div class="card-body">
            <h5>Key Statistics</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-user text-primary fs-2 me-2"></i>
                        <div>
                            <h6>Active Clients</h6>
                            <p class="text-muted mb-0">Total registered users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-server text-success fs-2 me-2"></i>
                        <div>
                            <h6>Active Accounts</h6>
                            <p class="text-muted mb-0">Hosting accounts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-support text-warning fs-2 me-2"></i>
                        <div>
                            <h6>Open Tickets</h6>
                            <p class="text-muted mb-0">Support requests</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-shield text-info fs-2 me-2"></i>
                        <div>
                            <h6>SSL Certificates</h6>
                            <p class="text-muted mb-0">Active certificates</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3>Recent Activities</h3>
    <div class="card mb-4">
        <div class="card-body">
            <ul class="list-unstyled mb-0">
                <li class="mb-3">
                    <div class="d-flex align-items-center">
                        <span class="avatar-xs bg-primary text-white rounded me-2">
                            <i class="bx bx-plus"></i>
                        </span>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">New Account Created</h6>
                            <p class="text-muted mb-0">example.domain.com</p>
                        </div>
                        <small class="text-muted">5 mins ago</small>
                    </div>
                </li>
                <li class="mb-3">
                    <div class="d-flex align-items-center">
                        <span class="avatar-xs bg-success text-white rounded me-2">
                            <i class="bx bx-ticket"></i>
                        </span>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Ticket Resolved</h6>
                            <p class="text-muted mb-0">Ticket #12345</p>
                        </div>
                        <small class="text-muted">1 hour ago</small>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="pro-tip">
        <i class="bx bx-bulb me-2"></i>
        <strong>Pro Tip:</strong> You can customize the dashboard layout in the settings.
    </div>
</section>

<!-- Account Management Section -->
<section class="doc-section" id="accounts">
    <h2>
        <i class="bx bx-user-circle me-2"></i>
        Account Management
    </h2>

    <h3>Creating New Accounts</h3>
    <div class="card mb-4">
        <div class="card-body">
            <h5>Account Creation Steps:</h5>
            <ol>
                <li>Click "Create New Account" in the accounts section</li>
                <li>Choose domain/subdomain
                    <ul>
                        <li>Enter desired subdomain name</li>
                        <li>Select available extension</li>
                    </ul>
                </li>
                <li>Set account credentials
                    <ul>
                        <li>Username: Will be generated automatically</li>
                        <li>Password: Must meet security requirements</li>
                    </ul>
                </li>
                <li>Select hosting package</li>
                <li>Submit for creation</li>
            </ol>
        </div>
    </div>

    <h3>Managing Existing Accounts</h3>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Suspend Account</td>
                            <td>Temporarily disable hosting access</td>
                        </tr>
                        <tr>
                            <td>Reactivate Account</td>
                            <td>Restore access to suspended account</td>
                        </tr>
                        <tr>
                            <td>Reset Password</td>
                            <td>Change account credentials</td>
                        </tr>
                        <tr>
                            <td>Delete Account</td>
                            <td>Permanently remove account (irreversible)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pro-tip">
        <i class="bx bx-bulb me-2"></i>
        <strong>Pro Tip:</strong> Regularly review inactive accounts to optimize server resources.
    </div>
</section>

<!-- Domain Management Section -->
<section class="doc-section" id="domains">
    <h2>
        <i class="bx bx-globe me-2"></i>
        Domain Management
    </h2>

    <h3>Domain Extensions</h3>
    <div class="card mb-4">
        <div class="card-body">
            <h5>Managing Domain Extensions:</h5>
            <div class="mb-4">
                <h6>Adding New Extensions</h6>
                <ol>
                    <li>Go to Domain Extensions section</li>
                    <li>Click "Add New Extension"</li>
                    <li>Enter domain extension (e.g., .example.com)</li>
                    <li>Set as active/inactive</li>
                    <li>Save changes</li>
                </ol>
            </div>
            
            <div class="mb-4">
                <h6>Extension Settings</h6>
                <ul>
                    <li>Status: Enable/disable for user selection</li>
                    <li>Visibility: Public/Private availability</li>
                    <li>Restrictions: User group access</li>
                    <li>Priority: Order in selection lists</li>
                </ul>
            </div>
        </div>
    </div>

    <h3>DNS Management</h3>
    <div class="card mb-4">
        <div class="card-body">
            <h5>DNS Configuration:</h5>
            <div class="code-block">
                <p>Default Nameservers:</p>
                <ul>
                    <li>NS1: ns1.byet.org</li>
                    <li>NS2: ns2.byet.org</li>
                </ul>
            </div>
            
            <h6 class="mt-4">Custom Nameservers</h6>
            <p>To use custom nameservers:</p>
            <ol>
                <li>Configure nameserver records at domain registrar</li>
                <li>Update nameserver settings in Bixa</li>
                <li>Wait for DNS propagation (24-48 hours)</li>
            </ol>
        </div>
    </div>

    <div class="pro-tip">
        <i class="bx bx-bulb me-2"></i>
        <strong>Pro Tip:</strong> Regular DNS health checks ensure optimal domain performance.
    </div>
</section>
                        <!-- MOFH Integration -->
                        <section class="doc-section" id="mofh">
                            <h2>
                                <i class="bx bx-server me-2"></i>
                                MOFH Integration
                            </h2>
                            
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="mb-3">API Configuration Steps</h5>
                                    
                                    <div class="mb-4">
                                        <h6>1. Access MOFH Panel</h6>
                                        <ul>
                                            <li>Login to your MOFH reseller panel</li>
                                            <li>Navigate to API Settings</li>
                                        </ul>
                                    </div>

                                    <div class="mb-4">
                                        <h6>2. Get API Credentials</h6>
                                        <ul>
                                            <li>Copy API Username</li>
                                            <li>Generate API Password</li>
                                            <li>Save allowed IP addresses</li>
                                        </ul>
                                    </div>

                                    <div class="mb-4">
                                        <h6>3. Configure in Bixa</h6>
                                        <ul>
                                            <li>Enter API credentials in Bixa settings</li>
                                            <li>Set up nameservers</li>
                                            <li>Configure package details</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="pro-tip">
                                <i class="bx bx-bulb me-2"></i>
                                <strong>Pro Tip:</strong> Keep your API credentials secure and never share them publicly.
                            </div>
                        </section>

                        <!-- Email Settings -->
                        <section class="doc-section" id="email">
                            <h2>
                                <i class="bx bx-envelope me-2"></i>
                                Email Configuration
                            </h2>

                            <div class="alert alert-warning mb-4">
                                <i class="bx bx-info-circle me-2"></i>
                                Proper email configuration is crucial for notifications and user communication.
                            </div>

                            <h3>SMTP Setup</h3>
                            <div class="code-block">
                                <p><strong>Required Information:</strong></p>
                                <ul>
                                    <li>SMTP Host</li>
                                    <li>SMTP Port (usually 465 or 587)</li>
                                    <li>Username and Password</li>
                                    <li>Encryption method (SSL/TLS)</li>
                                </ul>
                            </div>

                            <h3>Email Templates</h3>
                            <p>Customize your email templates for various notifications:</p>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Template</th>
                                            <th>Description</th>
                                            <th>Variables</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Welcome Email</td>
                                            <td>Sent to new users</td>
                                            <td>{username}, {site_name}</td>
                                        </tr>
                                        <tr>
                                            <td>Account Created</td>
                                            <td>New hosting account</td>
                                            <td>{domain}, {username}, {password}</td>
                                        </tr>
                                        <tr>
                                            <td>Ticket Update</td>
                                            <td>Support ticket replies</td>
                                            <td>{ticket_id}, {message}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <!-- Security Settings -->
                        <section class="doc-section" id="security">
                            <h2>
                                <i class="bx bx-shield me-2"></i>
                                Security
                            </h2>

                            <h3>Captcha Protection</h3>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5>Available Options:</h5>
                                    
                                    <div class="mb-3">
                                        <h6><i class="bx bxl-google me-2"></i>Google reCAPTCHA</h6>
                                        <p>Recommended for most users. Requires site and secret keys from Google.</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6><i class="bx bx-check-shield me-2"></i>hCaptcha</h6>
                                        <p>Alternative option with good privacy features.</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6><i class="bx bx-cloud me-2"></i>Cloudflare Turnstile</h6>
                                        <p>Integrated option for Cloudflare users.</p>
                                    </div>
                                </div>
                            </div>
							<!-- OAuth Settings -->
                        <section class="doc-section" id="oauth">
                            <h2>
                                <i class="bx bx-lock-alt me-2"></i>
                                OAuth Integration
                            </h2>

                            <h3>Google OAuth Setup</h3>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5>Configuration Steps:</h5>
                                    <ol>
                                        <li>Go to Google Cloud Console</li>
                                        <li>Create or select a project</li>
                                        <li>Enable Google+ API</li>
                                        <li>Create OAuth 2.0 credentials</li>
                                        <li>Set up authorized redirect URI:
                                            <code>https://your-domain.com/oauth/google/callback</code>
                                        </li>
                                    </ol>
                                </div>
                            </div>

                            <h3>Facebook OAuth Setup</h3>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5>Configuration Steps:</h5>
                                    <ol>
                                        <li>Visit Facebook Developers Console</li>
                                        <li>Create new app</li>
                                        <li>Add Facebook Login product</li>
                                        <li>Configure OAuth settings</li>
                                        <li>Set OAuth redirect URI:
                                            <code>https://your-domain.com/oauth/facebook/callback</code>
                                        </li>
                                    </ol>
                                </div>
                            </div>

                            <h3>GitHub OAuth Setup</h3>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5>Configuration Steps:</h5>
                                    <ol>
                                        <li>Go to GitHub Developer Settings</li>
                                        <li>Create new OAuth App</li>
                                        <li>Set authorization callback URL:
                                            <code>https://your-domain.com/oauth/github/callback</code>
                                        </li>
                                        <li>Copy Client ID and Client Secret</li>
                                    </ol>
                                </div>
                            </div>
                        </section>

                        <!-- SSL Configuration -->
                        <section class="doc-section" id="ssl">
                            <h2>
                                <i class="bx bx-lock me-2"></i>
                                SSL Management
                            </h2>

                            <div class="alert alert-info mb-4">
                                <i class="bx bx-info-circle me-2"></i>
                                SSL certificates are essential for secure website connections. Bixa supports multiple SSL providers.
                            </div>

                            <h3>Let's Encrypt Integration</h3>
                            <div class="code-block">
                                <h5>Configuration:</h5>
                                <ul>
                                    <li>Directory URL: <code>https://acme-v02.api.letsencrypt.org/directory</code></li>
                                    <li>Enable DNS validation</li>
                                    <li>Set up auto-renewal</li>
                                </ul>
                            </div>

                            <h3>ZeroSSL Setup</h3>
                            <div class="code-block">
                                <h5>Required Information:</h5>
                                <ul>
                                    <li>API Key</li>
                                    <li>EAB Credentials</li>
                                    <li>Directory URL</li>
                                </ul>
                            </div>

                            <div class="pro-tip">
                                <i class="bx bx-bulb me-2"></i>
                                <strong>Pro Tip:</strong> Enable DNS validation for more reliable certificate issuance.
                            </div>
                        </section>

                        <!-- Support System -->
                        <section class="doc-section" id="tickets">
                            <h2>
                                <i class="bx bx-support me-2"></i>
                                Support Ticket System
                            </h2>

                            <h3>Ticket Management</h3>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5>Key Features:</h5>
                                    <div class="row g-4">
                                        <div class="col-sm-6">
                                            <div class="d-flex">
                                                <i class="bx bx-check-circle text-success fs-4 me-2"></i>
                                                <div>
                                                    <h6>Automated Notifications</h6>
                                                    <p class="text-muted mb-0">Email alerts for new tickets and replies</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex">
                                                <i class="bx bx-check-circle text-success fs-4 me-2"></i>
                                                <div>
                                                    <h6>Priority Levels</h6>
                                                    <p class="text-muted mb-0">Categorize tickets by urgency</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex">
                                                <i class="bx bx-check-circle text-success fs-4 me-2"></i>
                                                <div>
                                                    <h6>Status Tracking</h6>
                                                    <p class="text-muted mb-0">Monitor ticket progress</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex">
                                                <i class="bx bx-check-circle text-success fs-4 me-2"></i>
                                                <div>
                                                    <h6>Rich Text Editor</h6>
                                                    <p class="text-muted mb-0">Format responses with ease</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Troubleshooting -->
                        <section class="doc-section" id="troubleshooting">
                            <h2>
                                <i class="bx bx-help-circle me-2"></i>
                                Troubleshooting
                            </h2>

                            <div class="accordion" id="troubleshootingAccordion">
                                <!-- Common Issues -->
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#issue1">
                                            Email Notifications Not Working
                                        </button>
                                    </h3>
                                    <div id="issue1" class="accordion-collapse collapse show" 
                                         data-bs-parent="#troubleshootingAccordion">
                                        <div class="accordion-body">
                                            <ol>
                                                <li>Verify SMTP credentials</li>
                                                <li>Check spam settings</li>
                                                <li>Ensure correct ports are open</li>
                                                <li>Validate email templates</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#issue2">
                                            SSL Certificate Issues
                                        </button>
                                    </h3>
                                    <div id="issue2" class="accordion-collapse collapse" 
                                         data-bs-parent="#troubleshootingAccordion">
                                        <div class="accordion-body">
                                            <ol>
                                                <li>Verify domain DNS settings</li>
                                                <li>Check domain validation</li>
                                                <li>Ensure proper HTTP validation</li>
                                                <li>Validate SSL provider credentials</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#issue3">
                                            Account Creation Failed
                                        </button>
                                    </h3>
                                    <div id="issue3" class="accordion-collapse collapse" 
                                         data-bs-parent="#troubleshootingAccordion">
                                        <div class="accordion-body">
                                            <ol>
                                                <li>Check MOFH API credentials</li>
                                                <li>Verify domain availability</li>
                                                <li>Ensure package limits</li>
                                                <li>Check error logs</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- FAQ Section -->
                        <section class="doc-section" id="faq">
                            <h2>
                                <i class="bx bx-question-mark me-2"></i>
                                Frequently Asked Questions
                            </h2>

                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#faq1">
                                            How do I update Bixa to the latest version?
                                        </button>
                                    </h3>
                                    <div id="faq1" class="accordion-collapse collapse show" 
                                         data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <ol>
                                                <li>Download the latest version from your account</li>
                                                <li>Backup your current installation</li>
                                                <li>Upload and replace the files</li>
                                                <li>Run the update script</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#faq2">
                                            Can I customize the email templates?
                                        </button>
                                    </h3>
                                    <div id="faq2" class="accordion-collapse collapse" 
                                         data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Yes, all email templates can be customized through the admin panel. You can modify 
                                            the content, styling, and use variables to personalize messages.
                                        </div>
                                    </div>
                                </div>

                                <!-- Add more FAQs as needed -->
                            </div>
                        </section>

                        <!-- Footer -->
                        <div class="border-top mt-5 pt-4">
                            <p class="text-muted text-center">
                                Still have questions? Contact us at 
                                <a href="mailto:hi@bixa.app">hi@bixa.app</a>
                            </p>
                        </div>
                    </div>
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

    <!-- Page Specific JS -->
    <script>
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Active section highlighting
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.doc-section');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });

        

        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }

        // Copy code blocks
        document.querySelectorAll('.code-block').forEach(block => {
            const code = block.innerText;
            const copyBtn = document.createElement('button');
            copyBtn.className = 'btn btn-sm btn-light position-absolute top-0 end-0 m-2';
            copyBtn.innerHTML = '<i class="bx bx-copy"></i>';
            copyBtn.addEventListener('click', () => {
                navigator.clipboard.writeText(code);
                copyBtn.innerHTML = '<i class="bx bx-check text-success"></i>';
                setTimeout(() => {
                    copyBtn.innerHTML = '<i class="bx bx-copy"></i>';
                }, 2000);
            });
            block.style.position = 'relative';
            block.appendChild(copyBtn);
        });

        // Search functionality
        const searchInput = document.querySelector('input[type="search"]');
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const sections = document.querySelectorAll('.doc-section');
            
            sections.forEach(section => {
                const content = section.textContent.toLowerCase();
                const searchMatch = content.includes(query);
                section.style.display = searchMatch ? 'block' : 'none';
            });
        });

        // Responsive sidebar
        const sidebar = document.querySelector('.doc-nav');
        const toggleBtn = document.querySelector('.navbar-toggler');
        
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        // Close sidebar on mobile when clicking a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                }
            });
        });

        // Progress indication
        window.addEventListener('scroll', function() {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            document.querySelector('.progress-bar').style.width = scrolled + '%';
        });
    </script>
</body>
</html>