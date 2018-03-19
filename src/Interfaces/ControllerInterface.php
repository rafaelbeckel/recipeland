<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

use Psr\Log\LoggerInterface as Logger;

interface ControllerInterface
{
    public function __construct(string $action, array $arguments, Logger $logger);

    public function setStatus(int $code): void;
    
    public function setHeader(string $key, string $value): void;

    public function setResponseBody(string $body): void;

    public function setJsonResponse(array $json): void;
}
