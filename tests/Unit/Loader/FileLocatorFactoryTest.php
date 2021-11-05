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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Loader;

use NoiseLabs\Bundle\SmartyBundle\Loader\FileLocatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\Kernel;

class FileLocatorFactoryTest extends TestCase
{
    public function testItCreatesAFileLocatorInstance()
    {
        $kernel = $this->createMock(Kernel::class);
        $fileLocator = FileLocatorFactory::createFileLocator($kernel, 'path', [], []);
        $this->assertInstanceOf(FileLocator::class, $fileLocator);
    }
}
