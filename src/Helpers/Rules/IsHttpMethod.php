<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

class IsHttpMethod extends AbstractRule
{
    protected $message = 'errors.validation.must_be_http_method';

    protected $HTTP_Methods = ['HEAD', 'GET', 'POST', 'PUT',
                               'PATCH', 'DELETE', 'OPTIONS',
                               'PURGE', 'TRACE', 'CONNECT', ];

    public function apply(...$arguments): bool
    {
        return in_array($this->value, $this->HTTP_Methods);
    }
}
