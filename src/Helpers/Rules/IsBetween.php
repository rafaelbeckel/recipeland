<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

use BadMethodCallException;

class IsBetween extends AbstractRule
{
    protected $message = 'errors.validation.value_must_be_between:$1,$2';

    public function apply(...$arguments): bool
    {
        if (count($arguments) != 2) {
            throw new BadMethodCallException('IsBetween needs exactly 2 arguments');
        }

        $min = floatval($arguments[0]);
        $max = floatval($arguments[1]);

        return $this->value >= $min && $this->value <= $max;
    }
}
