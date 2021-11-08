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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Integration\DependencyInjection;

use Exception;
use NoiseLabs\Bundle\SmartyBundle\DependencyInjection\SmartyExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * DependencyInjection\SmartyExtension tests.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class SmartyExtensionTest extends TestCase
{
    public function testLoadEmptyConfiguration()
    {
        $container = $this->createContainer();
        $container->registerExtension(new SmartyExtension());
        $container->loadFromExtension('smarty', []);
        $this->compileContainer($container);

        // Smarty options
        $options = $container->getParameter('smarty.options');
        self::assertEquals(__DIR__.'/smarty/cache', $options['cache_dir']);
        self::assertEquals(__DIR__.'/smarty/templates_c', $options['compile_dir']);
        self::assertEquals(__DIR__.'/config/smarty', $options['config_dir']);
        self::assertEquals('file', $options['default_resource_type']);
        self::assertFalse($options['use_include_path']);
        self::assertTrue($options['use_sub_dirs']);

        self::assertArrayNotHasKey('template_dir', $options);
        self::assertArrayHasKey('templates_dir', $options);
        self::assertEquals([
            __DIR__.'/templates',
            __DIR__.'/Resources/views',
        ], $options['templates_dir']);
    }

    /**
     * @dataProvider getFormats
     *
     * @param $format
     *
     * @throws Exception
     */
    public function testLoadFullConfiguration($format)
    {
        $appDir = '/tmp/noiselabs-smarty-bundle-test/app';

        $container = $this->createContainer();
        $container->registerExtension(new SmartyExtension());
        $this->loadFromFile($container, 'full', $format);
        $this->compileContainer($container);

        // Globals
        $calls = $container->getDefinition('templating.engine.smarty')->getMethodCalls();
        self::assertEquals('foo', $calls[0][1][0]);
        self::assertEquals(new Reference('bar'), $calls[0][1][1]);
        self::assertEquals('pi', $calls[1][1][0]);
        self::assertEquals(3.14, $calls[1][1][1]);

        // Smarty options
        $options = $container->getParameter('smarty.options');
        self::assertEquals($appDir.'/cache/smarty/cache', $options['cache_dir']);
        self::assertEquals($appDir.'/cache/smarty/templates_c', $options['compile_dir']);
        self::assertEquals($appDir.'/config/smarty', $options['config_dir']);
        self::assertEquals('string', $options['default_resource_type']);
        self::assertEquals([
            '/tmp/noiselabs-smarty-bundle-test/app/Resources/plugins',
            '/tmp/noiselabs-smarty-bundle-test/app/Resources/smarty/plugins',
        ], $options['plugins_dir']);
        self::assertTrue($options['use_include_path']);
        self::assertFalse($options['use_sub_dirs']);

        self::assertArrayNotHasKey('template_dir', $options);
        self::assertArrayHasKey('templates_dir', $options);
        self::assertEquals([
            '/tmp/noiselabs-smarty-bundle-test/app/Resources/views',
            '/tmp/noiselabs-smarty-bundle-test/app/templates_1',
            '/tmp/noiselabs-smarty-bundle-test/app/templates_2',
        ], $options['templates_dir']);
    }

    public function getFormats()
    {
        return [
            ['yml'],
        ];
    }

    private function createContainer()
    {
        return new ContainerBuilder(new ParameterBag([
            'kernel.cache_dir' => __DIR__,
            'kernel.charset' => 'UTF-8',
            'kernel.debug' => false,
            'kernel.root_dir' => __DIR__,
            'kernel.bundles' => [],
        ]));
    }

    private function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();
    }

    /**
     * @param $file
     * @param $format
     *
     * @throws Exception
     */
    private function loadFromFile(ContainerBuilder $container, $file, $format)
    {
        $locator = new FileLocator(__DIR__.'/Fixtures/'.$format);

        switch ($format) {
            case 'php':
                $loader = new PhpFileLoader($container, $locator);

                break;

            case 'xml':
                $loader = new XmlFileLoader($container, $locator);

                break;

            case 'yml':
                $loader = new YamlFileLoader($container, $locator);

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported format: %s', $format));
        }

        $loader->load($file.'.'.$format);
    }
}
