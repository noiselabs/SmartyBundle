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

use NoiseLabs\Bundle\SmartyBundle\Extension\ExpressionExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\ExpressionLanguage\Expression;

class ExpressionExtensionTest extends TestCase
{
    public function testExtensionName()
    {
        $extension = $this->createExpressionExtension();

        $this->assertEquals('expression', $extension->getName());
    }

    public function testCreateExpression()
    {
        $extension = $this->createExpressionExtension();
        $this->assertInstanceOf(Expression::class, $extension->createExpression('test_expression'));
    }

    public function testGetPlugins()
    {
        $extension = $this->createExpressionExtension();
        $this->assertNotEmpty($extension->getPlugins());
    }

    protected function createExpressionExtension(): ExpressionExtension
    {
        return new ExpressionExtension();
    }
}
