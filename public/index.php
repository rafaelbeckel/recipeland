<?php

declare(strict_types=1);

/**
 * Register the autoloader.
 */
require __DIR__.'/../bootstrap/Autoload.php';

/**
 * Get our Dependency Injection container.
 */
$container = require __DIR__.'/../bootstrap/Config.php';

/**
 * Get Request object from globals.
 */
$request = require __DIR__.'/../bootstrap/Request.php';

/**
 * Do the Magic.
 */
$app = $container->get(Recipeland\App::class);
$response = $app->go($request);

/*
 * Send response to user
 */
$app->render($response);

/*
 * Run background jobs
 */
// $app->close($request, $response);
