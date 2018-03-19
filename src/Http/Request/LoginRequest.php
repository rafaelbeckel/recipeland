<?php 

declare(strict_types=1);

namespace Recipeland\Http\Request;

use Recipeland\Http\Request\SpecializedRequest;

class LoginRequest extends SpecializedRequest
{
    protected function addRules(): void
    {
        $this->addRule('item(username):not_empty');
        $this->addRule('item(password):not_empty');
        $this->addRule('item(password):chars:min(8)');
    }
}
