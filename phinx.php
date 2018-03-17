<?php

$env = new Dotenv\Dotenv(__DIR__);
$env->load();

return require __DIR__.'/config/db.php';
