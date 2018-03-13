<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsType extends AbstractRule
{
    protected $message = 'errors.validation.must_be_of_type_$1';

    public function apply(...$arguments): bool
    {
        $this->message = str_replace('$1', strtolower($arguments[0]), $this->message);

        return gettype($this->value) == $arguments[0];
    }
}
