<?php

declare(strict_types=1);

namespace Recipeland;

class Routes
{
    protected $routes = [];
    
    public function __construct()
    {
        $this->add( 'GET',    '/',                    'Main.home'           );
        $this->add( 'POST',   '/auth/login',          'Auth\Users.login'    );
        
        $this->add( 'GET',    '/recipes',             'Recipes.list'        );
        $this->add( 'POST',   '/recipes',             'Recipes.create'      );
        $this->add( 'GET',    '/recipes/{id}',        'Recipes.read'        );
        $this->add( 'PUT',    '/recipes/{id}',        'Recipes.update'      );
        $this->add( 'PATCH',  '/recipes/{id}',        'Recipes.updateField' );
        $this->add( 'DELETE', '/recipes/{id}',        'Recipes.delete'      );
        $this->add( 'POST',   '/recipes/{id}/rating', 'Recipes.rate'        );
    }
    
    public function add($method, $path, $destination)
    {
        $this->routes[] = [$method, $path, $destination];
    }
    
    public function get()
    {
        return $this->routes;
    }
}
