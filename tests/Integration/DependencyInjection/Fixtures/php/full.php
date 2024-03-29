<?php

$container->loadFromExtension('twig', [
    'form' => [
        'resources' => [
            'MyBundle::form.html.twig',
        ],
    ],
    'globals' => [
        'foo' => '@bar',
        'baz' => '@@qux',
        'pi' => 3.14,
        'bad' => ['key' => 'foo'],
    ],
    'auto_reload' => true,
    'autoescape' => true,
    'base_template_class' => 'stdClass',
    'cache' => '/tmp',
    'charset' => 'ISO-8859-1',
    'debug' => true,
    'strict_variables' => true,
    'paths' => [
        'path1',
        'path2',
        'namespaced_path1' => 'namespace',
        'namespaced_path2' => 'namespace',
    ],
]);
