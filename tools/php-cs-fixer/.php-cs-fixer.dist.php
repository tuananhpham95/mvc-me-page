<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/../../src',
        __DIR__ . '/../../tests',
    ]);

$config = new Config();
$config->setRules([
    '@PSR12' => true,
])
->setFinder($finder);

return $config;
