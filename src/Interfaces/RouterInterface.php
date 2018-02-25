<?php

namespace Recipeland\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface RouterInterface
{
    public function __construct(Array $routes);
    
    public function go(Request $request): void;
    
    public function setRoutes(Array $routes): void;
}