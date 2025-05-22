@extends('layouts.master')

@section('title') Legal Information @endsection

@section('css')
<style>
    .legal-nav {
        position: sticky;
        top: 20px;
    }
    .legal-nav .nav-link.active {
        color: #fff;
        background-color: #5156be;
    }
    .legal-nav .nav-link {
        color: #495057;
        padding: 1rem;
        border-radius: 0.25rem;
        margin-bottom: 0.5rem;
    }
    .legal-content {
        font-size: 15px;
        line-height: 1.6;
    }
    .legal-content h2 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .legal-content p, .legal-content ul {
        margin-bottom: 1rem;
    }
    .scroll-content {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        padding-right: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left Navigation -->
        <div class="col-lg-3">
            <div class="legal-nav">
                <div class="nav flex-column nav-pills" role="tablist">
                    <a class="nav-link active" data-bs-toggle="pill" href="#terms" role="tab">
                        <i class="bx bx-file me-2"></i>Terms of Service
                    </a>
                    <a class="nav-link" data-bs-toggle="pill" href="#privacy" role="tab">
                        <i class="bx bx-shield-quarter me-2"></i>Privacy Policy
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                           <!-- Terms of Service -->
                                <div class="tab-pane fade show active" id="terms" role="tabpanel">
                                    <div class="scroll-content legal-content">
                                        <!-- Terms Content -->
<div class="terms-content">
    <h2>1. Acceptance of Terms</h2>
    <p>By accessing and using the services provided by Bixa, you agree to accept and comply with these terms and conditions. These terms may be updated periodically without notice.</p>

    <h2>2. Service Description</h2>
    <p>Bixa provides free web hosting services which include:</p>
    <ul>
        <li>Free web space</li>
        <li>MySQL databases</li>
        <li>FTP access</li>
        <li>cPanel control panel</li>
        <li>Basic customer support</li>
    </ul>

    <h2>3. Account Terms</h2>
    <p><strong>3.1 Account Limits</strong></p>
    <ul>
        <li>Maximum of 3 free hosting accounts per person</li>
        <li>Each account receives 1000 MB disk space</li>
        <li>Each account is allowed unlimited bandwidth</li>
        <li>Each account can host one primary domain and unlimited subdomains</li>
    </ul>

    <p><strong>3.2 Account Requirements</strong></p>
    <ul>
        <li>You must be at least 13 years old to use our services</li>
        <li>You must provide accurate and complete registration information</li>
        <li>You are responsible for maintaining the security of your account credentials</li>
        <li>You must notify us immediately of any unauthorized account access</li>
    </ul>

    <h2>4. Prohibited Content and Activities</h2>
    <p>The following content and activities are strictly prohibited:</p>
    <ul>
        <li>Adult/pornographic content</li>
        <li>Phishing or fraudulent content</li>
        <li>Malware, viruses, or harmful code</li>
        <li>Copyright infringement</li>
        <li>Spam or mass mailing services</li>
        <li>IRC scripts or bots</li>
        <li>Mining cryptocurrency</li>
        <li>File hosting/sharing services</li>
        <li>VPN/Proxy services</li>
        <li>Streaming services</li>
        <li>Any illegal content or activities</li>
    </ul>

    <h2>5. Resource Usage</h2>
    <p><strong>Your account may not use excessive amounts of system resources, including:</strong></p>
    <ul>
        <li>CPU usage exceeding 50% for more than 30 seconds</li>
        <li>Running resource-intensive scripts</li>
        <li>Creating excessive MySQL connections</li>
        <li>Running background processes</li>
        <li>Using the service for file storage rather than web hosting</li>
    </ul>

    <h2>6. Service Availability and Support</h2>
    <p><strong>6.1 Service Level</strong></p>
    <ul>
        <li>We strive to maintain 99% uptime but do not guarantee uninterrupted service</li>
        <li>Maintenance and upgrades may require temporary service interruption</li>
        <li>Support is provided on a best-effort basis through our ticket system</li>
    </ul>

    <h2>7. Account Suspension and Termination</h2>
    <p>We reserve the right to suspend or terminate accounts that:</p>
    <ul>
        <li>Violate these Terms of Service</li>
        <li>Remain inactive for more than 30 days</li>
        <li>Abuse server resources</li>
        <li>Engage in prohibited activities</li>
        <li>Receive valid abuse reports</li>
    </ul>

    <h2>8. Data Protection and Backups</h2>
    <p><strong>8.1 Your Responsibilities</strong></p>
    <ul>
        <li>You are responsible for maintaining backups of your website content</li>
        <li>We do not guarantee data recovery in case of loss</li>
        <li>You must comply with applicable data protection laws</li>
    </ul>

    <h2>9. Advertisements</h2>
    <p>By using our free hosting service, you agree that:</p>
    <ul>
        <li>We may place advertisements on your website</li>
        <li>These advertisements must not be blocked or hidden</li>
        <li>Tampering with advertisement code is prohibited</li>
    </ul>

    <h2>10. Disclaimer of Warranties</h2>
    <p>The service is provided "as is" without warranties of any kind. We are not responsible for:</p>
    <ul>
        <li>Data loss or corruption</li>
        <li>Service interruptions</li>
        <li>Lost business or revenue</li>
        <li>Damages resulting from service use</li>
    </ul>

    <h2>11. Changes to Terms</h2>
    <p>We reserve the right to modify these terms at any time. Continued use of the service constitutes acceptance of modified terms.</p>

    <h2>12. Contact Information</h2>
    <p>For questions about these terms, please contact us at:</p>
    <p>Email:hi@bixa.app</p>

    <p><strong>Last updated: <?= date('F d, Y') ?></strong></p>
</div>                                    </div>
                                </div>

                                <!-- Privacy Policy -->
                                <div class="tab-pane fade" id="privacy" role="tabpanel">
                                    <div class="scroll-content legal-content">
                                        <h2>Privacy Policy</h2>
                                        <p>This Privacy Policy describes how Bixa collects and uses your information.</p>

                                        <h2>1. Information We Collect</h2>
                                        <p><strong>We collect the following information:</strong></p>
                                        <ul>
                                            <li>Account information (name, email, password)</li>
                                            <li>Usage data and statistics</li>
                                            <li>IP addresses and browser information</li>
                                            <li>Cookies and similar technologies</li>
                                            <li>Communication records</li>
                                        </ul>

                                        <h2>2. How We Use Your Information</h2>
                                        <p><strong>We use your information to:</strong></p>
                                        <ul>
                                            <li>Provide and maintain our services</li>
                                            <li>Process your transactions</li>
                                            <li>Send service updates and notifications</li>
                                            <li>Detect and prevent fraud</li>
                                            <li>Improve our services</li>
                                        </ul>

                                        <h2>3. Data Storage and Security</h2>
                                        <ul>
                                            <li>We employ industry-standard security measures</li>
                                            <li>Data is stored on secure servers</li>
                                            <li>Regular security audits are performed</li>
                                            <li>Access to personal data is restricted</li>
                                        </ul>

                                        <h2>4. Data Sharing</h2>
                                        <p>We may share your information with:</p>
                                        <ul>
                                            <li>Service providers and partners</li>
                                            <li>Law enforcement when required</li>
                                            <li>Third parties with your consent</li>
                                        </ul>

                                        <h2>5. Your Rights</h2>
                                        <p>You have the right to:</p>
                                        <ul>
                                            <li>Access your personal data</li>
                                            <li>Correct inaccurate data</li>
                                            <li>Request data deletion</li>
                                            <li>Opt-out of communications</li>
                                            <li>File a complaint</li>
                                        </ul>

                                        <h2>6. Cookies Policy</h2>
                                        <p>We use cookies to:</p>
                                        <ul>
                                            <li>Remember your preferences</li>
                                            <li>Analyze site traffic</li>
                                            <li>Provide secure authentication</li>
                                            <li>Improve user experience</li>
                                        </ul>

                                        <h2>7. Contact Us</h2>
                                        <p>For privacy concerns, contact us at:</p>
                                        <p>Email: hi@bixa.app</p>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
