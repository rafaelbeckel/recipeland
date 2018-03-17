<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface SenderInterface
{
    public function send(ResponseInterface $response): void;
}
