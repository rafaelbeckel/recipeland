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
use Recipeland\Http\Request\SearchRecipeRequest;
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
     * Lists all recipes in JSON format
     * 
     * The recipes are always paginated.
     * This method accepts the page number 
     * as an input via URL query parameters.
     *
     * @params ServerRequestInterface
     **/
    public function list(Request $request)
    {
        $recipes = $this->getRecipesFromCacheOrDB($request);
        $this->setJsonResponse($recipes->toArray());
    }

    /**
     * Creates a new recipe.
     * 
     * This is a protected method that accepts
     * only pre-validated signed Requests.
     *
     * @params CreateRecipeRequest
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
            // @codeCoverageIgnoreStart
            return $this->error(
                'internal_server_error',
                'Insert a new Recipe: DB Transaction failed.'
            );
            // @codeCoverageIgnoreEnd
        }
        
        $this->setJsonResponse($recipe->toArray());
    }

    /**
     * Read a given recipe by id
     * 
     * This method shows a recipe from a given id.
     * It should receive only the recipe id 
     * as a parameter directly via URL.
     *
     * @params ServerRequestInterface
     * @params integer|string $id
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
     * Replaces a selected recipe by a new provided one.
     * 
     * This is a protected method that accepts
     * only pre-validated signed Requests.
     * 
     * It should receive all fields in 
     * one call via a PUT request.
     *
     * @params UpdateRecipeRequest
     * @params integer $id
     **/
    public function update(UpdateRecipeRequest $request, $id)
    {
        return $this->checkCredentialsAndUpdate($request, $id, ['hard' => true]);
    }

    /**
     * Updates some fields in the selected recipe.
     * 
     * This is a protected method that accepts
     * only pre-validated signed Requests.
     * 
     * It should receive a list of fields 
     * to be updated via a PATCH request.
     * 
     * @params UpdateRecipeFieldsRequest
     * @params integer $id
     **/
    public function updateFields(UpdateRecipeFieldsRequest $request, $id)
    {
        return $this->checkCredentialsAndUpdate($request, $id, ['hard' => false]);
    }

    /**
     * Deletes the selected Recipe.
     * 
     * This is a protected method that accepts
     * only pre-validated signed Requests.
     * 
     * It should receive only the recipe id 
     * as a parameter directly via URL.
     *
     * @params DeleteRecipeRequest
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
     * Creates a rating for a given recipe.
     *
     * This is a protected method that accepts
     * only pre-validated signed Requests.
     * 
     * @params RateRecipeRequest
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
     * Searches the recipes database
     * 
     * It can filter the recipes by:
     * - Query string (searches name, subtitle AND description)
     * - Author
     * - Rating
     * - Preparation time
     * - Total time
     * - Vegetarian flag
     * - Difficulty
     **/
    public function search(SearchRecipeRequest $request)
    {
        $page = $request->getQueryParam('page', 1);
        $input = $request->getQueryParam('query', null);
        $author = $request->getQueryParam('author', null);
        $rating = $request->getQueryParam('rating', null);
        $prep_time = $request->getQueryParam('prep_time', null);
        $total_time = $request->getQueryParam('total_time', null);
        $vegetarian = $request->getQueryParam('vegetarian', null);
        $difficulty = $request->getQueryParam('difficulty', null);
        
        $users = $author ? User::where('name', 'ilike', '%'.$author.'%')
                               ->pluck('id')
                               ->toArray() : null;
        
        $results = Recipe::with('ingredients', 'steps', 'author')
            ->where(function ($q) use ($input) {
                return $q->orWhere('name', 'ilike', '%'.$input.'%')
                         ->orWhere('subtitle', 'ilike', '%'.$input.'%')
                         ->orWhere('description', 'ilike', '%'.$input.'%');
            })
            ->when($users, function ($q) use ($users) {
                return $q->whereIn('created_by', $users);
            })
            ->when(!is_null($vegetarian), function ($q) use ($vegetarian) {
                return $q->where('vegetarian', $vegetarian);
            })
            ->when(!is_null($rating), function ($q) use ($rating) {
                return $this->addFilters('rating', $rating, $q);
            })
            ->when(!is_null($difficulty), function ($q) use ($difficulty) {
                return $this->addFilters('difficulty', $difficulty, $q);
            })
            ->when(!is_null($prep_time), function ($q) use ($prep_time) {
                return $this->addFilters('prep_time', $prep_time, $q);
            })
            ->when(!is_null($total_time), function ($q) use ($total_time) {
                return $this->addFilters('total_time', $total_time, $q);
            })
            ->paginate(
                self::RESULTS_PER_PAGE,
                self::QUERY_COLUMNS,
                self::PAGE_NAME,
                $page
            );
        
        $this->setJsonResponse($results->toArray());
    }
    
    /**
     * A helper method to check token's permission for editing and deleting
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
    
    /**
     * Gets the recipe collection from cache or DB
     * 
     * @param ServerRequestInterface $request
     **/
    private function getRecipesFromCacheOrDB(Request $request)
    {
        $force = $request->getQueryParam('force', false);
        $page = $request->getQueryParam('page', 1);
        $cache = $request->getAttribute('cache');
        
        if ($cache && !$force) {
            return $cache->tags(['recipes_pages'])
                         ->rememberForever('recipes_page'.$page, function () use ($page) {
                             return $this->getRecipesFromPage($page);
                         });
        } else {
            // @codeCoverageIgnoreStart
            return $this->getRecipesFromPage($page);
            // @codeCoverageIgnoreEnd
        }
    }
    
    private function getRecipesFromPage($page)
    {
        return Recipe::with('ingredients', 'steps', 'author')->paginate(
            self::RESULTS_PER_PAGE,
            self::QUERY_COLUMNS,
            self::PAGE_NAME,
            $page
        );
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
            // @codeCoverageIgnoreStart
            return $this->error(
                'internal_server_error',
                'Update Recipe: DB Transaction failed.'
            );
            // @codeCoverageIgnoreEnd
        }

        $this->setJsonResponse($recipe->toArray());
    }
    
    private function addFilters($key, $value, &$query)
    {
        if (is_numeric($value)) {
            $query->where($key, $value);
        } else {
            $json = json_decode($value, true);
            $gt = $json['gt'] ?? null;
            $lt = $json['lt'] ?? null;
            $gte = $json['gte'] ?? null;
            $lte = $json['lte'] ?? null;
            if ($json) {
                $query->when($gt, function ($q) use ($key, $gt) {
                    return $q->where($key, '>', $gt);
                })->when($lt, function ($q) use ($key, $lt) {
                    return $q->where($key, '<', $lt);
                })->when($gte, function ($q) use ($key, $gte) {
                    return $q->where($key, '>=', $gte);
                })->when($lte, function ($q) use ($key, $lte) {
                    return $q->where($key, '<=', $lte);
                });
            }
        }
    }
}
