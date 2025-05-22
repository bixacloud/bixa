<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\HostingAccount;
use App\Services\CloudflareService;
use App\Services\MofhService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Afosto\Acme\Client;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\User\ExtendedAcmeClient;

class SSLController extends Controller
{
    protected $acmeClient;
    protected $cloudflareService;
    protected $mofhService;
    protected $notificationService;

    public function __construct(CloudflareService $cloudflareService, MofhService $mofhService, NotificationService $notificationService)
    {
        $this->cloudflareService = $cloudflareService;
        $this->mofhService = $mofhService;
        $this->notificationService = $notificationService;
        
        $adapter = new LocalFilesystemAdapter(storage_path('app/acme'));
        $filesystem = new Filesystem($adapter);

        $this->acmeClient = new ExtendedAcmeClient([
            'username' => config('services.acme.email'),
            'fs' => $filesystem,
            'mode' => config('services.acme.mode') === 'live' ? Client::MODE_LIVE : Client::MODE_STAGING,
        ]);
    }

    public function index()
    {
        $certificates = Certificate::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('ssl.index', compact('certificates'));
    }

    public function create()
    {
        return view('ssl.create');
    }

    protected function isSubdomain($domain)
    {
        $parts = explode('.', $domain);
        return count($parts) > 2;
    }

    protected function getDomainFromString($domain) 
    {
        $parts = explode('.', $domain);
        if (count($parts) > 2) {
            return implode('.', array_slice($parts, -2));
        }
        return $domain;
    }

    protected function getSubdomainPrefix($domain)
    {
        $parts = explode('.', $domain);
        if (count($parts) > 2) {
            return $parts[0];
        }
        return null;
    }

    protected function checkHostingDomain($domain)
    {
        $hosting = HostingAccount::where('user_id', auth()->id())
            ->where('status', 'active')
            ->get();

        foreach ($hosting as $account) {
            // Check main domain
            if ($account->domain === $this->getDomainFromString($domain)) {
                return $account;
            }

            // Check in domain list
            $domains = $this->mofhService->getDomains($account->username);
            foreach ($domains as $hostingDomain) {
                if ($hostingDomain === $domain) {
                    return $account;
                }
            }
        }

        return null;
    }

  public function store(Request $request)
{
    $request->validate([
        'domain' => 'required|string|max:255',
        'type' => 'required|in:letsencrypt,zerossl,googletrust',
    ]);

    // Check if a certificate for this domain was created within the last 90 days
    $existingCertificate = Certificate::where('domain', $request->domain)
        ->where(function($query) {
            // Include certificates that are active, pending, or were created within 90 days
            $query->where('status', 'active')
                ->orWhere('status', 'pending')
                ->orWhere(function($q) {
                    $q->where('created_at', '>=', now()->subDays(90));
                });
        })
        ->first();

    if ($existingCertificate) {
        return redirect()
            ->back()
            ->with('error', 'A certificate for this domain was already created within the last 90 days. Please wait before requesting a new one.')
            ->withInput();
    }

    try {
        // Create order for domain
        $order = $this->acmeClient->createOrder([$request->domain]);
        $authorizations = $this->acmeClient->authorize($order);
        $authorization = $authorizations[0];
        $txtRecord = $authorization->getTxtRecord();

        // Check hosting account
        $hostingAccount = $this->checkHostingDomain($request->domain);
        
        if ($hostingAccount && $this->cloudflareService->isConfigured()) {
            // Use CNAME with Cloudflare proxy
            $proxyDomain = $this->cloudflareService->getProxyDomain();
            $recordName = '_acme-challenge.' . $request->domain;

            // Create TXT record on Cloudflare
            $cloudflareRecord = $this->cloudflareService->createTxtRecord(
                $recordName, // Send full domain for service to process record name
                $txtRecord->getValue(),
                $request->domain // Send domain for service to identify subdomain
            );

            $dnsValidation = [
                'record_type' => 'CNAME',
                'record_name' => $recordName,
                'record_content' => $cloudflareRecord['name'], // Use record name from Cloudflare
                'cloudflare_record_id' => $cloudflareRecord['id'],
                'cloudflare_zone_id' => $cloudflareRecord['zone_id'],
                'txt_content' => $txtRecord->getValue(),
                'use_proxy' => true,
                'proxy_domain' => $proxyDomain
            ];

            Log::info('Created Cloudflare record', [
                'domain' => $request->domain,
                'record_name' => $recordName,
                'points_to' => $cloudflareRecord['name']
            ]);

            $message = 'Please add CNAME record to your domain: ' . $recordName . ' => ' . $cloudflareRecord['name'];
        } else {
            // Use TXT record directly
            $dnsValidation = [
                'record_type' => 'TXT',
                'record_name' => $txtRecord->getName(),
                'record_content' => $txtRecord->getValue(),
                'use_proxy' => false
            ];

            $message = 'Please add TXT record to your domain: ' . $txtRecord->getName() . ' with value: ' . $txtRecord->getValue();
        }

        $certificate = Certificate::create([
            'user_id' => auth()->id(),
            'domain' => $request->domain,
            'type' => $request->type,
            'status' => 'pending',
            'order_id' => $order->getId(),
            'dns_validation' => $dnsValidation,
        ]);

        // Create SSL notification
        $this->notificationService->createSSLNotification(
            auth()->user(), 
            'created', 
            [
                'domain' => $request->domain,
                'certificate_id' => $certificate->id
            ]
        );

        Log::info('SSL certificate creation initiated', [
            'domain' => $request->domain,
            'validation' => $dnsValidation
        ]);

        return redirect()
            ->route('ssl.show', $certificate)
            ->with('success', $message);

    } catch (\Exception $e) {
        Log::error('Certificate creation failed', [
            'domain' => $request->domain,
            'error' => $e->getMessage()
        ]);
        
        return redirect()
            ->back()
            ->with('error', 'Failed to create certificate: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show($id)
    {
        $certificate = Certificate::where('user_id', auth()->id())
            ->findOrFail($id);

        return view('ssl.show', compact('certificate'));
    }

    public function checkDns($id)
    {
        try {
            $certificate = Certificate::where('user_id', auth()->id())
                ->findOrFail($id);
            
            if ($certificate->dns_validation['use_proxy']) {
                // Check CNAME record
                $records = dns_get_record($certificate->dns_validation['record_name'], DNS_CNAME);
                $foundRecords = [];
                
                if ($records) {
                    foreach ($records as $record) {
                        $foundRecords[] = $record['target'];
                    }
                }

                $isValid = in_array($certificate->dns_validation['record_content'], $foundRecords);

                if ($isValid) {
                    // If CNAME is correct, check TXT record on proxy domain
                    $txtRecords = dns_get_record($certificate->dns_validation['record_content'], DNS_TXT);
                    if ($txtRecords) {
                        foreach ($txtRecords as $record) {
                            if ($record['txt'] === $certificate->dns_validation['txt_content']) {
                                return response()->json([
                                    'success' => true,
                                    'isValid' => true,
                                    'message' => 'DNS verification successful',
                                    'expected' => $certificate->dns_validation['record_content'],
                                    'found' => $foundRecords
                                ]);
                            }
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'isValid' => false,
                    'message' => 'DNS record not found or not propagated yet',
                    'expected' => $certificate->dns_validation['record_content'],
                    'found' => $foundRecords
                ]);

            } else {
                // Check TXT record
                $records = dns_get_record($certificate->dns_validation['record_name'], DNS_TXT);
                $foundRecords = [];
                
                if ($records) {
                    foreach ($records as $record) {
                        $foundRecords[] = $record['txt'];
                    }
                }

                $isValid = in_array($certificate->dns_validation['record_content'], $foundRecords);

                return response()->json([
                    'success' => true,
                    'isValid' => $isValid,
                    'message' => $isValid ? 'DNS verification successful' : 'DNS record not found or not propagated yet',
                    'expected' => $certificate->dns_validation['record_content'],
                    'found' => $foundRecords
                ]);
            }

        } catch (\Exception $e) {
            Log::error('DNS check failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error checking DNS: ' . $e->getMessage()
            ], 422);
        }
    }

    public function verify($id)
{
    try {
        $certificate = Certificate::where('user_id', auth()->id())
            ->findOrFail($id);

        // Delete old TXT record if using proxy
        if (!empty($certificate->dns_validation['use_proxy']) && 
            !empty($certificate->dns_validation['cloudflare_record_id'])) {
            $this->cloudflareService->deleteTxtRecord(
                $certificate->dns_validation['cloudflare_record_id'],
                $certificate->dns_validation['cloudflare_zone_id']
            );
        }

        // Create new order
        $order = $this->acmeClient->createOrder([$certificate->domain]);
        $authorizations = $this->acmeClient->authorize($order);
        $authorization = $authorizations[0];
        $txtRecord = $authorization->getTxtRecord();

        // Check if domain exists in hosting
        $hostingAccount = $this->checkHostingDomain($certificate->domain);

        if ($hostingAccount && $this->cloudflareService->isConfigured()) {
            // Use CNAME with Cloudflare proxy
            $proxyDomain = $this->cloudflareService->getProxyDomain();
            $recordName = '_acme-challenge.' . $certificate->domain;

            // Create new TXT record on Cloudflare
            $cloudflareRecord = $this->cloudflareService->createTxtRecord(
                $recordName,
                $txtRecord->getValue(),
                $certificate->domain // Pass full domain for proper record name generation
            );

            $dnsValidation = [
                'record_type' => 'CNAME',
                'record_name' => $recordName,
                'record_content' => $cloudflareRecord['name'], // Use Cloudflare record name
                'cloudflare_record_id' => $cloudflareRecord['id'],
                'cloudflare_zone_id' => $cloudflareRecord['zone_id'],
                'txt_content' => $txtRecord->getValue(),
                'use_proxy' => true,
                'proxy_domain' => $proxyDomain,
                'hosting_username' => $hostingAccount->username
            ];

            Log::info('New DNS verification with Cloudflare proxy', [
                'domain' => $certificate->domain,
                'record_name' => $recordName,
                'points_to' => $cloudflareRecord['name']
            ]);

            $message = 'New DNS record has been generated. Please wait a few minutes for DNS propagation.';
        } else {
            // Use TXT record directly
            $dnsValidation = [
                'record_type' => 'TXT',
                'record_name' => $txtRecord->getName(),
                'record_content' => $txtRecord->getValue(),
                'use_proxy' => false
            ];

            Log::info('New DNS verification with direct TXT record', [
                'domain' => $certificate->domain,
                'record_name' => $txtRecord->getName()
            ]);

            $message = 'New TXT record has been generated. Please update your DNS settings.';
        }

        // Update certificate with new information
        $certificate->update([
            'dns_validation' => $dnsValidation,
            'order_id' => $order->getId()
        ]);

        return redirect()
            ->back()
            ->with('info', $message);

    } catch (\Exception $e) {
        Log::error('SSL verification failed', [
            'domain' => $certificate->domain ?? null,
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->back()
            ->with('error', 'Verification failed: ' . $e->getMessage());
    }
}

public function challengeValidate($id)
{
    try {
        $certificate = Certificate::where('user_id', auth()->id())
            ->findOrFail($id);

        // Check DNS records before validating
        if ($certificate->dns_validation['use_proxy']) {
            // Check CNAME record
            $records = dns_get_record($certificate->dns_validation['record_name'], DNS_CNAME);
            if (empty($records)) {
                throw new \Exception('CNAME record not found. Please check your DNS configuration.');
            }

            $cnameFound = false;
            foreach ($records as $record) {
                if ($record['target'] === $certificate->dns_validation['record_content']) {
                    $cnameFound = true;
                    break;
                }
            }

            if (!$cnameFound) {
                throw new \Exception('CNAME record does not match expected value.');
            }

            // Check TXT record on proxy domain
            $txtRecords = dns_get_record($certificate->dns_validation['record_content'], DNS_TXT);
            if (empty($txtRecords)) {
                throw new \Exception('TXT record not found on proxy domain.');
            }

            $txtFound = false;
            foreach ($txtRecords as $record) {
                if ($record['txt'] === $certificate->dns_validation['txt_content']) {
                    $txtFound = true;
                    break;
                }
            }

            if (!$txtFound) {
                throw new \Exception('TXT record on proxy domain does not match expected value.');
            }
        }

        // Add delay for DNS propagation
        sleep(10); // Increase wait time

        // Get and validate order
        $order = $this->acmeClient->getOrder($certificate->order_id);
        $authorizations = $this->acmeClient->authorize($order);
        $authorization = $authorizations[0];

        // Validate challenge
        $challenge = $authorization->getDnsChallenge();
        if (!$this->acmeClient->validate($challenge)) {
            throw new \Exception('Challenge validation failed. Please wait a few minutes and try again.');
        }

        // Add delay after validation
        sleep(10);

        try {
            $cert = $this->acmeClient->getCertificate($order);
            
            if (!$cert) {
                throw new \Exception('Failed to obtain certificate');
            }

            // Delete TXT record if using proxy
            if ($certificate->dns_validation['use_proxy']) {
                if (!empty($certificate->dns_validation['cloudflare_record_id'])) {
                    $this->cloudflareService->deleteTxtRecord(
                        $certificate->dns_validation['cloudflare_record_id'],
                        $certificate->dns_validation['cloudflare_zone_id']
                    );
                }
            }

            // Update certificate
            $certificate->update([
                'status' => 'active',
                'private_key' => $cert->getPrivateKey(),
                'certificate' => $cert->getCertificate(false),
                'ca_certificate' => $cert->getIntermediate(),
                'csr' => $cert->getCsr(),
                'valid_until' => $cert->getExpiryDate(),
            ]);

            // Create activated notification
            $this->notificationService->createSSLNotification(
                auth()->user(), 
                'activated', 
                [
                    'domain' => $certificate->domain,
                    'certificate_id' => $certificate->id
                ]
            );

            return redirect()
                ->route('ssl.show', $certificate)
                ->with('success', 'SSL certificate has been issued successfully.');

        } catch (\Exception $e) {
            throw new \Exception('Failed to generate certificate: ' . $e->getMessage());
        }

    } catch (\Exception $e) {
        Log::error('Challenge validation failed', [
            'domain' => $certificate->domain ?? null,
            'error' => $e->getMessage(),
            'dns_validation' => $certificate->dns_validation ?? null
        ]);
        
        return redirect()
            ->back()
            ->with('error', $e->getMessage());
    }
}

    public function revoke($id)
    {
        try {
            $certificate = Certificate::where('user_id', auth()->id())
                ->findOrFail($id);

            // Delete TXT record on Cloudflare if using proxy
            if ($certificate->dns_validation['use_proxy']) {
                if (!empty($certificate->dns_validation['cloudflare_record_id'])) {
                    $this->cloudflareService->deleteTxtRecord(
                        $certificate->dns_validation['cloudflare_record_id'],
                        $certificate->dns_validation['cloudflare_zone_id']
                    );
                }
            }

            $certificate->update([
                'status' => 'revoked',
                'revoked_at' => now(),
            ]);

            // Create revoked notification
            $this->notificationService->createSSLNotification(
                auth()->user(), 
                'revoked', 
                [
                    'domain' => $certificate->domain,
                    'certificate_id' => $certificate->id
                ]
            );

            Log::info('SSL certificate revoked', [
                'domain' => $certificate->domain
            ]);

            return redirect()
                ->route('ssl.index')
                ->with('success', 'SSL certificate has been revoked successfully.');

        } catch (\Exception $e) {
            Log::error('Certificate revocation failed', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to revoke certificate: ' . $e->getMessage());
        }
    }
}