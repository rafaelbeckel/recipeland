<?php

declare(strict_types=1);

namespace Recipeland\Middleware;

use Recipeland\Stack;

class MiddlewareStack extends Stack
{
    /**
     * Middleware list for ALL routes.
     */
    protected $items = [
        HttpsOnly::class, //ok
        AddSecurityHeaders::class, //ok
        GetDataSources::class, //ok
        //AuthenticateUsers::class, //@todo implement
        //ValidateRequests::class, //@todo implement

        // @todo remove those comments
    ];
}
