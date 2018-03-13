<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

interface FactoryInterface
{
    public function build(string $class, ...$arguments);
}
