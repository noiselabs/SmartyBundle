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
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\ActionsExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\HttpKernel\Kernel as Symfony;

/**
 * Test suite for the actions extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class ActionsExtensionTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $templatesDir = __DIR__.'/templates/actions';
        $this->engine->setTemplateDir($templatesDir);
    }

    public function testExtensionName()
    {
        $extension = $this->createActionsExtension();

        $this->assertEquals('actions', $extension->getName());
    }

    protected function createActionsExtension()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        return new ActionsExtension($container);
    }
}
