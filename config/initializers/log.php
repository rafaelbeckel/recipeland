<?php

declare(strict_types=1);

use Monolog\Logger;
use Recipeland\Helpers\Screamer;
use Monolog\Handler\StreamHandler as Stream;
use Monolog\Formatter\JsonFormatter as Json;
use Monolog\Handler\NativeMailerHandler as Mail;

return [
    'access' => function ($config) {
        $accessLogger = new Logger('access');
        $handler = new Stream($config->get('log.access.logfile'));
        $handler->setFormatter(new Json());
        $accessLogger->pushHandler($handler);

        return $accessLogger;
    },

    'error' => function ($config) {
        $errorLogger = new Screamer('error');
        $handler = new Stream($config->get('log.error.logfile'), Logger::ERROR);
        $handler->setFormatter(new Json());
        $errorLogger->pushHandler($handler);

        if (getenv('ENVIRONMENT') == 'production') {
            $errorLogger->pushHandler(new Mail(
                                        $config->get('log.error.mail.to'),
                                        $config->get('log.error.mail.subject'),
                                        $config->get('log.error.mail.from')
                                     ));
        }

        return $errorLogger;
    },
];
