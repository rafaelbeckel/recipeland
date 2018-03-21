<?php

namespace Recipeland\Traits;

use Recipeland\Controllers\Errors;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;

trait ReturnsErrorResponse
{
    protected function errorResponse(string $error, RequestInterface $request, HandlerInterface $next, string $message = null): ResponseInterface
    {
        $argument = $message ? ['message' => $message] : [];
        $errorController = new Errors($error, $argument);
    
        return $errorController->process($request, $next);
    }
}
