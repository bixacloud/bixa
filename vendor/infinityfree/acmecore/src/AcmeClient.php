<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore;

use InfinityFree\AcmeCore\Exception\AcmeCoreClientException;
use InfinityFree\AcmeCore\Exception\AcmeCoreServerException;
use InfinityFree\AcmeCore\Exception\Protocol\CertificateRequestFailedException;
use InfinityFree\AcmeCore\Exception\Protocol\CertificateRevocationException;
use InfinityFree\AcmeCore\Exception\Protocol\ChallengeNotSupportedException;
use InfinityFree\AcmeCore\Http\SecureHttpClient;
use InfinityFree\AcmeCore\Protocol\AuthorizationChallenge;
use InfinityFree\AcmeCore\Protocol\CertificateOrder;
use InfinityFree\AcmeCore\Protocol\ExternalAccount;
use InfinityFree\AcmeCore\Protocol\ResourcesDirectory;
use InfinityFree\AcmeCore\Protocol\RevocationReason;
use AcmePhp\Ssl\Certificate;
use AcmePhp\Ssl\CertificateRequest;
use AcmePhp\Ssl\Signer\CertificateRequestSigner;
use GuzzleHttp\Psr7\Utils;
use Webmozart\Assert\Assert;

/**
 * ACME protocol client implementation.
 *
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class AcmeClient implements AcmeClientInterface
{
    /**
     * @var SecureHttpClient
     */
    private $uninitializedHttpClient;

    /**
     * @var SecureHttpClient
     */
    private $initializedHttpClient;

    /**
     * @var CertificateRequestSigner
     */
    private $csrSigner;

    /**
     * @var string
     */
    private $directoryUrl;

    /**
     * @var ResourcesDirectory
     */
    private $directory;

    /**
     * @var string
     */
    private $account;

    public function __construct(SecureHttpClient $httpClient, string $directoryUrl, CertificateRequestSigner $csrSigner = null)
    {
        $this->uninitializedHttpClient = $httpClient;
        $this->directoryUrl = $directoryUrl;
        $this->csrSigner = $csrSigner ?: new CertificateRequestSigner();
    }

    /**
     * {@inheritdoc}
     */
    public function registerAccount(string $email = null, ExternalAccount $externalAccount = null): array
    {
        $client = $this->getHttpClient();

        $payload = [
            'termsOfServiceAgreed' => true,
            'contact' => [],
        ];

        if ($email) {
            $payload['contact'][] = 'mailto:'.$email;
        }

        if ($externalAccount) {
            $payload['externalAccountBinding'] = $client->createExternalAccountPayload(
                $externalAccount,
                $this->getResourceUrl(ResourcesDirectory::NEW_ACCOUNT)
            );
        }

        $this->requestResource('POST', ResourcesDirectory::NEW_ACCOUNT, $payload);
        $account = $this->getResourceAccount();

        return $client->request('POST', $account, $client->signKidPayload($account, $account, null));
    }

    /**
     * {@inheritdoc}
     */
    public function requestOrder(array $domains): CertificateOrder
    {
        Assert::allStringNotEmpty($domains, 'requestOrder::$domains expected a list of strings. Got: %s');

        $payload = [
            'identifiers' => array_map(
                static function ($domain) {
                    return [
                        'type' => 'dns',
                        'value' => $domain,
                    ];
                },
                array_values($domains)
            ),
        ];

        $client = $this->getHttpClient();
        $resourceUrl = $this->getResourceUrl(ResourcesDirectory::NEW_ORDER);
        $response = $client->request('POST', $resourceUrl, $client->signKidPayload($resourceUrl, $this->getResourceAccount(), $payload));

        return $this->createCertificateOrder($response, $client->getLastLocation());
    }

    /**
     * {@inheritdoc}
     */
    public function reloadOrder(CertificateOrder $order): CertificateOrder
    {
        $client = $this->getHttpClient();
        $orderEndpoint = $order->getOrderEndpoint();
        $response = $client->request('POST', $orderEndpoint, $client->signKidPayload($orderEndpoint, $this->getResourceAccount(), null));

        return $this->createCertificateOrder($response, $orderEndpoint);
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeOrder(CertificateOrder $order, CertificateRequest $csr): CertificateOrder
    {
        $client = $this->getHttpClient();
        $orderEndpoint = $order->getOrderEndpoint();
        $response = $client->request('POST', $orderEndpoint, $client->signKidPayload($orderEndpoint, $this->getResourceAccount(), null));
        if (\in_array($response['status'], ['pending', 'processing', 'ready'])) {
            $humanText = ['-----BEGIN CERTIFICATE REQUEST-----', '-----END CERTIFICATE REQUEST-----'];

            $csrContent = $this->csrSigner->signCertificateRequest($csr);
            $csrContent = trim(str_replace($humanText, '', $csrContent));
            $csrContent = trim($client->getBase64Encoder()->encode(base64_decode($csrContent)));

            $response = $client->request('POST', $response['finalize'], $client->signKidPayload($response['finalize'], $this->getResourceAccount(), ['csr' => $csrContent]));
        }

        return new CertificateOrder([], $orderEndpoint, $response['status']);
    }

    public function retrieveCertificate(CertificateOrder $order, bool $returnAlternateCertificateIfAvailable = false): Certificate
    {
        $client = $this->getHttpClient();
        $orderEndpoint = $order->getOrderEndpoint();
        $response = $client->request('POST', $orderEndpoint, $client->signKidPayload($orderEndpoint, $this->getResourceAccount(), null));

        if ('valid' !== $response['status']) {
            throw new CertificateRequestFailedException('The order has not been validated');
        }

        $response = $client->rawRequest('POST', $response['certificate'], $client->signKidPayload($response['certificate'], $this->getResourceAccount(), null));
        $responseHeaders = $response->getHeaders();

        if ($returnAlternateCertificateIfAvailable && isset($responseHeaders['Link'][1])) {
            $matches = [];
            preg_match('/<(http.*)>;rel="alternate"/', $responseHeaders['Link'][1], $matches);

            // If response headers include a valid alternate certificate link, return that certificate instead
            if (isset($matches[1])) {
                return $this->createCertificateResponse(
                    $client->request('POST', $matches[1], $client->signKidPayload($matches[1], $this->getResourceAccount(), null), false)
                );
            }
        }

        return $this->createCertificateResponse(Utils::copyToString($response->getBody()));
    }

    /**
     * {@inheritdoc}
     */
    public function reloadAuthorization(AuthorizationChallenge $challenge): AuthorizationChallenge
    {
        $client = $this->getHttpClient();
        $challengeUrl = $challenge->getUrl();
        $response = (array) $client->request('POST', $challengeUrl, $client->signKidPayload($challengeUrl, $this->getResourceAccount(), null));

        return $this->createAuthorizationChallenge($challenge->getDomain(), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function challengeAuthorization(AuthorizationChallenge $challenge): AuthorizationChallenge
    {
        $client = $this->getHttpClient();
        $challengeUrl = $challenge->getUrl();
        $response = (array) $client->request('POST', $challengeUrl, $client->signKidPayload($challengeUrl, $this->getResourceAccount(), []));
        return $this->createAuthorizationChallenge($challenge->getDomain(), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function revokeCertificate(Certificate $certificate, RevocationReason $revocationReason = null)
    {
        if (!$endpoint = $this->getResourceUrl(ResourcesDirectory::REVOKE_CERT)) {
            throw new CertificateRevocationException('This ACME server does not support certificate revocation.');
        }

        if (null === $revocationReason) {
            $revocationReason = RevocationReason::createDefaultReason();
        }

        openssl_x509_export(openssl_x509_read($certificate->getPEM()), $formattedPem);

        $formattedPem = str_ireplace('-----BEGIN CERTIFICATE-----', '', $formattedPem);
        $formattedPem = str_ireplace('-----END CERTIFICATE-----', '', $formattedPem);
        $client = $this->getHttpClient();
        $formattedPem = $client->getBase64Encoder()->encode(base64_decode(trim($formattedPem)));

        try {
            $client->request(
                'POST',
                $endpoint,
                $client->signKidPayload($endpoint, $this->getResourceAccount(), ['certificate' => $formattedPem, 'reason' => $revocationReason->getReasonType()]),
                false
            );
        } catch (AcmeCoreServerException $e) {
            throw new CertificateRevocationException($e->getMessage(), $e);
        } catch (AcmeCoreClientException $e) {
            throw new CertificateRevocationException($e->getMessage(), $e);
        }
    }

    /**
     * Find a resource URL from the Certificate Authority.
     */
    public function getResourceUrl(string $resource): string
    {
        if (!$this->directory) {
            $this->directory = new ResourcesDirectory(
                $this->getHttpClient()->request('GET', $this->directoryUrl)
            );
        }

        return $this->directory->getResourceUrl($resource);
    }

    /**
     * Request a resource (URL is found using ACME server directory).
     *
     * @throws AcmeCoreServerException when the ACME server returns an error HTTP status code
     * @throws AcmeCoreClientException when an error occured during response parsing
     *
     * @return array|string
     */
    protected function requestResource(string $method, string $resource, array $payload, bool $returnJson = true)
    {
        $client = $this->getHttpClient();
        $endpoint = $this->getResourceUrl($resource);

        return $client->request(
            $method,
            $endpoint,
            $client->signJwkPayload($endpoint, $payload),
            $returnJson
        );
    }

    private function createCertificateOrder(array $response, string $orderEndpoint): CertificateOrder
    {
        if (!isset($response['authorizations']) || !$response['authorizations']) {
            throw new ChallengeNotSupportedException();
        }

        $client = $this->getHttpClient();

        $authorizationsChallenges = [];
        foreach ($response['authorizations'] as $authorizationEndpoint) {
            $authorizationsResponse = $client->request('POST', $authorizationEndpoint, $client->signKidPayload($authorizationEndpoint, $this->getResourceAccount(), null));
            $domain = (empty($authorizationsResponse['wildcard']) ? '' : '*.').$authorizationsResponse['identifier']['value'];
            foreach ($authorizationsResponse['challenges'] as $challenge) {
                $authorizationsChallenges[$domain][] = $this->createAuthorizationChallenge($authorizationsResponse['identifier']['value'], $challenge);
            }
        }

        return new CertificateOrder($authorizationsChallenges, $orderEndpoint, $response['status']);
    }

    private function createCertificateResponse(string $certificate): Certificate
    {
        $certificateHeader = '-----BEGIN CERTIFICATE-----';
        $certificatesChain = null;

        foreach (array_reverse(explode($certificateHeader, $certificate)) as $pem) {
            if ('' !== \trim($pem)) {
                $certificatesChain = new Certificate($certificateHeader.$pem, $certificatesChain);
            }
        }

        return $certificatesChain;
    }

    private function getResourceAccount(): string
    {
        if (!$this->account) {
            $payload = [
                'onlyReturnExisting' => true,
            ];

            $this->requestResource('POST', ResourcesDirectory::NEW_ACCOUNT, $payload);
            $this->account = $this->getHttpClient()->getLastLocation();
        }

        return $this->account;
    }

    private function createAuthorizationChallenge($domain, array $response): AuthorizationChallenge
    {
        $base64encoder = $this->getHttpClient()->getBase64Encoder();

        return new AuthorizationChallenge(
            $domain,
            $response['status'],
            $response['type'],
            $response['url'],
            $response['token'],
            $response['token'].'.'.$base64encoder->encode($this->getHttpClient()->getJWKThumbprint()),
            $response['error'] ?? []
        );
    }

    private function getHttpClient(): SecureHttpClient
    {
        if (!$this->initializedHttpClient) {
            $this->initializedHttpClient = $this->uninitializedHttpClient;
            $this->initializedHttpClient->setNonceEndpoint($this->getResourceUrl(ResourcesDirectory::NEW_NONCE));
        }

        return $this->initializedHttpClient;
    }
}
