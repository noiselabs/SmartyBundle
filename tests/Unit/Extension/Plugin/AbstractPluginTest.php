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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\Plugin;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\AbstractPlugin;
use NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension\NullExtension;
use PHPUnit\Framework\TestCase;

class AbstractPluginTest extends TestCase
{
    public function testItReturnsType()
    {
        $nullExtension = new NullExtension();
        $abstractPlugin = new TestAbstractWithInvalidTypePlugin('test', $nullExtension, 'noop');
        $this->assertSame('test', $abstractPlugin->getType());
    }

    public function testItCanSetANameAfterInstantiation()
    {
        $nullExtension = new NullExtension();
        $abstractPlugin = new TestAbstractWithInvalidTypePlugin('test', $nullExtension, 'noop');
        $newName = 'a_different_plugin_name';
        $abstractPlugin->setName($newName);
        $this->assertSame($newName, $abstractPlugin->getName());
    }

    public function testItReturnsTheExtension()
    {
        $nullExtension = new NullExtension();
        $abstractPlugin = new TestAbstractWithInvalidTypePlugin('test', $nullExtension, 'sayHello');
        $this->assertSame($nullExtension, $abstractPlugin->getExtension());
    }

    public function testItReturnsAValidCallback()
    {
        $nullExtension = new NullExtension();
        $abstractPlugin = new TestAbstractWithInvalidTypePlugin('test', $nullExtension, 'sayHello');
        $callback = $abstractPlugin->getCallback();
        $this->assertSame('hello', $callback());
    }

    public function testItValidatesTheTypeWithIncorrectType()
    {
        $nullExtension = new NullExtension();
        $abstractPlugin = new TestAbstractWithInvalidTypePlugin('test', $nullExtension, 'sayHello');

        $this->expectException(\RuntimeException::class);
        $abstractPlugin->validateType();
    }

    public function testItValidatesTheTypeWithCorrectType()
    {
        $nullExtension = new NullExtension();
        $abstractPlugin = new TestAbstractWithValidTypePlugin('test', $nullExtension, '');
        $abstractPlugin->validateType();
        $this->addToAssertionCount(1);
    }
}

class TestAbstractWithValidTypePlugin extends AbstractPlugin
{
    public function getType()
    {
        return AbstractPlugin::TYPE_BLOCK;
    }
}

class TestAbstractWithInvalidTypePlugin extends AbstractPlugin
{
    public function getType()
    {
        return 'test';
    }
}
