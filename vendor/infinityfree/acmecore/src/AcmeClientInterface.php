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
use InfinityFree\AcmeCore\Exception\Protocol\CertificateRequestTimedOutException;
use InfinityFree\AcmeCore\Exception\Protocol\CertificateRevocationException;
use InfinityFree\AcmeCore\Exception\Protocol\ChallengeFailedException;
use InfinityFree\AcmeCore\Exception\Protocol\ChallengeNotSupportedException;
use InfinityFree\AcmeCore\Exception\Protocol\ChallengeTimedOutException;
use InfinityFree\AcmeCore\Protocol\AuthorizationChallenge;
use InfinityFree\AcmeCore\Protocol\CertificateOrder;
use InfinityFree\AcmeCore\Protocol\ExternalAccount;
use InfinityFree\AcmeCore\Protocol\RevocationReason;
use AcmePhp\Ssl\Certificate;
use AcmePhp\Ssl\CertificateRequest;
use AcmePhp\Ssl\CertificateResponse;

/**
 * ACME protocol client interface.
 *
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
interface AcmeClientInterface
{
    /**
     * Register the local account KeyPair in the Certificate Authority.
     *
     * @param string|null          $email           an optionnal e-mail to associate with the account
     * @param ExternalAccount|null $externalAccount an optionnal External Account to use for External Account Binding
     *
     * @throws AcmeCoreServerException when the ACME server returns an error HTTP status code
     *                                 (the exception will be more specific if detail is provided)
     * @throws AcmeCoreClientException when an error occured during response parsing
     *
     * @return array the Certificate Authority response decoded from JSON into an array
     */
    public function registerAccount(string $email = null, ExternalAccount $externalAccount = null): array;

    /**
     * Request authorization challenge data for a list of domains.
     *
     * An AuthorizationChallenge is an association between a URI, a token and a payload.
     * The Certificate Authority will create this challenge data and you will then have
     * to expose the payload for the verification (see challengeAuthorization).
     *
     * @param string[] $domains the domains to challenge
     *
     * @throws AcmeCoreServerException        when the ACME server returns an error HTTP status code
     *                                        (the exception will be more specific if detail is provided)
     * @throws AcmeCoreClientException        when an error occured during response parsing
     * @throws ChallengeNotSupportedException when the HTTP challenge is not supported by the server
     *
     * @return CertificateOrder the Order returned by the Certificate Authority
     */
    public function requestOrder(array $domains): CertificateOrder;

    /**
     * Retrieve the latest information of a certificate order.
     *
     * Retrieve the information of a certificate order given the order endpoint of an order that was created before.
     * This way, you can see the current status of the order and the challenges to determine the next step to take.
     *
     * @param CertificateOrder $order a CertificateOrder object containing an orderEndpoint.
     * @return CertificateOrder the Order returned by the Certificate Authority
     */
    public function reloadOrder(CertificateOrder $order): CertificateOrder;

    /**
     * Request a certificate for the given domain.
     *
     * This method should be called only if a previous authorization challenge has been successful for the asked domain.
     * In most cases, the certificate status should be "ready".
     *
     * @param CertificateOrder $order the Order returned by the Certificate Authority
     * @param CertificateRequest $csr the Certificate Signing Request (informations for the certificate)
     *
     * @throws AcmeCoreServerException when the ACME server returns an error HTTP status code
     *                                  (the exception will be more specific if detail is provided)
     * @throws AcmeCoreClientException  when an error occured during response parsing
     *
     * @return CertificateOrder an updated Order from the Certificate Authority with the current status.
     */
    public function finalizeOrder(CertificateOrder $order, CertificateRequest $csr): CertificateOrder;

    /**
     * Retrieve the issued certificate from the Certificate Authority.
     *
     * After the order has been finalized, you can retrieve the issued certificate from the CA.
     * The status of the order should be "issued".
     *
     * @param CertificateOrder $order
     * @param bool $returnAlternateCertificateIfAvailable whether the alternate certificate provided by
     *                                                    the CA should be returned instead of the main one.
     *                                                    This is especially useful following
     *                                                    https://letsencrypt.org/2019/04/15/transitioning-to-isrg-root.html.
     * @throws CertificateRequestFailedException   when the certificate request failed
     * @return Certificate the certificate data you can store somewhere.
     */
    public function retrieveCertificate(CertificateOrder $order, bool $returnAlternateCertificateIfAvailable = false): Certificate;

    /**
     * Request the current status of an authorization challenge.
     *
     * @param AuthorizationChallenge $challenge The challenge to request
     *
     * @return AuthorizationChallenge A new instance of the challenge
     */
    public function reloadAuthorization(AuthorizationChallenge $challenge): AuthorizationChallenge;

    /**
     * Ask the Certificate Authority to challenge a given authorization.
     *
     * This check will generally consists of requesting over HTTP the domain
     * at a specific URL. This URL should return the raw payload generated
     * by requestAuthorization.
     *
     * @param AuthorizationChallenge $challenge the challenge data to check
     *
     * @throws AcmeCoreServerException    when the ACME server returns an error HTTP status code
     *                                    (the exception will be more specific if detail is provided)
     * @throws AcmeCoreClientException    when an error occured during response parsing
     * @throws ChallengeTimedOutException when the challenge timed out
     * @throws ChallengeFailedException   when the challenge failed
     *
     * @return AuthorizationChallenge the updated challenge information.
     */
    public function challengeAuthorization(AuthorizationChallenge $challenge): AuthorizationChallenge;

    /**
     * Revoke a given certificate from the Certificate Authority.
     *
     * @throws CertificateRevocationException
     */
    public function revokeCertificate(Certificate $certificate, RevocationReason $revocationReason = null);
}
