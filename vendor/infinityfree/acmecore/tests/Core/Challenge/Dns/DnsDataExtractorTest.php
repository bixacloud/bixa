<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\AcmeCore\Core\Challenge\Dns;

use InfinityFree\AcmeCore\Challenge\Dns\DnsDataExtractor;
use InfinityFree\AcmeCore\Http\Base64SafeEncoder;
use InfinityFree\AcmeCore\Protocol\AuthorizationChallenge;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DnsDataExtractorTest extends TestCase
{
    use ProphecyTrait;

    public function testGetRecordName()
    {
        $domain = 'foo.com';

        $mockEncoder = $this->prophesize(Base64SafeEncoder::class);
        $stubChallenge = $this->prophesize(AuthorizationChallenge::class);

        $extractor = new DnsDataExtractor($mockEncoder->reveal());

        $stubChallenge->getDomain()->willReturn($domain);

        $this->assertEquals('_acme-challenge.'.$domain.'.', $extractor->getRecordName($stubChallenge->reveal()));
    }

    public function testGetRecordValue()
    {
        $payload = 'randomPayload';
        $encodedPayload = 'encodedSHA256Payload';

        $mockEncoder = $this->prophesize(Base64SafeEncoder::class);
        $stubChallenge = $this->prophesize(AuthorizationChallenge::class);

        $extractor = new DnsDataExtractor($mockEncoder->reveal());

        $stubChallenge->getPayload()->willReturn($payload);

        $mockEncoder->encode(hash('sha256', $payload, true))->willReturn($encodedPayload);

        $this->assertEquals($encodedPayload, $extractor->getRecordValue($stubChallenge->reveal()));
    }
}
