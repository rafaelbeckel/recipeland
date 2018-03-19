<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Validators;

use Recipeland\Helpers\Validator;

class RequestValidator extends Validator
{
    protected function init(): void
    {
        $this->addRule('not_empty');
        $this->addRule('is_array');
    }
}
