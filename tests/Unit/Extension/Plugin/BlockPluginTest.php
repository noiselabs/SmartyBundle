<?php

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\Plugin;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\AbstractPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use PHPUnit\Framework\TestCase;

class BlockPluginTest extends TestCase
{
    public function testGetType()
    {
        $nullExtension = new NullExtension();
        $blockPlugin = new BlockPlugin('test', $nullExtension, 'none');
        $this->assertSame(AbstractPlugin::TYPE_BLOCK, $blockPlugin->getType());
    }
}
