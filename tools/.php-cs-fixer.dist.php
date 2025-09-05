<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

// Hitta filer i src och tests, exkludera var och vendor
$finder = Finder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->exclude(['var', 'vendor']);

$config = new Config();
$config->setRules([
    '@PSR12' => true,
])
->setFinder($finder);

return $config;
