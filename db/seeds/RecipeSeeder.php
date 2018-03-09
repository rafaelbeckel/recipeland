<?php

require_once(__DIR__.'/../../bootstrap/Autoload.php');

use Faker\Factory;
use Recipeland\Data\User;
use Recipeland\Data\Step;
use Recipeland\Data\Recipe;
use Phinx\Seed\AbstractSeed;
use Recipeland\Data\Ingredient;
use Bezhanov\Faker\Provider\Food;

class RecipeSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = Factory::create();
        $faker->addProvider(new Food($faker));
        
        $authors = [
            User::where('username','luigi')->first(),
            User::where('username','burns')->first()
        ];
        
        $actions = require('cooking_verbs.php');
        
        // Let's create 100 recipes and its related objects!
        for ($count = 0; $count < getenv('RECIPE_SEED_COUNT'); $count++)
        {
            $spices = [];
            $ingredients = [];
            $prepare_actions = [];
            $prepared_actions = [];
            $cook_actions = [];
            $cooked_actions = [];
            $serve_action = '';
            
            // Select spices
            for ($i=0; $i<rand(1,3); $i++) {
                $spices[] = $faker->unique($reset = true)->spice;
            }
            
            // Select ingredients
            for ($i=0; $i<rand(2,7); $i++) {
                $ingredients[] = $faker->unique($reset = true)->ingredient;
            }
            
            // Select prepare actions
            for ($i=0; $i<rand(1,3); $i++) {
                $index = rand(0,14);
                $prepare_actions[]  = $actions['prepare'][$index];
                $prepared_actions[] = $actions['prepared'][$index];
            }
            
            // Select cook actions
            for ($i=0; $i<rand(1,3); $i++) {
                $index = rand(0,7);
                $cook_actions[]   = $actions['cook'][$index];
                $cooked_actions[] = $actions['cooked'][$index];
            }
            
            // Select serve action
            $serve_action = $actions['serve'][rand(0,2)];
            
            // Select chef
            $author = $authors[rand(0,1)];
            
            // Select some fancy title
            $fancyTitles = [
                '', //No title
                ' with '.$prepared_actions[0].' '.$spices[0],
                ' a la '.$faker->lastName,
                ' from '.$faker->city,
                ' by '  .$author->name
            ];
            $fancyTitle = $fancyTitles[rand(0,4)];
            
            // Create a recipe
            $recipe = Recipe::firstOrCreate(
                ['name' => ucwords($cooked_actions[0].' '.$ingredients[0]).$fancyTitle],
                [
                    'created_by'  => $author->id,
                    'subtitle'    => 'A delicious random-generated recipe by ' . $author->name,
                    'description' => "Just don't try to actually cook it, OK?",
                    'prep_time'   => rand(10,30),
                    'total_time'  => rand(20,60),
                    'vegetarian'  => rand(0,1) == 1,
                    'difficulty'  => rand(1,3),
                    'picture'     => $faker->imageUrl(800,600,'food'),
                ]
            );
            
            // Save ingredients in the database
            $ingredients = array_merge($ingredients, $spices);
            foreach ($ingredients as $ingredientName) {
                $ingredient = Ingredient::firstOrCreate(
                    ['slug' => str_replace(' ', '_', strtolower($ingredientName))],
                    [
                        'name'      => $ingredientName,
                        'picture'   => $faker->imageUrl(100,100,'food'),
                        'allergens' => 'none'
                    ]
                );
                
                list($quantity, $unit) = explode(' ', $faker->measurement());
                
                $recipe->attachIngredient($ingredient, $quantity, $unit);
            }
            
            // Save cooking steps in the database
            $order = 0;
            $steps = array_merge($prepare_actions, $cook_actions, [$serve_action]);
            foreach ($steps as $step_name) {
                $order++;
                $step = Step::firstOrCreate(
                    ['description' => ucfirst($step_name).' '.implode(' with ', $this->one_or_two_ingredients($ingredients))],
                    [
                        'picture' => $faker->imageUrl(200,200,'abstract'),
                    ]
                );
                
                $recipe->attachStep($step, $order);
            }
        }
        
    }
    
    private function one_or_two_ingredients(array $ingredients)
    {
        $one_or_two_ingredients = [];
        $index = array_rand($ingredients, rand(1,2));
        if (! is_array($index)) {
            $one_or_two_ingredients[0] = $ingredients[$index];
        } else {
            $one_or_two_ingredients[0] = $ingredients[$index[0]];
            $one_or_two_ingredients[1] = $ingredients[$index[1]];
        }
        return $one_or_two_ingredients;
    }
}
