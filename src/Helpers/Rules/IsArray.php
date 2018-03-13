<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsArray extends AbstractRule
{
    protected $message = 'errors.validation.must_be_array';

    public function apply(...$arguments): bool
    {
        return is_array($this->value);
    }
}
