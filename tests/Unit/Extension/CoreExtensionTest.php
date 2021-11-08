<?php
/*
 * This file is part of the NoiseLabs-SmartyBundle package.
 *
 * Copyright (c) 2011-2021 Vítor Brandão <vitor@noiselabs.io>
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
 */
declare(strict_types=1);

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\CoreExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;

class CoreExtensionTest extends TestCase
{
    public function testExtensionName()
    {
        $extension = $this->createCoreExtension();

        $this->assertEquals('smartybundle_core', $extension->getName());
    }

    public function testGetCharset()
    {
        $extension = $this->createCoreExtension();
        $this->assertSame('UTF-8', $extension->getCharset());

        $extension->setCharset('US-ASCII');
        $this->assertSame('US-ASCII', $extension->getCharset());
    }

    public function testGetPlugins()
    {
        $extension = $this->createCoreExtension();
        $this->assertNotEmpty($extension->getPlugins());
    }

    public function testLenghtModifier()
    {
        $extension = $this->createCoreExtension();
        $this->assertSame(strlen('test-string'), $extension->length_modifier('test-string'));

        $this->assertSame(2, $extension->length_modifier(['test', 'string']));
    }

    protected function createCoreExtension(): CoreExtension
    {
        return new CoreExtension('UTF-8');
    }
}
