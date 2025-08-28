<?php

use Symfony\Component\Dotenv\Dotenv;

$mask = 0;
require dirname(__DIR__) . '/vendor/autoload.php';
umask(0);
if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}
