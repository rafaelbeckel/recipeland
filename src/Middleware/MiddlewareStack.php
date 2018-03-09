<?php declare(strict_types=1);

namespace Recipeland\Middleware;

use Recipeland\Stack;

class MiddlewareStack extends Stack
{
    /**
     * Middleware list for ALL routes
     */
    protected $items = [
        \Recipeland\Middlewares\HttpsOnly::class,
        \Recipeland\Middlewares\AddSecurityHeaders::class,
    ];
}
