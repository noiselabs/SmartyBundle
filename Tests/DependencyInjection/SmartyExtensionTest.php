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
 * @author      Vítor Brandão <vitor@noiselabs.io>
 * @copyright   (C) 2011-2018 Vítor Brandão <vitor@noiselabs.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        https://www.noiselabs.io
 * @since       0.1.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\DependencyInjection;

use NoiseLabs\Bundle\SmartyBundle\DependencyInjection\SmartyExtension;
use PHPUnit_Framework_TestCase;
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
class SmartyExtensionTest extends PHPUnit_Framework_TestCase
{
    public function testLoadEmptyConfiguration()
    {
        $container = $this->createContainer();
        $container->registerExtension(new SmartyExtension());
        $container->loadFromExtension('smarty', array());
        $this->compileContainer($container);

        // Smarty options
        $options = $container->getParameter('smarty.options');
        self::assertEquals(__DIR__.'/smarty/cache', $options['cache_dir'], '->load() sets default value for cache_dir option');
        self::assertEquals(__DIR__.'/smarty/templates_c', $options['compile_dir'], '->load() sets default value for compile_dir option');
        self::assertEquals(__DIR__.'/config/smarty', $options['config_dir'], '->load() sets default value for config_dir option');
        self::assertEquals('file', $options['default_resource_type'], '->load() sets default value for default_resource_type option');
        self::assertEquals(__DIR__.'/Resources/views', $options['template_dir'], '->load() sets default value for template_dir option');
        self::assertFalse($options['use_include_path'], '->load() sets default value for use_include_path option');
        self::assertTrue($options['use_sub_dirs'], '->load() sets default value for use_sub_dirs option');
    }

    /**
     * @dataProvider getFormats
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
        self::assertEquals('foo', $calls[0][1][0], '->load() registers services as Smarty globals');
        self::assertEquals(new Reference('bar'), $calls[0][1][1], '->load() registers services as Smarty globals');
        self::assertEquals('pi', $calls[1][1][0], '->load() registers variables as Smarty globals');
        self::assertEquals(3.14, $calls[1][1][1], '->load() registers variables as Smarty globals');

        // Smarty options
        $options = $container->getParameter('smarty.options');
        self::assertEquals($appDir.'/cache/smarty/cache', $options['cache_dir'], '->load() sets the cache_dir option');
        self::assertEquals($appDir.'/cache/smarty/templates_c', $options['compile_dir'], '->load() sets the compile_dir option');
        self::assertEquals($appDir.'/config/smarty', $options['config_dir'], '->load() sets the config_dir option');
        self::assertEquals('string', $options['default_resource_type'], '->load() sets the default_resource_type option');
        self::assertEquals(array(
            '/tmp/noiselabs-smarty-bundle-test/app/Resources/plugins',
            '/tmp/noiselabs-smarty-bundle-test/app/Resources/smarty/plugins'
        ), $options['plugins_dir'], '->load() adds directories to the default list of directories where plugins are stored');
        self::assertEquals($appDir.'/Resources/views', $options['template_dir'], '->load() sets the template_dir option');
        self::assertTrue($options['use_include_path'], '->load() sets the use_include_path option');
        self::assertFalse($options['use_sub_dirs'], '->load() sets the use_sub_dirs option');
    }

    public function getFormats()
    {
        return array(
            //array('php'),
            array('yml'),
            //array('xml'),
        );
    }

    private function createContainer()
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.cache_dir'  => __DIR__,
            'kernel.charset'    => 'UTF-8',
            'kernel.debug'      => false,
            'kernel.root_dir'   => __DIR__,
            'kernel.bundles'    => []
        ]));

        return $container;
    }

    private function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->compile();
    }

    /**
     * @param  ContainerBuilder $container
     * @param $file
     * @param $format
     *
     * @throws \Exception
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
