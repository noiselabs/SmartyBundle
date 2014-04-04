<?php
/**
 * This file is part of NoiseLabs-SmartyBundle
 *
 * NoiseLabs-SmartyBundle is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * NoiseLabs-SmartyBundle is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with NoiseLabs-SmartyBundle; if not, see
 * <http://www.gnu.org/licenses/>.
 *
 * Copyright (C) 2011-2014 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\AssetsExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Templating\Helper\AssetsHelper;

/**
 * Test suite for the assets extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class AssetsExtensionTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $templatesDir = __DIR__.'/templates/assets';
        $this->engine->setTemplateDir($templatesDir);
    }

    public function testExtensionName()
    {
        $extension = $this->createAssetsExtension();
        $this->assertEquals('assets', $extension->getName());
    }

    public function testGetVersion()
    {
        $extension = $this->createAssetsExtension(null, array(), 'foo');
        $this->engine->addExtension($extension);
        $xml = $this->renderXml('version.smarty');
        $this->assertEquals('foo', (string) $xml->asset);
    }

    public function testGetUrl()
    {
        $extension = $this->createAssetsExtension(null, 'http://assets.example.com/');
        $this->engine->addExtension($extension);
        $xml = $this->renderXml('base_url.smarty');
        $this->assertEquals('http://assets.example.com/foo.js', (string) $xml->asset[0]);
        $this->assertEquals('http://assets.example.com/foo.js', (string) $xml->asset[1]);
    }

    protected function createAssetsExtension($basePath = null, $baseUrls = array(), $version = null, $format = null, $namedPackages = array())
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');
        $helper = new AssetsHelper($basePath, $baseUrls, $version, $format, $namedPackages);

        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValue($helper));

        return new AssetsExtension($container);
    }
}
