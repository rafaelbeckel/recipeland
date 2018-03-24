<?php declare(strict_types=1);

namespace Recipeland\Data;

use Illuminate\Database\Eloquent\Model;
use Recipeland\Traits\Data\HasCompositePrimaryKey;

class Rating extends Model
{
    use HasCompositePrimaryKey;
    
    protected $primaryKey = ['user_id', 'recipe_id'];
    
    public $timestamps = false;
    
    public static function average($id): ?array
    {
        $count = Rating::where('recipe_id', $id)->count();
        if (!$count) {
            return null;
        }
        
        $sum = 0.0;
        $ratings = Rating::where('recipe_id', $id)->get();
        foreach ($ratings as $rating) {
            $sum += floatval($rating->rating);
        }
        
        return [
            'count' => $count,
            'average' => number_format($sum/$count, 1)
        ];
    }
}
