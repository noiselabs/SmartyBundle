<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Templating\Helper;

use NoiseLabs\Bundle\SmartyBundle\Templating\Helper\AssetsHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;

/**
 * @group legacy
 */
class AssetsHelperTest extends TestCase
{
    private $helper;

    protected function setUp(): void
    {
        $fooPackage = new Package(new StaticVersionStrategy('42', '%s?v=%s'));
        $barPackage = new Package(new StaticVersionStrategy('22', '%s?%s'));

        $packages = new Packages($fooPackage, ['bar' => $barPackage]);

        $this->helper = new AssetsHelper($packages);
    }

    public function testGetUrl()
    {
        $this->assertEquals('me.png?v=42', $this->helper->getUrl('me.png'));
        $this->assertEquals('me.png?22', $this->helper->getUrl('me.png', 'bar'));
    }

    public function testGetVersion()
    {
        $this->assertEquals('42', $this->helper->getVersion('/'));
        $this->assertEquals('22', $this->helper->getVersion('/', 'bar'));
    }

    public function testGetName()
    {
        $this->assertSame('assets', $this->helper->getName());
    }
}
