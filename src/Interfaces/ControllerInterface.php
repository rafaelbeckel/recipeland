<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

interface ControllerInterface
{
    public function __construct(string $action, array $arguments);

    public function getMiddleware(): array;

    public function getAction(): string;

    public function getArguments(): array;

    public function setQueryParams(array $params): void;

    public function getQueryParam(string $key, $default);

    public function setStatus(int $code): void;

    public function setResponseBody(string $body): void;

    public function setJsonResponse(array $json): void;
}
