<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use Exception;
use Recipeland\Data\User;
use Recipeland\Data\Step;
use Recipeland\Data\Recipe;
use Recipeland\Data\Rating;
use Recipeland\Http\Request;
use Recipeland\Data\Ingredient;
use Recipeland\Traits\CooksRecipes;
use Illuminate\Database\QueryException;
use Recipeland\Http\Request\RateRecipeRequest;
use Recipeland\Http\Request\CreateRecipeRequest;
use Recipeland\Http\Request\UpdateRecipeRequest;
use Recipeland\Http\Request\DeleteRecipeRequest;
use Recipeland\Http\Request\UpdateRecipeFieldsRequest;
use Recipeland\Controllers\AbstractController as Controller;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

class Recipes extends Controller
{
    use CooksRecipes;
    
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
        $data = $request->getParam('recipe');
        $user_id = $jwt->getClaim('user_id');
        $can = (array) $jwt->getClaim('permissions');
        
        if (!($can['create_recipes'] ?? false)) {
            return $this->error('forbidden');
        }
        
        $db = $request->getAttribute('db');
        $recipe = $this->createNewRecipe($db, $data, $user_id);
        
        if (empty($recipe)) {
            return $this->error(
                'internal_server_error',
                'Insert a new Recipe: DB Transaction failed.'
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
        return $this->checkCredentialsAndUpdate($request, $id, ['hard' => true]);
    }

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function updateFields(UpdateRecipeFieldsRequest $request, $id)
    {
        return $this->checkCredentialsAndUpdate($request, $id, ['hard' => false]);
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
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->error('not_found', 'Recipe "'.$id.'" not found!');
        }
        
        if (!$this->can($request, 'delete', $recipe)) {
            return $this->error('forbidden', 'User cannot delete this recipe.');
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
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->error('not_found');
        }
        
        $jwt = $request->getAttribute('jwt');
        $user_id = $jwt->getClaim('user_id');
        $is_author = $user_id == $recipe->created_by;
        if ($is_author) {
            return $this->error('forbidden', 'You cannot rate your own recipe.');
        }
        
        $stars = $request->getParam('rating');
        
        try {
            $rating = new Rating();
            $rating->recipe_id = $id;
            $rating->user_id = $user_id;
            $rating->rating = $stars;
            $rating->save();
        } catch (QueryException $e) {
            return $this->error('forbidden', 'You cannot rate twice.');
        }
        
        $this->setJsonResponse([
            'message' => 'You rated '.$recipe->name.' with '.$stars.' stars!'
        ]);
    }
    
    
    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function getRate(Request $request, $id)
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->error('not_found', "Recipe ".$id." does not exist.");
        }
        
        $rating = Rating::average($id);
        if (!$rating) {
            return $this->error('not_found', "Recipe ".$id." was not rated yet.");
        }
        
        $this->setJsonResponse([
            'recipe' => [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'author' => $recipe->author->name,
                'ratings_count' => $rating['count'],
                'average_rating' => $rating['average'],
            ]
        ]);
    }
    
    
    /**
     * A helper method to check token's permission
     **/
    private function can(Request $request, string $action, Recipe $recipe): bool
    {
        $jwt = $request->getAttribute('jwt');
        $user_id = $jwt->getClaim('user_id');
        $can = (array) $jwt->getClaim('permissions');
        $is_author = $user_id == $recipe->created_by;
        $proceed = ($is_author && ($can[$action.'_own_recipes'] ?? false)) ||
                   (!$is_author && ($can[$action.'_all_recipes'] ?? false));
        
        return $proceed;
    }
    
    private function checkCredentialsAndUpdate(RequestInterface $request, $id, array $options)
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->error('not_found', 'Recipe "'.$id.'" not found!');
        }
        
        if (!$this->can($request, 'edit', $recipe)) {
            return $this->error('forbidden', 'User cannot edit this recipe.');
        }
        
        $last_update = $recipe->updated_at;
        $db = $request->getAttribute('db');
        $data = $request->getParam('recipe');
        $this->updateRecipe($db, $recipe, $data, $options);
        
        if ($recipe->updated_at == $last_update) {
            return $this->error(
                'internal_server_error',
                'Update Recipe: DB Transaction failed.'
            );
        }

        $this->setJsonResponse($recipe->toArray());
    }
}
