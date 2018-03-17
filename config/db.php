<?php

return [
    // Eloquent -------------------------------------

    'connection' => getenv('DB_CONNECTION'),

    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_NAME'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'port' => getenv('DB_PORT'),
            'charset' => 'utf8',
        ],

        'pgtest' => [
            'driver' => 'pgsql',
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_NAME').'_test',
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'port' => getenv('DB_PORT'),
            'charset' => 'utf8',
        ],
    ],

    // Redis ----------------------------------------

    'redis' => [
        'cluster' => false,
        'default' => [
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'database' => 0,
        ],
    ],

    // Phinx ----------------------------------------

    'paths' => [
        'migrations' => __DIR__.'/../db/migrations',
        'seeds' => __DIR__.'/../db/seeds',
    ],

    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => getenv('ENVIRONMENT'),

        getenv('ENVIRONMENT') => [
            'adapter' => 'pgsql',
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASS'),
            'port' => getenv('DB_PORT'),
            'charset' => 'utf8',
        ],

        'testing' => [
            'adapter' => 'pgsql',
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME').'_test',
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASS'),
            'port' => getenv('DB_PORT'),
            'charset' => 'utf8',
        ],
    ],

    'version_order' => 'creation',
];
