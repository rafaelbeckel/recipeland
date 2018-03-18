<?php

use Recipeland\Routes;
use Recipeland\Http\Router;
use Recipeland\Helpers\Rules\RuleFactory;
use Recipeland\Controllers\ControllerFactory;
use Psr\Container\ContainerInterface as Container;
use Recipeland\Helpers\Validators\RoutesArrayValidator;

return function ($config, Container $c) {
    $routes = new Routes();

    return new Router(
        $routes->get(),
        new ControllerFactory($c),
        new RoutesArrayValidator(
            new RuleFactory()
        ),
        $config->get('cache.files.routes')
    );
};
