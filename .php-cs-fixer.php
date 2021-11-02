<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['tools', 'tmp'])
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'strict_param' => false,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache')
    ;
