<?php

namespace Recipeland\Helpers;

use Recipeland\Interfaces\ValidatorInterface;

abstract class Validator implements ValidatorInterface
{
    abstract public function validate($payload): void;
}
