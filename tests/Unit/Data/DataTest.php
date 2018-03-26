<?php

namespace Tests\Unit\Data;

use InvalidArgumentException;
use Recipeland\Data\Ingredient;
use Recipeland\Data\Permission;
use Recipeland\Data\Rating;
use Recipeland\Data\Recipe;
use Recipeland\Data\Role;
use Recipeland\Data\Step;
use Recipeland\Data\User;
use Tests\Api\SeedsData;
use Tests\TestSuite;

class DataTest extends TestSuite
{
    use SeedsData;
    
    public function setUp()
    {
        $this->container = require(BASE_DIR.'/bootstrap/Config.php');
        $this->container->get('db');
        
        $this->database('rollback');
        $this->database('migrate');
        $this->database('seed', 'users');
        $this->database('seed', 'recipes');
    }
    
    public function test_models()
    {
        echo 'Data: test models methods and relationships - multiple assertions';
        
        $ingredient = Ingredient::find(1);
        $recipe = $ingredient->recipes()->first();
        $this->assertInstanceOf(Recipe::class, $recipe);
        
        $role = Role::find(1);
        $permission = Permission::find(3);
        $permission->attachRole($role);
        $attached = $permission->roles()->where('role_id', 1)->first();
        $this->assertInstanceOf(get_class($role), $attached);
        $this->assertEquals($role->id, $attached->id);
        
        $user = User::find(1);
        $role->attachUser($user);
        
        $recipe = Recipe::find(1);
        $rating = new Rating();
        $this->assertNull($rating::average(1));
        $rating->user_id = 1;
        $rating->recipe_id = 1;
        $rating->rating = 5;
        $rating->save();
        $this->assertEquals($rating->rating, $user->ratings()->first()->rating);
        $this->assertEquals($rating->author->id, $user->id);
        $this->assertEquals($rating->recipe->id, $recipe->id);
        $this->assertEquals($rating::average(1), [
            'count' => 1,
            'average' => 5
        ]);
        
        $this->assertEquals($recipe->author->id, 2);
        
        $this->assertArraySubset([
            'user_id' => 1,
            'recipe_id' => 1,
            'rating' => 5.0
        ], $recipe->ratings->toArray()[0]);
        $this->assertEquals($recipe->rating, [
            'count' => 1,
            'average' => 5
        ]);
        
        $step = Step::find(1);
        $attached = $step->recipes()->first();
        $this->assertEquals($recipe->id, $attached->id);
        
        $user->createPassword('foo');
        $this->assertTrue($user->verifyPassword('foo'));
        
        $attached = $user->recipes()->first();
        $this->assertNull($attached);
        
        $this->assertEquals('Homer', $user->firstName());
        $this->assertEquals('Simpson', $user->lastName());
    }
}
