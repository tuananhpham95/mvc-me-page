<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {// @phpstan-ignore-line
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
