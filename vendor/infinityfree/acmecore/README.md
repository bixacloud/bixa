# AcmeCore PHP Library

AcmeCore is a modified version of the [Acme PHP Core](https://github.com/acmephp/core) library.

## When to use AcmeCore

AcmeCore is designed as a straight forward implementation of the [Let's Encrypt/ACME protocol](https://github.com/letsencrypt/acme-spec) following best practices for libraries. There are no file system dependencies, integrated schedulers or anything like that. You can integrate it in your own project and take care of scheduling and persistence yourself.

## Differences with Acme PHP Core

Acme PHP Core is a great library, but assumes that the "happy path" always works. I.e. the CA never returns an error, performs all tasks quickly, and always returns the expected data. With Let's Encrypt this is generally true, but other CAs might be less stable.

The key differences between this library and Acme PHP Core are the following:

- **Every function on `AcmeClient` maps to a single step in the ACME process.** This way, you're free to call and retry the steps at your own pace (e.g. retrying receiving a certificate without calling finalize again).
- **No more sleep loops.** Schedule tasks the way you want, and don't hog a PHP process if you don't want to.
- **`CertificateOrder`s now contain the status of the order.** Load the order with the `reloadOrder` function, see the current status of the order and choose the next step to apply.

## Documentation

The official [Acme PHP documentation](https://acmephp.github.io) still applies for the most part. But the certificate issuance process has been changed a bit.

```php
$secureHttpClientFactory = new SecureHttpClientFactory(
    new GuzzleHttpClient(),
    new Base64SafeEncoder(),
    new KeyParser(),
    new DataSigner(),
    new ServerErrorHandler()
);

// $accountKeyPair instance of KeyPair
$secureHttpClient = $secureHttpClientFactory->createSecureHttpClient($accountKeyPair);

// Important, change to production LE directory for real certs!
$acmeClient = new AcmeClient($secureHttpClient, 'https://acme-staging-v02.api.letsencrypt.org/directory');

// Request a certificate for mydomain.com.
$certificateOrder = $acmeClient->requestOrder('mydomain.com');

// Retrieve the challenges to complete for mydomain.com.
$challenges = $certificateOrder->getAuthorizationChallenges('mydomain.com');

// Now complete the challenge for the domain.
// Find the challenge object for the verification type you want to do, e.g. http-01, dns-01.
$challenge = $challenges[0];

// Ask the CA to confirm the authorization.
$challenge = $acmeClient->challengeAuthorization($dnsChallenge);

// Wait for the CA to complete the authorization.
// This example uses a sleep loop, but you can schedule your own.
while ($challenge->getStatus() != 'ready') {
    sleep(1);
    
    $challenge = $acmeClient->reloadAuthorization($challenge);
}

// Prepare the CSR
$dn = new DistinguishedName('mydomain.com');
$keyPairGenerator = new KeyPairGenerator();
// Make a new key pair. We'll keep the private key as our cert key
$domainKeyPair = $keyPairGenerator->generateKeyPair();

// This is the private key
echo $domainKeyPair->getPrivateKey()->getPem());

// Generate CSR
$csr = new CertificateRequest($dn, $domainKeyPair);

// Tell the CA to generate the certificate.
$certificateOrder = $acmeClient->finalizeOrder($certificateOrder, $csr);

// Wait for the CA to complete the issuance.
// This example uses a sleep loop, but you can schedule your own.
while ($certificateOrder->getStatus() != 'issued') {
    sleep(1);
    
    $certificateOrder = $acmeClient->reloadOrder($certificateOrder->getOrderEndpoint());
}

// Retrieve the generated certificate.
$certificate = $acmeClient->retrieveCertificate($certificateOrder);

// This is the generated certificate.
echo $certificate->getPem();
```

## Launch the Test suite

The Acme PHP test suite is located in the main repository:
[https://github.com/acmephp/acmephp#launch-the-test-suite](https://github.com/acmephp/acmephp#launch-the-test-suite).
