<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Helpers\Factory;

class ControllerFactory extends Factory
{
    protected $namespace = __NAMESPACE__;

    public function build(string $className, ...$arguments)
    {
        $class = $this->getNamespace().$className;

        if ($this->container && class_exists($class)) {
            return $this->container->make($class, [
                'action' => $arguments[0],
                'arguments' => $arguments[1] ?? [],
                'logger' => $this->container->get('log')
            ]);
        } else {
            throw new RuntimeException('Class '.$class.' not Found');
        }
    }
}
