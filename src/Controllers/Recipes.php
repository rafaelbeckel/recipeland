<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Data\User;
use Recipeland\Data\Step;
use Recipeland\Data\Recipe;
use Recipeland\Http\Request;
use Recipeland\Data\Ingredient;
use Illuminate\Support\Facades\DB;
use Recipeland\Http\Request\CreateRecipeRequest;
use Recipeland\Http\Request\DeleteRecipeRequest;
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
        $author = User::find($user_id);
        
        $permissions = $jwt->getClaim('permissions');
        if (!$author || !array_key_exists('create_recipes', $permissions)) {
            return $this->error('unauthorized');
        }
        
        $db = $request->getAttribute('db');
        
        $dbRecipe = $db->transaction(function () use ($recipe, $author) {
            $dbRecipe = Recipe::firstOrCreate(
                ['name' => $recipe['name']],
                [
                    'created_by'  => $author->id,
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
                    $ingredient['units']
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
        $this->setResponseBody('Hi, '.__METHOD__.'!');
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
        $this->setResponseBody('Hi, '.__METHOD__.'!');
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
