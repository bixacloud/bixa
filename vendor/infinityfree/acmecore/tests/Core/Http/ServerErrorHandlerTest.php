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

use InfinityFree\AcmeCore\Exception\AcmeCoreServerException;
use InfinityFree\AcmeCore\Exception\Server\BadCsrServerException;
use InfinityFree\AcmeCore\Exception\Server\BadNonceServerException;
use InfinityFree\AcmeCore\Exception\Server\CaaServerException;
use InfinityFree\AcmeCore\Exception\Server\ConnectionServerException;
use InfinityFree\AcmeCore\Exception\Server\DnsServerException;
use InfinityFree\AcmeCore\Exception\Server\IncorrectResponseServerException;
use InfinityFree\AcmeCore\Exception\Server\InternalServerException;
use InfinityFree\AcmeCore\Exception\Server\InvalidContactServerException;
use InfinityFree\AcmeCore\Exception\Server\InvalidEmailServerException;
use InfinityFree\AcmeCore\Exception\Server\MalformedServerException;
use InfinityFree\AcmeCore\Exception\Server\OrderNotReadyServerException;
use InfinityFree\AcmeCore\Exception\Server\RateLimitedServerException;
use InfinityFree\AcmeCore\Exception\Server\RejectedIdentifierServerException;
use InfinityFree\AcmeCore\Exception\Server\TlsServerException;
use InfinityFree\AcmeCore\Exception\Server\UnauthorizedServerException;
use InfinityFree\AcmeCore\Exception\Server\UnknownHostServerException;
use InfinityFree\AcmeCore\Exception\Server\UnsupportedContactServerException;
use InfinityFree\AcmeCore\Exception\Server\UnsupportedIdentifierServerException;
use InfinityFree\AcmeCore\Exception\Server\UserActionRequiredServerException;
use InfinityFree\AcmeCore\Http\ServerErrorHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ServerErrorHandlerTest extends TestCase
{
    public function getErrorTypes()
    {
        return [
            ['badCSR', BadCsrServerException::class],
            ['badNonce', BadNonceServerException::class],
            ['caa', CaaServerException::class],
            ['connection', ConnectionServerException::class],
            ['dns', DnsServerException::class],
            ['incorrectResponse', IncorrectResponseServerException::class],
            ['invalidContact', InvalidContactServerException::class],
            ['invalidEmail', InvalidEmailServerException::class],
            ['malformed', MalformedServerException::class],
            ['orderNotReady', OrderNotReadyServerException::class],
            ['rateLimited', RateLimitedServerException::class],
            ['rejectedIdentifier', RejectedIdentifierServerException::class],
            ['serverInternal', InternalServerException::class],
            ['tls', TlsServerException::class],
            ['unauthorized', UnauthorizedServerException::class],
            ['unknownHost', UnknownHostServerException::class],
            ['unsupportedContact', UnsupportedContactServerException::class],
            ['unsupportedIdentifier', UnsupportedIdentifierServerException::class],
            ['userActionRequired', UserActionRequiredServerException::class],
        ];
    }

    /**
     * @dataProvider getErrorTypes
     */
    public function testAcmeExceptionThrown($type, $exceptionClass)
    {
        $errorHandler = new ServerErrorHandler();

        $response = new Response(500, [], json_encode([
            'type' => 'urn:acme:error:'.$type,
            'detail' => $exceptionClass.'Detail',
        ]));

        $exception = $errorHandler->createAcmeExceptionForResponse(new Request('GET', '/foo/bar'), $response);

        $this->assertInstanceOf($exceptionClass, $exception);
        $this->assertStringContainsString($type, $exception->getMessage());
        $this->assertStringContainsString($exceptionClass.'Detail', $exception->getMessage());
        $this->assertStringContainsString('/foo/bar', $exception->getMessage());
    }

    public function testDefaultExceptionThrownWithInvalidJson()
    {
        $errorHandler = new ServerErrorHandler();

        $exception = $errorHandler->createAcmeExceptionForResponse(
            new Request('GET', '/foo/bar'),
            new Response(500, [], 'Invalid JSON')
        );

        $this->assertInstanceOf(AcmeCoreServerException::class, $exception);
        $this->assertStringContainsString('non-ACME', $exception->getMessage());
        $this->assertStringContainsString('/foo/bar', $exception->getMessage());
        $this->assertStringContainsString('Invalid JSON', $exception->getMessage());
    }

    public function testDefaultExceptionThrownNonAcmeJson()
    {
        $errorHandler = new ServerErrorHandler();

        $exception = $errorHandler->createAcmeExceptionForResponse(
            new Request('GET', '/foo/bar'),
            new Response(500, [], json_encode(['not' => 'acme']))
        );

        $this->assertInstanceOf(AcmeCoreServerException::class, $exception);
        $this->assertStringContainsString('non-ACME', $exception->getMessage());
        $this->assertStringContainsString('/foo/bar', $exception->getMessage());
        $this->assertStringContainsString('"not":"acme"', $exception->getMessage());
    }
}
