<?php

declare(strict_types=1);

namespace Recipeland\Middleware;

use Recipeland\Controllers\Errors;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Traits\ReturnsErrorResponse;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;

class HttpsOnly implements MiddlewareInterface
{
    use ReturnsErrorResponse;
    
    public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
    {
        $scheme = $this->detectScheme($request);

        if ($scheme !== 'https') {
            return $this->errorResponse('forbidden', $request, $next);
        }

        return $next->handle($request);
    }

    protected function detectScheme(RequestInterface $request)
    {
        if ($request->getHeader('x-forwarded-proto') == ['https'] ||
            $request->getHeader('x-forwarded-protocol') == ['https'] ||
            $request->getHeader('front-end-https') == ['on'] ||
            $request->getHeader('front-end-https') == ['1'] ||
            $request->getHeader('x-url-scheme') == ['on'] ||
            $request->getHeader('x-url-scheme') == ['1'] ||
            $request->getHeader('x-forwarded-ssl') == ['on'] ||
            $request->getHeader('x-forwarded-ssl') == ['1'] ||
            $request->getUri()->getScheme() == 'https') {
            return 'https';
        } else {
            return 'http';
        }
    }
}
