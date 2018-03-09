<?php

namespace Recipeland\Data;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    protected $fillable = ['description', 'picture'];
    
    public function recipes()
    {
        return $this->belongsToMany('Recipeland\Data\Recipe', 'recipe_ingredient')
                    ->withPivot('order')->as('details');
    }
}