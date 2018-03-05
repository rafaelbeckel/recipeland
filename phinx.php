<?php
$env = new Dotenv\Dotenv(__DIR__);
$env->load();

return [

    'paths' => [
        'migrations' => __DIR__ . '/db/migrations',
        'seeds'      => __DIR__ . '/db/seeds'
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
            'adapter' => 'sqlite',
            'memory' => true,
        ]
    ],
    
    'version_order' => 'creation'

];