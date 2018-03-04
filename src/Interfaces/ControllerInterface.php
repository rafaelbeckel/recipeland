<?php

namespace Recipeland\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ControllerInterface
{
    public function __construct(ResponseInterface $response = null);
    
    public function defaultAction(): void;
    
    public function setAction(string $action): void;
    
    public function getAction(): string;
    
    public function setArguments(array $arguments): void;
    
    public function getArguments(): string;
    
    public function setStatus(int $code): void;
    
    public function setResponseBody(string $body): void;
    
    public function setJsonResponse(array $json): void;
}
