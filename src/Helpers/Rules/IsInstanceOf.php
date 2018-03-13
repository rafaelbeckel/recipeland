<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsInstanceOf extends AbstractRule
{
    protected $message = 'errors.validation.must_be_instance_of_$1';

    public function apply(...$arguments): bool
    {
        $this->message = str_replace('$1', strtolower($arguments[0]), $this->message);

        return $this->value instanceof $arguments[0];
    }
}
