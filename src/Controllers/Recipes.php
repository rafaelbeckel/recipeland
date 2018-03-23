<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Data\User;
use Recipeland\Data\Step;
use Recipeland\Data\Recipe;
use Recipeland\Http\Request;
use Recipeland\Data\Ingredient;
use Illuminate\Support\Facades\DB;
use Recipeland\Http\Request\RateRecipeRequest;
use Recipeland\Http\Request\CreateRecipeRequest;
use Recipeland\Http\Request\UpdateRecipeRequest;
use Recipeland\Http\Request\DeleteRecipeRequest;
use Recipeland\Http\Request\UpdateRecipeFieldsRequest;
use Recipeland\Controllers\AbstractController as Controller;

class Recipes extends Controller
{
    const RESULTS_PER_PAGE = 10;
    const QUERY_COLUMNS = ['*'];
    const PAGE_NAME = 'page';

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     **/
    public function list(Request $request)
    {
        $page = $request->getQueryParam('page', 1);

        $recipe = Recipe::with('ingredients', 'steps', 'author')
                        ->paginate(
                            self::RESULTS_PER_PAGE,
                            self::QUERY_COLUMNS,
                            self::PAGE_NAME,
                            $page
                        );

        $this->setJsonResponse($recipe->toArray());
    }

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     **/
    public function create(CreateRecipeRequest $request)
    {
        $jwt = $request->getAttribute('jwt');
        $recipe = $request->getParam('recipe');
        $user_id = $jwt->getClaim('user_id');
        $can = (array) $jwt->getClaim('permissions');
        
        if (!($can['create_recipes'] ?? false)) {
            return $this->error('forbidden');
        }
        
        $db = $request->getAttribute('db');
        
        $dbRecipe = $db->transaction(function () use ($recipe, $user_id) {
            $dbRecipe = Recipe::firstOrCreate(
                ['name' => $recipe['name']],
                [
                    'created_by'  => $user_id,
                    'subtitle'    => $recipe['subtitle'],
                    'description' => $recipe['description'],
                    'prep_time'   => $recipe['prep_time'],
                    'total_time'  => $recipe['total_time'],
                    'vegetarian'  => $recipe['vegetarian'],
                    'difficulty'  => $recipe['difficulty'],
                    'picture'     => $recipe['picture'],
                ]
            );
            
            foreach ($recipe['ingredients'] as $ingredient) {
                $dbIngredient = Ingredient::firstOrCreate(
                    ['slug' => $ingredient['slug']],
                    [
                        'name' => $ingredient['name'],
                        'picture' => $ingredient['picture'],
                        'allergens' => $ingredient['allergens'] ?? null,
                    ]
                );
                $dbRecipe->attachIngredient(
                    $dbIngredient,
                    $ingredient['quantity'],
                    $ingredient['unit']
                );
            }
            
            $counter = 1;
            foreach ($recipe['steps'] as $step) {
                $dbStep = Step::firstOrCreate(
                    ['description' => $step['description']],
                    [
                        'picture' => $step['picture'],
                    ]
                );
                $dbRecipe->attachStep($dbStep, $counter);
                $counter++;
            }
            
            return $dbRecipe;
        });
        
        if (empty($dbRecipe)) {
            return $this->error(
                'internal_server_error',
                'Insert a new Recipe: DB Transaction failed.'
            );
        }

        $this->setJsonResponse($dbRecipe->toArray());
    }

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function read(Request $request, $id)
    {
        $recipe = Recipe::with('ingredients', 'steps', 'author')->find($id);

        if (!$recipe) {
            return $this->error('not_found');
        }

        $this->setJsonResponse($recipe->toArray());
    }

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function update(UpdateRecipeRequest $request, $id)
    {
        $jwt = $request->getAttribute('jwt');
        $updated = $request->getParam('recipe');
        $user_id = $jwt->getClaim('user_id');
        
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->error('not_found', 'Recipe "'.$id.'" not found!');
        }
        
        $can = (array) $jwt->getClaim('permissions');
        $is_author = $user_id == $recipe->created_by;
        $can_edit = ($is_author && ($can['edit_own_recipes'] ?? false)) ||
                    (!$is_author && ($can['edit_all_recipes'] ?? false));
        
        if (!$can_edit) {
            return $this->error('forbidden');
        }
        
        $last_update = $recipe->updated_at;
        $db = $request->getAttribute('db');
        $db->transaction(function () use ($recipe, $updated) {
            $recipe->name        = $updated['name'];
            $recipe->subtitle    = $updated['subtitle'];
            $recipe->description = $updated['description'];
            $recipe->prep_time   = $updated['prep_time'];
            $recipe->total_time  = $updated['total_time'];
            $recipe->vegetarian  = $updated['vegetarian'];
            $recipe->difficulty  = $updated['difficulty'];
            $recipe->picture     = $updated['picture'];
            $recipe->save();
            
            $ingredients = [];
            foreach ($updated['ingredients'] as $updatedIngredient) {
                $ingredient = Ingredient::firstOrNew(
                    ['slug' => $updatedIngredient['slug']]
                );
                $ingredient->name = $updatedIngredient['name'];
                $ingredient->picture = $updatedIngredient['picture'];
                $ingredient->allergens = $updatedIngredient['allergens'] ?? null;
                $ingredient->save();
                
                $ingredients[$ingredient->id] = [
                    'quantity' => $updatedIngredient['quantity'],
                    'unit' => $updatedIngredient['unit']
                ];
            }
            // This will rebuild all relationships
            $recipe->ingredients()->sync($ingredients);
            
            $steps = [];
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
            $recipe->steps()->sync($steps);
            
            return $recipe;
        });
        
        if ($recipe->updated_at == $last_update) {
            return $this->error(
                'internal_server_error',
                'Update Recipe: DB Transaction failed.'
            );
        }

        $this->setJsonResponse($recipe->toArray());
    }

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function updateField(UpdateRecipeFieldsRequest $request, $id)
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function delete(DeleteRecipeRequest $request, $id)
    {
        $jwt = $request->getAttribute('jwt');
        $user_id = $jwt->getClaim('user_id');
        
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->error('not_found', 'Recipe "'.$id.'" not found!');
        }
        
        $can = (array) $jwt->getClaim('permissions');
        $is_author = $user_id == $recipe->created_by;
        $can_delete = ($is_author && ($can['delete_own_recipes'] ?? false)) ||
                      (!$is_author && ($can['delete_all_recipes'] ?? false));
        
        if (!$can_delete) {
            return $this->error('forbidden');
        }
        
        $name = $recipe->name;
        $recipe->delete();
        
        $this->setJsonResponse([
            'message' => 'Recipe '.$id.': '.$name.' has been deleted!'
        ]);
    }

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function rate(RateRecipeRequest $request, $id)
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
}
