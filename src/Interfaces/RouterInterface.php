<?php

namespace Recipeland\Interfaces;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

interface RouterInterface
{
    public function __construct(array $routes);
    
    public function getControllerFor(RequestInterface $request): MiddlewareInterface;
    
    public function setRoutes(array $routes): void;
}
