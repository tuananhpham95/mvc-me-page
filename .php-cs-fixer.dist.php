<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude('var');

return (new Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder);
