<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->exclude('vendor')
    ->exclude('coverage')
    ->exclude('.phpunit.cache')
    ->name('*.php');

$config = new PhpCsFixer\Config();
$config->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => true,
    ])
    ->setRiskyAllowed(true)
    ->setUnsupportedPhpVersionAllowed(true);

return $config;
