<?php 

declare(strict_types=1);

namespace Recipeland\Http\Request;

use Recipeland\Http\Request\SpecializedRequest;

class LoginRequest extends SpecializedRequest
{
    // At least one uppercase letter, one lowercase letter, one number and one special character
    const PASSWORD_PATTERN = "^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}$";
    
    protected function addRules(): void
    {
        $this->addRule('item(username):not_empty');
        $this->addRule('item(password):not_empty');
        $this->addRule('item(password):chars:min(4)');
        $this->addRule('item(password):chars:min(8)');
        $this->addRule('item(password):pattern('.self::PASSWORD_PATTERN.')');
    }
}
