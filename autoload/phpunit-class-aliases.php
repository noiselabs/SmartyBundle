<?php

if (!class_exists('PHPUnit_Framework_Assert')) {
    class_alias(PHPUnit\Framework\Assert::class, 'PHPUnit_Framework_Assert');
}

if (!class_exists('PHPUnit_Framework_TestCase')) {
    class_alias(PHPUnit\Framework\TestCase::class, 'PHPUnit_Framework_TestCase');
}

if (!class_exists('PHPUnit_Framework_ExpectationFailedException')) {
    class_alias(PHPUnit\Framework\ExpectationFailedException::class, 'PHPUnit_Framework_ExpectationFailedException');
}

if (!class_exists('PHPUnit_Framework_Constraint_IsAnything')) {
    class_alias(PHPUnit\Framework\Constraint\IsAnything::class, 'PHPUnit_Framework_Constraint_IsAnything');
}