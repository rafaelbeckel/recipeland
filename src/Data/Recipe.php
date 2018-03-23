<?php declare(strict_types=1);

namespace Recipeland\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name', 'created_by', 'subtitle', 'description', 'prep_time', 'total_time', 'vegetarian', 'picture', 'difficulty'];
    
    protected $hidden = ['published', 'deleted_at'];
    
    public function ingredients()
    {
        return $this->belongsToMany('Recipeland\Data\Ingredient', 'recipe_ingredient')
                    ->withPivot('quantity', 'unit')->as('details');
    }
    
    public function steps()
    {
        return $this->belongsToMany('Recipeland\Data\Step', 'recipe_step')
                    ->withPivot('order')->as('details');
    }
    
    public function author()
    {
        return $this->belongsTo('Recipeland\Data\User', 'created_by');
    }
    
    public function attachIngredient($ingredient, string $quantity, string $unit)
    {
        if (is_object($ingredient)) {
            $ingredient = $ingredient->getKey();
        }

        if (is_array($ingredient)) {
            $ingredient = $ingredient['id'];
        }
        
        if (! $this->ingredients()->where('ingredient_id', $ingredient)->count()) {
            $this->ingredients()->attach($ingredient, ['quantity'=>$quantity, 'unit'=>$unit]);
        }
    }
    
    public function attachStep($step, int $order)
    {
        if (is_object($step)) {
            $step = $step->getKey();
        }

        if (is_array($step)) {
            $step = $step['id'];
        }
        
        if (! $this->steps()->where('step_id', $step)->count()) {
            $this->steps()->attach($step, ['order'=>$order]);
        }
    }
}
