<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

use BadMethodCallException;

class Max extends AbstractRule
{
    protected $message = 'errors.validation.$1_value_must_be_less_than_$2';

    public function apply(...$arguments): bool
    {
        if (count($arguments) != 1) {
            throw new BadMethodCallException('Min needs exactly 1 argument');
        }

        $max = floatval($arguments[0]);
        $value = floatval($this->value);
        
        $this->message = str_replace(['$1','$2'], [$value,$max], $this->message);
        
        return $max >= $value;
    }
}
