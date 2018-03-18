<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Controllers\AbstractController as Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Recipeland\Http\Request\CreateRecipeRequest;
use Recipeland\Data\Recipe;

class Recipes extends Controller
{
    const RESULTS_PER_PAGE = 10;
    const QUERY_COLUMNS = ['*'];
    const PAGE_NAME = 'page';

    protected $middleware = [
        'all' => [
            'Some\Middleware',
            'Some\Other\Middleware',
        ],

        'create' => [
            'Middlewares\Auth\UserIdentity',
            'Middlewares\Roles\CreateRecipe',
        ],

        'update' => [
            'Middlewares\Roles\CreateRecipe',
        ],
    ];

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function list(Request $request)
    {
        $page = $this->getQueryParam('page', 1);

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
     * @params integer $id
     **/
    public function create(CreateRecipeRequest $request)
    {
        $recipe = Recipe::firstOrCreate();

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

        $this->setJsonResponse($recipe->toArray());
    }

    /**
     * @description
     * Lists the recipes
     *
     * @params ServerRequestInterface
     * @params integer $id
     **/
    public function update(Request $request, $id)
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
    public function updateField(Request $request, $id)
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
    public function delete(Request $request, $id)
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
    public function rate(Request $request, $id)
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
}
