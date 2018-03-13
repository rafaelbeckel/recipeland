<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

interface RuleInterface
{
    public function __construct($value);

    public function apply(...$arguments): bool;

    public function getMessage(): string;
}
