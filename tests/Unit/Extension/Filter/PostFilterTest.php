<?php

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\Filter;

use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\PostFilter;
use NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\NullExtension;
use PHPUnit\Framework\TestCase;

class PostFilterTest extends TestCase
{
    public function testGetType()
    {
        $nullExtension = new NullExtension();
        $postFilter = new PostFilter('test', $nullExtension, 'none');
        $this->assertSame('post', $postFilter->getType());
    }
}
