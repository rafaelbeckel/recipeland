<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Validators;

use InvalidArgumentException;
use Recipeland\Helpers\Validator;

class RoutesArrayValidator extends Validator
{
    protected function init(): void
    {
        $this->addRule('not_empty')
             ->addRule('is_array')
             ->addRule('each:is_array')
             ->addRule('each:not_empty')
             ->addRule('each:count:equals(3)')
             ->addRule('each:item(0):is_http_method')
             ->addRule('each:item(1):is_url_path')
             ->addRule('each:item(2):is_pattern(|[^@]*@[^@]*|)');
    }
}
