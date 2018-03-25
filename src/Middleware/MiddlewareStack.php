<?php

declare(strict_types=1);

namespace Recipeland\Middleware;

use Recipeland\Helpers\Stack;

class MiddlewareStack extends Stack
{
    /**
     * Middleware list for ALL routes.
     */
    protected $items = [
        HttpsOnly::class,
        AddSecurityHeaders::class,
        GetDataSources::class,
        VerifyJWT::class,
    ];
}
