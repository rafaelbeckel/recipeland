<?php 

declare(strict_types=1);

namespace Recipeland\Http\Request;

use Lcobucci\JWT\Token;
use Recipeland\Data\Recipe;
use Illuminate\Database\Connection as DB;
use Recipeland\Http\Request\SpecializedRequest;

class SearchRecipeRequest extends SpecializedRequest
{
    public function addRules(): void
    {
        $this->addRule('attributes:item(db):is_instance_of('.DB::class.')');
        
        $this->addRule('query:not_empty');
        $this->addRule('?query:item(query):chars:min(2)');
        $this->addRule('?query:item(author):chars:min(2)');
        $this->addRule('?query:item(vegetarian):is_numeric');
        $this->addRule('?query:item(vegetarian):is_between(0,1)');
        
        $this->addRule('?query:item(difficulty):not_empty'); //int max 3
        $this->addRule('?query:item(difficulty):item(gt):is_between(0,3)');
        $this->addRule('?query:item(difficulty):item(lt):is_between(2,3)');
        $this->addRule('?query:item(difficulty):item(gte):is_between(0,3)');
        $this->addRule('?query:item(difficulty):item(lte):is_between(1,3)');
        
        $this->addRule('?query:item(rating):not_empty'); //float max 5
        $this->addRule('?query:item(rating):item(gt):is_between(1,5)');
        $this->addRule('?query:item(rating):item(lt):is_between(1,5)');
        $this->addRule('?query:item(rating):item(gte):is_between(1,5)');
        $this->addRule('?query:item(rating):item(lte):is_between(1,5)');
        
        $this->addRule('?query:item(prep_time):not_empty');
        $this->addRule('?query:item(prep_time):item(gt):is_numeric');
        $this->addRule('?query:item(prep_time):item(lt):is_numeric');
        $this->addRule('?query:item(prep_time):item(gte):is_numeric');
        $this->addRule('?query:item(prep_time):item(lte):is_numeric');
        
        $this->addRule('?query:item(total_time):not_empty');
        $this->addRule('?query:item(total_time):item(gt):is_numeric');
        $this->addRule('?query:item(total_time):item(lt):is_numeric');
        $this->addRule('?query:item(total_time):item(gte):is_numeric');
        $this->addRule('?query:item(total_time):item(lte):is_numeric');
    }
}
