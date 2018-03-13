<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsPattern extends AbstractRule
{
    protected $message = 'errors.validation.must_match_pattern';

    public function apply(...$arguments): bool
    {
        return (bool) preg_match($arguments[0], $this->value);
    }
}
