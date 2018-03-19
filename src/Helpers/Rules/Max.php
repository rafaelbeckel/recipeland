<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

use BadMethodCallException;

class Max extends AbstractRule
{
    protected $message = 'errors.validation.value_must_be_less_than:$1';

    public function apply(...$arguments): bool
    {
        if (count($arguments) != 1) {
            throw new BadMethodCallException('Min needs exactly 1 argument');
        }

        $max = floatval($this->value);
        $value = floatval($arguments[0]);
        
        return $max >= $value;
    }
}
