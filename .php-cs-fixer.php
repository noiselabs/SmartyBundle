<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['cache', 'tests/Sandbox', 'tools', 'tmp'])
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PHP71Migration' => true,
    '@PHP80Migration' => true,
    '@PhpCsFixer' => true,
    '@PSR12' => true,
    'strict_param' => false,
    'array_syntax' => ['syntax' => 'short'],
    'php_unit_internal_class' => false,
    'php_unit_test_class_requires_covers' => false,
])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
    ;
