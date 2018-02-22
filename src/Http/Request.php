<?php

namespace Recipeland\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    public static function start()
    {
        return self::createFromGlobals();
    }
}