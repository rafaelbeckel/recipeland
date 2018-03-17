<?php

return [
    'default' => env('CACHE_DRIVER'),

    'stores' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
    ],

    'prefix' => 'recipeland:',

    'files' => [
        'dep_inversion' => __DIR__.'/../storage/cache/di.cache',
        'routes' => __DIR__.'/../storage/cache/routes.cache',
    ],
];
