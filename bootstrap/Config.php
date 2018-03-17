<?php

declare(strict_types=1);

use Recipeland\Config;

/*
 * Load config files.
 */
$config = new Config(__DIR__.'/../config');

/*
 * Register the global error Handler.
 */
$error = $config->runInitializer('error', $config->runInitializer('log.error'));
error_reporting(E_ALL);
$error->register();

/*
 * Return our PSR-11 dependency injection container.
 */
$container = $config->getInitializer('container');

return $container();
