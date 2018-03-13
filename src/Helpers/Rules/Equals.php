<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class Equals extends AbstractRule
{
    protected $message = 'errors.validation.value_must_be_$1';

    public function apply(...$arguments): bool
    {
        $this->message = str_replace('$1', $arguments[0], $this->message);

        return $this->value == $arguments[0];
    }
}
