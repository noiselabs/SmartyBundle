<?php

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\Filter;

use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\AbstractFilter;
use NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\NullExtension;
use PHPUnit\Framework\TestCase;

class AbstractFilterTest extends TestCase
{
    public function testItReturnsType()
    {
        $nullExtension = new NullExtension();
        $abstractFilter = new TestAbstractWithInvalidTypeFilter('test', $nullExtension, 'noop');
        $this->assertSame('test', $abstractFilter->getType());
    }

    public function testItReturnsAValidCallback()
    {
        $nullExtension = new NullExtension();
        $abstractFilter = new TestAbstractWithInvalidTypeFilter('test', $nullExtension, 'sayHello');
        $callback = $abstractFilter->getCallback();
        $this->assertSame('hello', $callback());
    }

    public function testItValidatesTheTypeWithIncorrectType()
    {
        $nullExtension = new NullExtension();
        $abstractFilter = new TestAbstractWithInvalidTypeFilter('test', $nullExtension, 'sayHello');

        $this->expectException(\RuntimeException::class);
        $abstractFilter->validateType();
    }

    public function testItValidatesTheTypeWithCorrectType()
    {
        $nullExtension = new NullExtension();
        $abstractFilter = new TestAbstractWithValidTypeFilter('test', $nullExtension, '');
        $abstractFilter->validateType();
        $this->addToAssertionCount(1);
    }
}

class TestAbstractWithValidTypeFilter extends AbstractFilter
{
    public function getType()
    {
        return 'output';
    }
}

class TestAbstractWithInvalidTypeFilter extends AbstractFilter
{
    public function getType()
    {
        return 'test';
    }
}
