<?php

namespace Tests\Api;

use Recipeland\Data\Step;
use Recipeland\Data\User;
use Recipeland\Data\Recipe;
use Recipeland\Data\Rating;
use Phinx\Wrapper\TextWrapper;
use Recipeland\Data\Ingredient;
use Phinx\Console\PhinxApplication;

trait SeedsData
{
    protected $container;
    
    protected $expected = [
        'current_page' => 1,
        'data' => [],
        'first_page_url' => '/?page=1',
        'from' => 1,
        'last_page' => 1,
        'last_page_url' => '/?page=1',
        'next_page_url' => null,
        'path' => '/',
        'per_page' => 10,
        'prev_page_url' => null,
        'to' => 4,
        'total' => 4,
    ];
    
    protected $newRecipe = '{
        "recipe" : {
            "name" : "My Most delicious Recipe!",
            "subtitle" : "My subtitle",
            "description" : "My description",
            "prep_time" : 10,
            "total_time" : 20,
            "vegetarian" : 1,
            "difficulty" : 3,
            "picture" : "https://example.com/picture.jpg",
            "ingredients" : [
                {
                    "slug" : "my_ingredient",
                    "quantity" : "12",
                    "unit" : "mg",
                    "name" : "My Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                    "allergens" : "milk"
                },
                {
                    "slug" : "my_second_ingredient",
                    "quantity" : "10",
                    "unit" : "ml",
                    "name" : "My Second Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                    "allergens" : "none"
                }
            ],
            "steps" : [
                {
                    "description" : "Put ingredient somewhere",
                    "picture" : "https://example.com/picture.jpg"
                },
                {
                    "description" : "Eat Ingredient",
                    "picture" : "https://example.com/picture.jpg"
                }
            ]
        }
    }';
    
    /***********************************************************************
     *                       DATABASE SEEDING METHODS                      *
     ***********************************************************************/
    private function database($command, $what=null)
    {
        if (getenv('ENVIRONMENT') == 'testing' &&
           getenv('DB_CONNECTION') == 'pgtest') {
            $phinx = new PhinxApplication();
            $wrapper = new TextWrapper($phinx);
            $wrapper->setOption('configuration', BASE_DIR.'/phinx.php');
            $wrapper->setOption('environment', 'testing');
            $wrapper->setOption('parser', 'PHP');
            
            switch ($command) {
                case 'migrate':
                    $wrapper->getMigrate();
                    break;
                    
                case 'seed':
                    if ($what == 'users') {
                        $wrapper->getSeed(null, null, 'AclSeeder');
                    } elseif ($what == 'recipes') {
                        $this->createRecipes();
                    }
                    break;
                    
                case 'rollback':
                    $wrapper->getRollback(null, 0);
                    break;
            }
        }
    }
    
    private function createRecipes()
    {
        $authors = [
            User::where('username', 'luigi')->first(),
            User::where('username', 'burns')->first(),
        ];

        $recipes = [
            $this->createRecipe($authors[0], 'Fooo', 10, 20, 1, 1),
            $this->createRecipe($authors[1], 'Baar', 15, 25, 1, 1),
            $this->createRecipe($authors[0], 'Baaz', 20, 30, 0, 2),
            $this->createRecipe($authors[1], 'Biim', 25, 35, 1, 3),
        ];

        $ingredients = [
            $this->createIngredient('Lola'),
            $this->createIngredient('Lula'),
        ];

        $steps = [
            $this->createStep('Do something'),
            $this->createStep('Go home'),
        ];

        foreach ($recipes as $recipe) {
            $recipe->attachIngredient($ingredients[0], '123', 'mg');
            $recipe->attachIngredient($ingredients[1], '456', 'kg');
            $recipe->attachStep($steps[0], 1);
            $recipe->attachStep($steps[1], 2);
        }
    }

    private function createRecipe(
        User $author,
        string $name,
        int $prep_time,
        int $total_time,
        int $vegetarian,
        int $difficulty
    ) {
        return Recipe::firstOrCreate(
            ['name' => 'Test Recipe '.$name],
            [
                'created_by' => $author->id,
                'subtitle' => 'Test Recipe by '.$author->name,
                'description' => 'TESTING',
                'prep_time' => $prep_time,
                'total_time' => $total_time,
                'vegetarian' => $vegetarian,
                'difficulty' => $difficulty,
                'picture' => 'https://example.com/example.jpg',
            ]
        );
    }

    private function createIngredient($name)
    {
        return Ingredient::firstOrCreate(
            ['slug' => str_replace(' ', '_', strtolower($name))],
            [
                'name' => $name,
                'picture' => 'https://example.com/example.jpg',
                'allergens' => 'none',
            ]
        );
    }

    private function createStep($description)
    {
        return Step::firstOrCreate(
            ['description' => $description],
            [
                'picture' => 'https://example.com/example.jpg',
            ]
        );
    }
}
