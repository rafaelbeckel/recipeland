<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class NotEmpty extends AbstractRule
{
    protected $message = 'errors.validation.cannot_be_empty';

    public function apply(...$arguments): bool
    {
        return !empty($this->value);
    }
}
