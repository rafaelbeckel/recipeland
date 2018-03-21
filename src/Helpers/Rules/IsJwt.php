<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsJwt extends AbstractRule
{
    protected $message = 'errors.validation.must_be_a_vaid_jwt_token';

    /**
     * You can test this pattern at:
     * https://regex101.com/r/2LoI0i/1
     */
    protected $pattern = "|^(Bearer\s)([A-Za-z0-9-_=]+\.[A-Za-z0-9-_=]+\.?[A-Za-z0-9-_.+\/=]*[^\.]+)$|";

    public function apply(...$arguments): bool
    {
        return (bool) preg_match($this->pattern, (string) $this->value);
    }
}
