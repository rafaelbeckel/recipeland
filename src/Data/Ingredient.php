<?php declare(strict_types=1);

namespace Recipeland\Data;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['slug', 'name', 'picture', 'allergens'];
    
    public function recipes()
    {
        return $this->belongsToMany('Recipeland\Data\Recipe', 'recipe_ingredient')
                    ->withPivot('quantity', 'unit')
                    ->as('details');
    }
}