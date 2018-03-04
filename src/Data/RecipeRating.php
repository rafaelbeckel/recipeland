<?php

namespace Recipeland\Data;

use Illuminate\Database\Eloquent\Model;

class RecipeRating extends Model
{
    public $timestamps = false;
    
    protected $table = 'ratings';
}