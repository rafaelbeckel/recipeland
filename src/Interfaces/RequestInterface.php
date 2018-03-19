<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

use Psr\Http\Message\ServerRequestInterface as ServerRequest;

interface RequestInterface extends ServerRequest
{
    public function getParam(string $key, $default = '');
    
    public static function upgrade(ServerRequest $request): RequestInterface;
}
