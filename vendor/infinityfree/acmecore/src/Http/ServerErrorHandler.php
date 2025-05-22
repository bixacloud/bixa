<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore\Http;

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
use InfinityFree\AcmeCore\Util\JsonDecoder;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Create appropriate exception for given server response.
 *
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class ServerErrorHandler
{
    private static $exceptions = [
        'badCSR' => BadCsrServerException::class,
        'badNonce' => BadNonceServerException::class,
        'caa' => CaaServerException::class,
        'connection' => ConnectionServerException::class,
        'dns' => DnsServerException::class,
        'incorrectResponse' => IncorrectResponseServerException::class,
        'invalidContact' => InvalidContactServerException::class,
        'invalidEmail' => InvalidEmailServerException::class,
        'malformed' => MalformedServerException::class,
        'orderNotReady' => OrderNotReadyServerException::class,
        'rateLimited' => RateLimitedServerException::class,
        'rejectedIdentifier' => RejectedIdentifierServerException::class,
        'serverInternal' => InternalServerException::class,
        'tls' => TlsServerException::class,
        'unauthorized' => UnauthorizedServerException::class,
        'unknownHost' => UnknownHostServerException::class,
        'unsupportedContact' => UnsupportedContactServerException::class,
        'unsupportedIdentifier' => UnsupportedIdentifierServerException::class,
        'userActionRequired' => UserActionRequiredServerException::class,
    ];

    /**
     * Get a response summary (useful for exceptions).
     * Use Guzzle method if available (Guzzle 6.1.1+).
     */
    public static function getResponseBodySummary(ResponseInterface $response): string
    {
        // Rewind the stream if possible to allow re-reading for the summary.
        if ($response->getBody()->isSeekable()) {
            $response->getBody()->rewind();
        }

        if (method_exists(RequestException::class, 'getResponseBodySummary')) {
            return RequestException::getResponseBodySummary($response);
        }

        $body = Utils::copyToString($response->getBody());

        if (\strlen($body) > 120) {
            return substr($body, 0, 120).' (truncated...)';
        }

        return $body;
    }

    public function createAcmeExceptionForResponse(
        RequestInterface $request,
        ResponseInterface $response,
        \Exception $previous = null
    ): AcmeCoreServerException {
        $body = Utils::copyToString($response->getBody());

        try {
            $data = JsonDecoder::decode($body, true);
        } catch (\InvalidArgumentException $e) {
            $data = null;
        }

        if (!$data || !isset($data['type'], $data['detail'])) {
            // Not JSON: not an ACME error response
            return $this->createDefaultExceptionForResponse($request, $response, $previous);
        }

        $type = preg_replace('/^urn:(ietf:params:)?acme:error:/i', '', $data['type']);

        if (!isset(self::$exceptions[$type])) {
            // Unknown type: not an ACME error response
            return $this->createDefaultExceptionForResponse($request, $response, $previous);
        }

        $exceptionClass = self::$exceptions[$type];

        return new $exceptionClass(
            $request,
            sprintf('%s (on request "%s %s")', $data['detail'], $request->getMethod(), $request->getUri()),
            $previous
        );
    }

    private function createDefaultExceptionForResponse(
        RequestInterface $request,
        ResponseInterface $response,
        \Exception $previous = null
    ): AcmeCoreServerException {
        return new AcmeCoreServerException(
            $request,
            sprintf(
                'A non-ACME %s HTTP error occured on request "%s %s" (response body: "%s")',
                $response->getStatusCode(),
                $request->getMethod(),
                $request->getUri(),
                self::getResponseBodySummary($response)
            ),
            $previous
        );
    }
}
