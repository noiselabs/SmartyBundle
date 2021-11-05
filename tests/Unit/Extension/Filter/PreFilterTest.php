<?php

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\Filter;

use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\PreFilter;
use NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\NullExtension;
use PHPUnit\Framework\TestCase;

class PreFilterTest extends TestCase
{
    public function testGetType()
    {
        $nullExtension = new NullExtension();
        $preFilter = new PreFilter('test', $nullExtension, 'none');
        $this->assertSame('pre', $preFilter->getType());
    }
}
