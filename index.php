<?php

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// tramanto de erros.
if ($_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}


include './core/index.php';


loadRoutesRecursively(__DIR__ . '/routes');
loadRoutesRecursively(__DIR__ . '/models');


Router::run();