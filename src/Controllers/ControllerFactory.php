<?php

namespace Recipeland\Controllers;

use Recipeland\Interfaces\FactoryInterface;
use \RuntimeException;

class ControllerFactory implements FactoryInterface
{
    public function build(string $className)
    {
        $class = __namespace__.'\\'.$className;
        
        if (class_exists($class)) {
            return new $class;
        } else {
            throw new RuntimeException('Class not Found');
        }
    }
}
