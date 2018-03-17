<?php

declare(strict_types=1);

namespace Recipeland\Helpers;

use RuntimeException;
use DI\FactoryInterface as Container;
use Recipeland\Interfaces\FactoryInterface;

abstract class Factory implements FactoryInterface
{
    protected $namespace;
    protected $container;

    public function __construct(Container $container = null, $namespace = null)
    {
        $this->container = $container;
        $this->setNamespace($namespace);
    }

    public function build(string $className, ...$arguments)
    {
        $class = $this->getNamespace().$className;

        if ($this->container && class_exists($class)) {
            return $this->container->make($class, $arguments);
        } elseif (class_exists($class)) {
            return new $class(...$arguments);
        } else {
            throw new RuntimeException('Class '.$class.' not Found');
        }
    }

    protected function getNamespace(): string
    {
        return $this->namespace ? $this->namespace.'\\' : '';
    }

    protected function setNamespace($namespace): void
    {
        if ($namespace !== null) {
            $this->namespace = $namespace;
        }
    }
}
