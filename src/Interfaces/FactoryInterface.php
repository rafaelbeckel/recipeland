<?php

namespace Recipeland\Interfaces;

interface FactoryInterface
{
    public function build(string $class);
}
