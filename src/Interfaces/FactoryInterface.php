<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

use DI\FactoryInterface as Container;

interface FactoryInterface
{
    public function __construct(Container $container = null, $namespace = null);

    public function build(string $class, ...$arguments);
}
