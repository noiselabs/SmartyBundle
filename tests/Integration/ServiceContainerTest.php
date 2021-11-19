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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Integration;

use NoiseLabs\Bundle\SmartyBundle\Extension\AssetsExtension;
use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateFinder;
use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateLoader;
use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;
use NoiseLabs\Bundle\SmartyBundle\Templating\GlobalVariables;
use NoiseLabs\Bundle\SmartyBundle\Templating\Helper\ActionsHelper;
use NoiseLabs\Bundle\SmartyBundle\Templating\Loader\FilesystemLoader;
use NoiseLabs\Bundle\SmartyBundle\Templating\Loader\TemplateLocator;
use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateFilenameParser;
use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateNameParser;
use Symfony\Component\Config\FileLocator;

class ServiceContainerTest extends AbstractKernelForTest
{
    public function setUp(): void
    {
        self::bootKernel(['test_case' => 'ServiceContainer']);
    }

    /**
     * @dataProvider servicesDataProvider
     */
    public function testGetSmartyEngineService(string $serviceName, string $className)
    {
        $this->assertHasService($serviceName);
        $this->assertInstanceOf($className, $this->getService($serviceName));
    }

    public function servicesDataProvider()
    {
        return [
            ['smarty.extension.assets', AssetsExtension::class],
            ['smarty.file_locator', FileLocator::class],
            ['smarty.templating.filename_parser', TemplateFilenameParser::class],
            ['smarty.templating.finder', TemplateFinder::class],
            ['smarty.templating.globals', GlobalVariables::class],
            ['smarty.templating.helper.actions', ActionsHelper::class],
            ['smarty.templating.loader', TemplateLoader::class],
            ['smarty.templating.loader.filesystem', FilesystemLoader::class],
            ['smarty.templating.locator', TemplateLocator::class],
            ['smarty.templating.name_parser', TemplateNameParser::class],
            ['templating.engine.smarty', SmartyEngine::class],
        ];
    }

    private function assertHasService(string $serviceName)
    {
        $this->assertTrue(self::$container->has($serviceName));
    }

    public function getService(string $serviceName)
    {
        return self::$container->get($serviceName);
    }
}
