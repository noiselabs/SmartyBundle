<?php

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\Filter;

use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\OutputFilter;
use NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\NullExtension;
use PHPUnit\Framework\TestCase;

class OutputFilterTest extends TestCase
{
    public function testGetType()
    {
        $nullExtension = new NullExtension();
        $outputFilter = new OutputFilter('test', $nullExtension, 'none');
        $this->assertSame('output', $outputFilter->getType());
    }
}
