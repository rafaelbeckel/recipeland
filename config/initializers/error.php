<?php

declare(strict_types=1);

use Whoops\Run as ErrorHandler;
use Whoops\Handler\PrettyPageHandler;
use Recipeland\Interfaces\ScreamInterface; // Psr3

return function ($config, ScreamInterface $logger) {
    $handler = new ErrorHandler();

    if (getenv('ENVIRONMENT') != 'production') {
        $handler->pushHandler(new PrettyPageHandler());
    } else {
        $handler->pushHandler(function (Throwable $e) use ($config, $logger) {
            header('Content-type: application/json;charset=utf-8');
            echo $config->get('error.default_message');

            $logger->emergency($e->getMessage(), $e->getTrace());
        });
    }

    return $handler;
};
