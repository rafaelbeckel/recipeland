<?php

use Recipeland\App;
use DI\ContainerBuilder;
use Recipeland\Http\Router;
use Psr\Log\LoggerInterface;
use Recipeland\Http\Response\Sender;
use Illuminate\Cache\Repository as Cache;
use Recipeland\Interfaces\ScreamInterface;
use Recipeland\Middleware\MiddlewareStack;
use Recipeland\Middleware\MiddlewareFactory;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Container\ContainerInterface as Container;

return function ($config) {
    $builder = new ContainerBuilder();
    $builder->useAutowiring(true);
    $builder->useAnnotations(false);

    if (getenv('ENVIRONMENT') == 'production') {
        $builder->enableCompilation($config->get('cache.files.dep_inversion'));
    }

    $builder->addDefinitions([
        // Aliases
        'config' => $config->getRepository(),
        'scream' => $config->getInitializer('log.error'),
        'sender' => DI\create(Sender::class),
        'cache' => $config->getInitializer('cache'),
        'log' => $config->getInitializer('log.access'),
        'db' => $config->getInitializer('db'),

        // Static Aliases needed by ACL package
        'facades' => function (Container $c) use ($config) {
            return $config->setFacades([
                'config' => $c->get('config'),
                'cache' => $c->get('cache'),
            ]);
        },

        // Router
        'router' => function (Container $c) use ($config) {
            return $config->runInitializer('router', $c);
        },

        // Middleware Stack
        'stack' => function (Container $c) {
            return new MiddlewareStack(
                new MiddlewareFactory($c, '')
            );
        },

        // App
        App::class => DI\create()->constructor(
            DI\get('router'),
            DI\get('stack'),
            DI\get('sender')
        ),

        // Router
        Router::class => DI\get('router'),

        // Data Sources
        DB::class => DI\get('db'),
        Cache::class => DI\get('cache'),

        // Loggers
        ScreamInterface::class => DI\get('scream'),
        LoggerInterface::class => DI\get('log'),
    ]);

    $container = $builder->build();

    return $container;
};
