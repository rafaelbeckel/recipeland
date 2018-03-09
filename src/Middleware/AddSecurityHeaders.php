<?php declare(strict_types=1);

namespace Recipeland\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;

class AddSecurityHeaders implements MiddlewareInterface
{
    public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
    {
        $response = $next->handle($request);
        
        return $response->withHeader("Strict-Transport-Security", "\"max-age=31536000; includeSubDomains; preload\"")
                        ->withHeader("Content-Security-Policy", "\"default-src 'self';\"")
                        ->withHeader("X-XSS-Protection", "\"1; mode=block\"")
                        ->withHeader("X-Frame-Options", "\"DENY\"")
                        ->withHeader("X-Content-Type-Options", "nosniff");
    }
}
