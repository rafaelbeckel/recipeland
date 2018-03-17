<?php

use Recipeland\Http\Router;
use Recipeland\Helpers\Rules\RuleFactory;
use Recipeland\Controllers\ControllerFactory;
use Psr\Container\ContainerInterface as Container;
use Recipeland\Helpers\Validators\RoutesArrayValidator;

return function ($config, Container $c) {
    return new Router(
        require $config->get('routes.file'),
        new ControllerFactory($c),
        new RoutesArrayValidator(
            new RuleFactory()
        ),
        $config->get('cache.files.routes')
    );
};
