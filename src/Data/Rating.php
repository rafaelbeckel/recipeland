<?php declare(strict_types=1);

namespace Recipeland\Data;

use Recipeland\Data\Recipe;
use Illuminate\Database\Eloquent\Model;
use Recipeland\Traits\Data\HasCompositePrimaryKey;

class Rating extends Model
{
    use HasCompositePrimaryKey;
    
    protected $primaryKey = ['user_id', 'recipe_id'];
    
    protected $casts = [
        'rating' => 'float',
    ];
    
    public $timestamps = false;
    
    public function author()
    {
        return $this->belongsTo('Recipeland\Data\User', 'user_id');
    }
    
    public function recipe()
    {
        return $this->belongsTo('Recipeland\Data\Recipe', 'recipe_id');
    }
    
    public static function average($id): ?float
    {
        $count = Rating::where('recipe_id', $id)->count();
        if (!$count) {
            return null;
        }
        
        $sum = 0.0; //cannot aggregate because it's an enum of strings
        $ratings = Rating::where('recipe_id', $id)->get();
        foreach ($ratings as $rating) {
            $sum += floatval($rating->rating);
        }
        
        return floatval(number_format($sum/$count, 1));
    }
    
    public function save(array $options = [])
    {
        $saved = parent::save($options);
        $this->recipe->rating = $this->average($this->recipe_id);
        $this->recipe->save();
        return $saved;
    }
}
