<?php

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\Filter;

use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\VariableFilter;
use NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\NullExtension;
use PHPUnit\Framework\TestCase;

class VariableFilterTest extends TestCase
{
    public function testGetType()
    {
        $nullExtension = new NullExtension();
        $variableFilter = new VariableFilter('test', $nullExtension, 'none');
        $this->assertSame('variable', $variableFilter->getType());
    }
}
