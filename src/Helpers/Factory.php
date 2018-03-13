<?php

declare(strict_types=1);

namespace Recipeland\Helpers;

use RuntimeException;
use Recipeland\Interfaces\FactoryInterface;

abstract class Factory implements FactoryInterface
{
    protected $namespace;

    public function __construct($namespace = null)
    {
        if ($namespace) {
            $this->namespace = $namespace;
        }
    }

    public function build(string $className, ...$arguments)
    {
        $class = $this->namespace.'\\'.$className;
        
        if (class_exists($class)) {
            return new $class(...$arguments);
            
        } else {
            throw new RuntimeException('Class '.$class.' not Found');
        }
    }
}
