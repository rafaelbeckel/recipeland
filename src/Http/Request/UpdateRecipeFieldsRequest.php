<?php 

declare(strict_types=1);

namespace Recipeland\Http\Request;

use Lcobucci\JWT\Token;
use Illuminate\Database\Connection as DB;
use Recipeland\Http\Request\CreateRecipeRequest;

class UpdateRecipeFieldsRequest extends CreateRecipeRequest
{
    public function addRules(): void
    {
        $this->addRule('headers:item(authorization):item(0):is_jwt');
        
        $this->addRule('attributes:item(db):is_instance_of('.DB::class.')');
        $this->addRule('attributes:item(jwt):is_instance_of('.Token::class.')');
        
        // All items are optional, but at least one must be present
        $this->addRule('body:item(recipe):is_array');
        $this->addRule('body:item(recipe):not_empty');
        $this->addRule('?body:item(recipe):item(name):chars:min(4)');
        $this->addRule('?body:item(recipe):item(subtitle):chars:min(10)');
        $this->addRule('?body:item(recipe):item(description):chars:min(10)');
        $this->addRule('?body:item(recipe):item(prep_time):is_numeric');
        $this->addRule('?body:item(recipe):item(total_time):is_numeric');
        $this->addRule('?body:item(recipe):compare(total_time,>=,prep_time)');
        $this->addRule('?body:item(recipe):item(vegetarian):is_type(integer)');
        $this->addRule('?body:item(recipe):item(vegetarian):is_between(0,1)');
        $this->addRule('?body:item(recipe):item(difficulty):is_type(integer)');
        $this->addRule('?body:item(recipe):item(difficulty):is_between(1,3)');
        $this->addRule('?body:item(recipe):item(picture):is_url');
        
        $this->addRule('?body:item(recipe):item(ingredients):is_array');
        $this->addRule('?body:item(recipe):item(ingredients):each:item(slug):chars:min(2)');
        $this->addRule('?body:item(recipe):item(ingredients):each:item(name):chars:min(4)');
        $this->addRule('?body:item(recipe):item(ingredients):each:item(quantity):is_numeric');
        $this->addRule('?body:item(recipe):item(ingredients):each:item(units):not_empty');
        $this->addRule('?body:item(recipe):item(ingredients):each:item(picture):is_url');
        $this->addRule('?body:item(recipe):item(ingredients):each:item(allergens):chars:min(2)');
        
        $this->addRule('?body:item(recipe):item(steps):is_array');
        $this->addRule('?body:item(recipe):item(steps):each:item(description):chars:min(10)');
        $this->addRule('?body:item(recipe):item(steps):each:item(picture):is_url');
    }
}
