<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsPattern extends AbstractRule
{
    protected $message = 'errors.validation.must_match_pattern';

    public function apply(...$arguments): bool
    {
        $pattern = isset($arguments[1]) ? implode(',', $arguments) : $arguments[0];
        return (bool) preg_match($pattern, (string) $this->value);
    }
}
