<?php declare(strict_types=1);

namespace Recipeland\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Step extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['description', 'picture'];
    
    public function recipes()
    {
        return $this->belongsToMany('Recipeland\Data\Recipe', 'recipe_ingredient')
                    ->withPivot('order')->as('details');
    }
}
