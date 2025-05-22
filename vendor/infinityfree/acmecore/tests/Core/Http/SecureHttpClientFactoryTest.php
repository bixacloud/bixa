<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\AcmeCore\Core\Http;

use InfinityFree\AcmeCore\Http\Base64SafeEncoder;
use InfinityFree\AcmeCore\Http\SecureHttpClient;
use InfinityFree\AcmeCore\Http\SecureHttpClientFactory;
use InfinityFree\AcmeCore\Http\ServerErrorHandler;
use AcmePhp\Ssl\Generator\KeyPairGenerator;
use AcmePhp\Ssl\Parser\KeyParser;
use AcmePhp\Ssl\Signer\DataSigner;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class SecureHttpClientFactoryTest extends TestCase
{
    public function testCreateClient()
    {
        $keyPair = (new KeyPairGenerator())->generateKeyPair();
        $base64Encoder = new Base64SafeEncoder();
        $keyParser = new KeyParser();
        $dataSigner = new DataSigner();

        $factory = new SecureHttpClientFactory(
            new Client(),
            $base64Encoder,
            $keyParser,
            $dataSigner,
            new ServerErrorHandler()
        );

        $client = $factory->createSecureHttpClient($keyPair);

        $this->assertInstanceOf(SecureHttpClient::class, $client);
        $this->assertEquals($base64Encoder, $client->getBase64Encoder());
        $this->assertEquals($keyParser, $client->getKeyParser());
        $this->assertEquals($dataSigner, $client->getDataSigner());
        $this->assertEquals($keyPair, $client->getAccountKeyPair());
    }
}
