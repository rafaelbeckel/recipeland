<?php 

declare(strict_types=1);

namespace Recipeland\Http\Request;

use Lcobucci\JWT\Token;
use Illuminate\Database\Connection as DB;
use Recipeland\Http\Request\CreateRecipeRequest;

class UpdateRecipeRequest extends CreateRecipeRequest
{
    public function addRules(): void
    {
        parent::addRules();
        $this->addRule('body:item(recipe):item(id):is_numeric');
    }
}
