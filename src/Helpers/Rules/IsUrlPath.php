<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsUrlPath extends AbstractRule
{
    protected $message = 'errors.validation.must_be_url_path';

    /**
     * You can test this pattern at:
     * https://regex101.com/r/LztcRw/1.
     */
    protected $pattern = "/^(\/)([\w\/\[\]\{\}]*)(\??[\w\/\[\]\{\}]+\=[\w\/\[\]\{\}]+)*(\&?[\w\/\[\]\{\}]+\=[\w\/\[\]\{\}]+)*$/i";

    public function apply(...$arguments): bool
    {
        return (bool) preg_match($this->pattern, $this->value);
    }
}
