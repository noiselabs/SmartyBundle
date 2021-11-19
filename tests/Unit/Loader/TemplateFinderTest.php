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

use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateFinder;
use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateFilenameParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class TemplateFinderTest extends TestCase
{
    public function testItCanFindAllTemplates()
    {
        $bundle = $this->createMock(BundleInterface::class);
        $bundle->expects($this->any())->method('getName')->willReturn('BarBundle');
        $bundle->expects($this->any())->method('getPath')->willReturn(__DIR__.'/Fixtures/templates/BarBundle');

        $kernel = $this->createMock(Kernel::class);
        $kernel->expects($this->any())->method('getBundles')->willReturn([
            $bundle,
        ]);

        $parser = new TemplateFilenameParser();
        $rootDir = __DIR__.'/Fixtures/templates';
        $smartyOptions['templates_dir'] = [__DIR__.'/Fixtures/templates/Foo'];

        $templateFinder = new TemplateFinder($kernel, $parser, $rootDir, $smartyOptions);
        $templates = $templateFinder->findAllTemplates($bundle);

        self::assertCount(2, $templates);
    }

    public function testItCanFindTemplatesInBundles()
    {
        $bundle = $this->createMock(BundleInterface::class);
        $bundle->expects($this->any())->method('getName')->willReturn('BarBundle');
        $bundle->expects($this->any())->method('getPath')->willReturn(__DIR__.'/Fixtures/templates/BarBundle');

        $kernel = $this->createMock(Kernel::class);
        $kernel->expects($this->any())->method('getBundles')->willReturn([
            $bundle,
        ]);

        $parser = new TemplateFilenameParser();
        $rootDir = __DIR__.'/Fixtures/templates';
        $smartyOptions['template_dir'] = __DIR__.'/Fixtures/templates/Foo';

        $templateFinder = new TemplateFinder($kernel, $parser, $rootDir, $smartyOptions);
        $templates = $templateFinder->findTemplatesInBundle($bundle);

        self::assertCount(1, $templates);
    }

    public function testGetBundleByName()
    {
        $bundle = $this->createMock(BundleInterface::class);
        $bundle->expects($this->any())->method('getName')->willReturn('BarBundle');
        $bundle->expects($this->any())->method('getPath')->willReturn(__DIR__.'/Fixtures/templates/BarBundle');

        $kernel = $this->createMock(Kernel::class);
        $kernel->expects($this->any())->method('getBundle')->willReturn($bundle);

        $parser = new TemplateFilenameParser();
        $rootDir = __DIR__.'/Fixtures/templates';
        $smartyOptions['templates_dir'] = [__DIR__.'/Fixtures/templates/Foo'];

        $templateFinder = new TemplateFinder($kernel, $parser, $rootDir, $smartyOptions);

        self::assertSame($bundle, $templateFinder->getBundle('BarBundle'));
    }
}
