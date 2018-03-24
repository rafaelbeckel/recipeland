<?php

namespace Recipeland\Traits;

use Recipeland\Data\Step;
use Recipeland\Data\Recipe;
use Recipeland\Data\Ingredient;
use Illuminate\Database\Connection as DB;

trait CooksRecipes
{
    private function createNewRecipe(DB $db, array $data, int $author_id): ?Recipe
    {
        $recipe = $db->transaction(function () use ($data, $author_id) {
            $recipe = Recipe::firstOrCreate(
                ['name' => $data['name']],
                [
                    'created_by'  => $author_id,
                    'subtitle'    => $data['subtitle'],
                    'description' => $data['description'],
                    'prep_time'   => $data['prep_time'],
                    'total_time'  => $data['total_time'],
                    'vegetarian'  => $data['vegetarian'],
                    'difficulty'  => $data['difficulty'],
                    'picture'     => $data['picture'],
                ]
            );
            
            foreach ($data['ingredients'] as $ingredient) {
                $dbIngredient = Ingredient::firstOrCreate(
                    ['slug' => $ingredient['slug']],
                    [
                        'name' => $ingredient['name'],
                        'picture' => $ingredient['picture'],
                        'allergens' => $ingredient['allergens'] ?? null,
                    ]
                );
                $recipe->attachIngredient(
                    $dbIngredient,
                    $ingredient['quantity'],
                    $ingredient['unit']
                );
            }
            
            $counter = 1;
            foreach ($data['steps'] as $step) {
                $dbStep = Step::firstOrCreate(
                    ['description' => $step['description']],
                    [
                        'picture' => $step['picture'],
                    ]
                );
                $recipe->attachStep($dbStep, $counter);
                $counter++;
            }
            
            return $recipe;
        });
        
        return $recipe;
    }
    
    private function updateRecipe(DB $db, Recipe &$recipe, array $updated, array $options = [])
    {
        $hard = $options['hard'] ?? false;
        
        $db->transaction(function () use ($recipe, $updated, $hard) {
            $recipe->name        = $updated['name']        ?? $recipe->name;
            $recipe->subtitle    = $updated['subtitle']    ?? $recipe->subtitle;
            $recipe->description = $updated['description'] ?? $recipe->description;
            $recipe->prep_time   = $updated['prep_time']   ?? $recipe->prep_time;
            $recipe->total_time  = $updated['total_time']  ?? $recipe->total_time;
            $recipe->vegetarian  = $updated['vegetarian']  ?? $recipe->vegetarian;
            $recipe->difficulty  = $updated['difficulty']  ?? $recipe->difficulty;
            $recipe->picture     = $updated['picture']     ?? $recipe->picture;
            $recipe->save();
            
            $ingredients = [];
            if (!empty($updated['ingredients'])) {
                foreach ($updated['ingredients'] as $updatedIngredient) {
                    $ingredient = Ingredient::firstOrNew(
                        ['slug' => $updatedIngredient['slug']]
                    );
                    $ingredient->name = $updatedIngredient['name'] ?? $ingredient->name;
                    $ingredient->picture = $updatedIngredient['picture'] ?? $ingredient->picture;
                    $ingredient->allergens = $updatedIngredient['allergens'] ?? $ingredient->allergens;
                    $ingredient->save();
                    
                    $ingredients[$ingredient->id] = [
                        'quantity' => $updatedIngredient['quantity'],
                        'unit' => $updatedIngredient['unit']
                    ];
                }
            }
            
            if ($hard) { // This will erase and rebuild all relationships
                $recipe->ingredients()->sync($ingredients);
            } else {
                $recipe->ingredients()->syncWithoutDetaching($ingredients);
            }
            
            $steps = [];
            if (!empty($updated['steps'])) {
                $counter = 1;
                foreach ($updated['steps'] as $updatedStep) {
                    $step = Step::firstOrNew(
                        ['description' => $updatedStep['description']]
                    );
                    $step->picture = $updatedStep['picture'];
                    $step->save();
                    
                    $steps[$step->id] = [
                        'order' => $counter
                    ];
                    
                    $counter++;
                }
            }
            
            if ($hard) { // This will erase and rebuild all relationships
                $recipe->steps()->sync($steps);
            } else {
                $recipe->steps()->syncWithoutDetaching($steps);
            }
        });
    }
}
