<?php 

declare(strict_types=1);

namespace Recipeland\Http\Request;

use Recipeland\Http\Request\SpecializedRequest;

class LoginRequest extends SpecializedRequest
{
    // At least one uppercase letter, one lowercase letter, one number and one special character
    const PASSWORD_PATTERN = "|^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!-_%*?&])[A-Za-z\d$@$!-_%*?&]{8,}$|";
    
    public function addRules(): void
    {
        $this->addRule('body:item(username):chars:min(4)');
        $this->addRule('body:item(password):chars:min(8)');
        $this->addRule('body:item(password):is_pattern('.self::PASSWORD_PATTERN.')');
    }
}
