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
use InfinityFree\AcmeCore\Challenge\Dns\DnsResolverInterface;
use InfinityFree\AcmeCore\Challenge\Dns\DnsValidator;
use InfinityFree\AcmeCore\Challenge\SolverInterface;
use InfinityFree\AcmeCore\Protocol\AuthorizationChallenge;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DnsValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSupports()
    {
        $typeDns = 'dns-01';
        $typeHttp = 'http-01';

        $mockExtractor = $this->prophesize(DnsDataExtractor::class);
        $stubChallenge = $this->prophesize(AuthorizationChallenge::class);

        $validator = new DnsValidator($mockExtractor->reveal());

        $stubChallenge->getType()->willReturn($typeDns);
        $this->assertTrue($validator->supports($stubChallenge->reveal(), $this->prophesize(SolverInterface::class)->reveal()));

        $stubChallenge->getType()->willReturn($typeHttp);
        $this->assertFalse($validator->supports($stubChallenge->reveal(), $this->prophesize(SolverInterface::class)->reveal()));
    }

    public function testIsValid()
    {
        $recordName = '_acme-challenge.bar.com.';
        $recordValue = 'record_value';

        $mockResolver = $this->prophesize(DnsResolverInterface::class);
        $mockResolver->getTxtEntries($recordName)->willReturn([$recordValue]);
        $mockExtractor = $this->prophesize(DnsDataExtractor::class);
        $stubChallenge = $this->prophesize(AuthorizationChallenge::class);

        $validator = new DnsValidator($mockExtractor->reveal(), $mockResolver->reveal());

        $mockExtractor->getRecordName($stubChallenge->reveal())->willReturn($recordName);
        $mockExtractor->getRecordValue($stubChallenge->reveal())->willReturn($recordValue);

        $this->assertTrue($validator->isValid($stubChallenge->reveal(), $this->prophesize(SolverInterface::class)->reveal()));
    }

    public function testIsValidCheckRecordValue()
    {
        $recordName = '_acme-challenge.bar.com.';
        $recordValue = 'record_value';

        $mockResolver = $this->prophesize(DnsResolverInterface::class);
        $mockResolver->getTxtEntries($recordName)->willReturn(['somethingElse']);
        $mockExtractor = $this->prophesize(DnsDataExtractor::class);
        $stubChallenge = $this->prophesize(AuthorizationChallenge::class);

        $validator = new DnsValidator($mockExtractor->reveal(), $mockResolver->reveal());

        $mockExtractor->getRecordName($stubChallenge->reveal())->willReturn($recordName);
        $mockExtractor->getRecordValue($stubChallenge->reveal())->willReturn($recordValue);

        $this->assertFalse($validator->isValid($stubChallenge->reveal(), $this->prophesize(SolverInterface::class)->reveal()));
    }
}
