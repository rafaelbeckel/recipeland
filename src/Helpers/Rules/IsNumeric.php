<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsNumeric extends AbstractRule
{
    protected $message = 'errors.validation.must_be_numeric';

    public function apply(...$arguments): bool
    {
        return is_numeric($this->value);
    }
}
