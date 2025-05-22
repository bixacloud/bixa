<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\AcmeCore\Core;

use InfinityFree\AcmeCore\AcmeClient;
use InfinityFree\AcmeCore\Challenge\Http\SimpleHttpSolver;
use InfinityFree\AcmeCore\Exception\Protocol\CertificateRevocationException;
use InfinityFree\AcmeCore\Http\Base64SafeEncoder;
use InfinityFree\AcmeCore\Http\SecureHttpClient;
use InfinityFree\AcmeCore\Http\ServerErrorHandler;
use InfinityFree\AcmeCore\Protocol\AuthorizationChallenge;
use InfinityFree\AcmeCore\Protocol\CertificateOrder;
use InfinityFree\AcmeCore\Protocol\ExternalAccount;
use AcmePhp\Ssl\Certificate;
use AcmePhp\Ssl\CertificateRequest;
use AcmePhp\Ssl\CertificateResponse;
use AcmePhp\Ssl\DistinguishedName;
use AcmePhp\Ssl\Generator\EcKey\EcKeyOption;
use AcmePhp\Ssl\Generator\KeyOption;
use AcmePhp\Ssl\Generator\KeyPairGenerator;
use AcmePhp\Ssl\Generator\RsaKey\RsaKeyOption;
use AcmePhp\Ssl\Parser\KeyParser;
use AcmePhp\Ssl\Signer\DataSigner;
use GuzzleHttp\Client;

class AcmeClientTest extends AbstractFunctionnalTest
{
    public function provideFullProcess()
    {
        yield 'rsa1024' => [new RsaKeyOption(1024), false];
        yield 'rsa1024-alternate' => [new RsaKeyOption(1024), true];
        yield 'rsa4098' => [new RsaKeyOption(4098), false];
        yield 'ecprime256v1' => [new EcKeyOption('prime256v1'), false];
        yield 'ecsecp384r1' => [new EcKeyOption('secp384r1'), false];
    }

    /**
     * @dataProvider provideFullProcess
     */
    public function testFullProcess(KeyOption $keyOption, bool $useAlternateCertificate)
    {
        $secureHttpClient = new SecureHttpClient(
            (new KeyPairGenerator())->generateKeyPair($keyOption),
            new Client(),
            new Base64SafeEncoder(),
            new KeyParser(),
            new DataSigner(),
            new ServerErrorHandler()
        );

        $client = new AcmeClient($secureHttpClient, 'https://localhost:14000/dir');

        /*
         * Register account
         */
        if ('eab' === getenv('PEBBLE_MODE')) {
            $data = $client->registerAccount('titouan.galopin@acmephp.com', new ExternalAccount('kid1', 'dGVzdGluZw'));
        } else {
            $data = $client->registerAccount('titouan.galopin@acmephp.com');
        }

        $this->assertIsArray($data);
        $this->assertArrayHasKey('key', $data);

        $solver = new SimpleHttpSolver();

        /*
         * Ask for domain challenge
         */
        $order = $client->requestOrder(['acmephp.com']);
        $this->assertEquals('pending', $order->getStatus());
        $challenges = $order->getAuthorizationChallenges('acmephp.com');
        foreach ($challenges as $challenge) {
            if ('http-01' === $challenge->getType()) {
                break;
            }
        }

        $this->assertInstanceOf(AuthorizationChallenge::class, $challenge);
        $this->assertEquals('acmephp.com', $challenge->getDomain());
        $this->assertStringContainsString('https://localhost:14000/chalZ/', $challenge->getUrl());

        $solver->solve($challenge);

        /*
         * Challenge check
         */
        $this->handleChallenge($challenge->getToken(), $challenge->getPayload());
        try {
            $check = $client->challengeAuthorization($challenge);
            sleep(1);
            $check = $client->reloadAuthorization($check);
            $this->assertEquals('valid', $check->getStatus());
        } finally {
            $this->cleanChallenge($challenge->getToken());
        }

        /**
         * Reload order, check if challenge was completed.
         */
        $updatedOrder = $client->reloadOrder($order);
        $this->assertEquals('ready', $updatedOrder->getStatus());
        $this->assertCount(1, $updatedOrder->getAuthorizationChallenges('acmephp.com'));
        $validatedChallenge = $updatedOrder->getAuthorizationChallenges('acmephp.com')[0];
        $this->assertEquals('valid', $validatedChallenge->getStatus());

        /*
         * Request certificate
         */
        $csr = new CertificateRequest(new DistinguishedName('acmephp.com'), (new KeyPairGenerator())->generateKeyPair($keyOption));
        $response = $client->finalizeOrder($order, $csr);
        $this->assertInstanceOf(CertificateOrder::class, $response);
        $this->assertEquals('processing', $response->getStatus());

        /**
         * Reload order, check if certificate was issued
         */
        sleep(1);
        $response = $client->reloadOrder($response);
        $this->assertEquals('valid', $response->getStatus());

        /**
         * Retrieve certificate
         */
        $certificate = $client->retrieveCertificate($order);
        $this->assertInstanceOf(Certificate::class, $certificate);

        /*
         * Revoke certificate
         *
         * ACME will not let you revoke the same cert twice so this test should pass both cases
         */
        try {
            $client->revokeCertificate($certificate);
        } catch (CertificateRevocationException $e) {
            $this->assertStringContainsString('Unable to find specified certificate', $e->getPrevious()->getPrevious()->getMessage());
        }
    }
}
