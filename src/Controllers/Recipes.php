<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Data\Recipe;
use Recipeland\Http\Request;
use Recipeland\Http\Request\CreateRecipeRequest;
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
        $page = $request->getParam('page', 1);

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
