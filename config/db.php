<?php

return [
    
    'connection' => getenv('DB_CONNECTION'),
    
    'connections' => [
        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => getenv('DB_HOST'),
            'database' => getenv('DB_NAME'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'port'     => getenv('DB_PORT'),
            'charset'  => 'utf8'
        ],
        
        'pgtest' => [
            'driver'   => 'pgsql',
            'host'     => getenv('DB_HOST'),
            'database' => getenv('DB_NAME').'_test',
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'port'     => getenv('DB_PORT'),
            'charset'  => 'utf8'
        ],
    ],
    
    'redis' => [
        'cluster' => false,
        'default' => [
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
    ]
];