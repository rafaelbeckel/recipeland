<?php

namespace Recipeland\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
    {
        // @TODO do Auth Stuff
        
        $response = $next->handle($request);
        
        // Run After the Controller
        
        return $response;
    }
}
