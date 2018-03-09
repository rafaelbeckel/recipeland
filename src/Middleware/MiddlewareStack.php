<?php declare(strict_types=1);

namespace Recipeland\Middleware;

use Recipeland\Stack;

class MiddlewareStack extends Stack
{
    protected $items = [
        \Recipeland\Middlewares\AuthMiddleware::class,
    ];
}
