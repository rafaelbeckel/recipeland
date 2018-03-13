<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

interface ValidatorInterface
{
    public function __construct(FactoryInterface $factory = null);

    public function addRule(string $rule): ValidatorInterface;

    public function validate($payload): bool;

    public function getMessage(): string;
}
