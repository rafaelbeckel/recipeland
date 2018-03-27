<?php 

declare(strict_types=1);

namespace Recipeland\Http\Request;

use Lcobucci\JWT\Token;
use Illuminate\Database\Connection as DB;
use Recipeland\Http\Request\SpecializedRequest;

class RateRecipeRequest extends SpecializedRequest
{
    public function addRules(): void
    {
        $this->addRule('headers:item(authorization):item(0):is_jwt');
        
        $this->addRule('attributes:item(db):is_instance_of('.DB::class.')');
        $this->addRule('attributes:item(jwt):is_instance_of('.Token::class.')');
        
        $this->addRule('body:item(rating):is_type(integer)');
        $this->addRule('body:item(rating):is_between(1,5)');
    }
}
