<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['Tests/Functional/App', 'Tests/Sandbox', 'tools', 'tmp'])
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
])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
    ;
