<?php

declare(strict_types=1);

namespace Recipeland;

/**
 * Defines all the routes in our Application.
 *
 * This class will be injected in the Router
 * to map URL endpoints to Controller classes.
 *
 * By default, the root controller namespace
 * is \Recipeland\Controllers, so our routes
 * do not need to declare the full namespace.
 */
class Routes
{
    protected $routes = [];
    
    public function __construct()
    {
        $this->add( 'GET',    '/',                    'Main.home'            );
        $this->add( 'POST',   '/auth/login',          'Auth\Users.login'     );
        
        $this->add( 'GET',    '/recipes',             'Recipes.list'         );
        $this->add( 'POST',   '/recipes',             'Recipes.create'       );
        $this->add( 'GET',    '/recipes/search',      'Recipes.search'       );
        $this->add( 'GET',    '/recipes/{id}',        'Recipes.read'         );
        $this->add( 'PUT',    '/recipes/{id}',        'Recipes.update'       );
        $this->add( 'PATCH',  '/recipes/{id}',        'Recipes.updateFields' );
        $this->add( 'DELETE', '/recipes/{id}',        'Recipes.delete'       );
        $this->add( 'POST',   '/recipes/{id}/rating', 'Recipes.rate'         );
    }
    
    public function add(string $method, string $path, string $destination): void
    {
        $this->routes[] = [$method, $path, $destination];
    }
    
    public function get(): array
    {
        return $this->routes;
    }
}
