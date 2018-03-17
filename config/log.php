<?php

return [
    'access' => [
        'logfile' => __DIR__.'/../storage/log/access.log',
    ],

    'error' => [
        'logfile' => __DIR__.'/../storage/log/error.log',
        'mail' => [
            'to' => getenv('DEV_MAIL'),
            'subject' => 'ERROR: Take Action Now!',
            'from' => getenv('APP_MAIL'),
        ],
    ],
];
