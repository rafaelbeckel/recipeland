<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

use BadMethodCallException;

class Min extends AbstractRule
{
    protected $message = 'errors.validation.$1_must_be_greater_than_$2';

    public function apply(...$arguments): bool
    {
        if (count($arguments) != 1) {
            throw new BadMethodCallException('Min needs exactly 1 argument');
        }
        
        $min = floatval($arguments[0]);
        $value = floatval($this->value);
        
        $this->message = str_replace(['$1','$2'], [$value,$min], $this->message);
        
        return $min <= $value;
    }
}
