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
 * Copyright (C) 2011-2018 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2018 Vítor Brandão <vitor@noiselabs.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\RoutingExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use PHPUnit_Framework_Constraint_IsAnything;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test suite for the routing extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class RoutingExtensionTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $templatesDir = __DIR__.'/templates/routing';
        $this->engine->setTemplateDir($templatesDir);
    }

    public function testExtensionName()
    {
        $extension = $this->createRoutingExtension();
        $this->assertEquals('routing', $extension->getName());
    }

    public function testGetPath()
    {
        $extension = $this->createRoutingExtension();
        $this->engine->addExtension($extension);
        $xml = $this->renderXml('path.smarty');
        $this->assertEquals('/blog/my-blog-post', (string) $xml->path[0]);
    }

    public function testGetUrl()
    {
        $extension = $this->createRoutingExtension(UrlGeneratorInterface::ABSOLUTE_URL);
        $this->engine->addExtension($extension);
        $xml = $this->renderXml('url.smarty');
        $this->assertEquals('http://www.example.com/blog/my-blog-post', (string) $xml->url[0]);
    }

    public function createRoutingExtension($referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');

        $url = '/blog/my-blog-post';
        if (UrlGeneratorInterface::ABSOLUTE_URL === $referenceType) {
            $url = 'http://www.example.com/blog/my-blog-post';
        }

        $generator->expects($this->any())
            ->method('generate')
            ->with(
                new PHPUnit_Framework_Constraint_IsAnything(),
                new PHPUnit_Framework_Constraint_IsAnything(),
                $referenceType
            )
            ->will($this->returnValue($url));

        return new RoutingExtension($generator);
    }
}
