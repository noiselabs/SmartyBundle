<?php

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\Plugin;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\AbstractPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\CompilerPlugin;
use PHPUnit\Framework\TestCase;

class CompilerPluginTest extends TestCase
{
    public function testGetType()
    {
        $nullExtension = new NullExtension();
        $compilerPlugin = new CompilerPlugin('test', $nullExtension, 'none');
        $this->assertSame(AbstractPlugin::TYPE_COMPILER, $compilerPlugin->getType());
    }
}
