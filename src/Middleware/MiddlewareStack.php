<?php

namespace Recipeland\Middleware;

use Recipeland\Stack;

class MiddlewareStack extends Stack
{
    protected $items = [
        \Recipeland\Middlewares\AuthMiddleware::class,
    ];
}
