<?php

/**
 * Register the autoloader
 */
require __DIR__ . '/../vendor/autoload.php';

/**
 * Read environment variables
 */
$env = new Dotenv\Dotenv(__DIR__.'/..');
$env->load();