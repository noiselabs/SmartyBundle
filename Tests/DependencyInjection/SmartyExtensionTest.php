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
 * @author      Vítor Brandão <vitor@noiselabs.org>
 * @copyright   (C) 2011-2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 * @since       0.1.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\DependencyInjection;

use NoiseLabs\Bundle\SmartyBundle\DependencyInjection\SmartyExtension;
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
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class SmartyExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadEmptyConfiguration()
    {
        $container = $this->createContainer();
        $container->registerExtension(new SmartyExtension());
        $container->loadFromExtension('smarty', array());
        $this->compileContainer($container);

        $this->assertEquals('Smarty', $container->getParameter('smarty.class'), '->load() loads the smarty.xml file');

        // Smarty options
        $options = $container->getParameter('smarty.options');
        $this->assertEquals(__DIR__.'/smarty/cache', $options['cache_dir'], '->load() sets default value for cache_dir option');
        $this->assertEquals(__DIR__.'/smarty/templates_c', $options['compile_dir'], '->load() sets default value for compile_dir option');
        $this->assertEquals(__DIR__.'/config/smarty', $options['config_dir'], '->load() sets default value for config_dir option');
        $this->assertEquals('file', $options['default_resource_type'], '->load() sets default value for default_resource_type option');
        $this->assertEquals(__DIR__.'/Resources/views', $options['template_dir'], '->load() sets default value for template_dir option');
        $this->assertFalse($options['use_include_path'], '->load() sets default value for use_include_path option');
        $this->assertTrue($options['use_sub_dirs'], '->load() sets default value for use_sub_dirs option');
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

        $this->assertEquals('Smarty', $container->getParameter('smarty.class'), '->load() loads the smarty.xml file');

        // Globals
        $calls = $container->getDefinition('templating.engine.smarty')->getMethodCalls();

        $this->assertEquals('foo', $calls[0][1][0], '->load() registers services as Smarty globals');
        $this->assertEquals(new Reference('bar'), $calls[0][1][1], '->load() registers services as Smarty globals');
        $this->assertEquals('pi', $calls[1][1][0], '->load() registers variables as Smarty globals');
        $this->assertEquals(3.14, $calls[1][1][1], '->load() registers variables as Smarty globals');

        // Smarty options
        $options = $container->getParameter('smarty.options');
        $this->assertEquals($appDir.'/cache/smarty/cache', $options['cache_dir'], '->load() sets the cache_dir option');
        $this->assertEquals($appDir.'/cache/smarty/templates_c', $options['compile_dir'], '->load() sets the compile_dir option');
        $this->assertEquals($appDir.'/config/smarty', $options['config_dir'], '->load() sets the config_dir option');
        $this->assertEquals('string', $options['default_resource_type'], '->load() sets the default_resource_type option');
        $this->assertEquals($appDir.'/Resources/views', $options['template_dir'], '->load() sets the template_dir option');
        $this->assertTrue($options['use_include_path'], '->load() sets the use_include_path option');
        $this->assertFalse($options['use_sub_dirs'], '->load() sets the use_sub_dirs option');
    }

    public function testGlobalsWithDifferentTypesAndValues()
    {
        $globals = array(
            'array'   => array(),
            'false'   => false,
            'float'   => 2.0,
            'integer' => 3,
            'null'    => null,
            'object'  => new \stdClass(),
            'string'  => 'foo',
            'true'    => true,
        );

        $container = $this->createContainer();
        $container->registerExtension(new SmartyExtension());
        $container->loadFromExtension('smarty', array('globals' => $globals));
        $this->compileContainer($container);

        $calls = $container->getDefinition('smarty')->getMethodCalls();

        foreach ($calls as $call) {
            list($name, $value) = each($globals);
            $this->assertEquals($name, $call[1][0]);
            $this->assertSame($value, $call[1][1]);
        }
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
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.cache_dir'  => __DIR__,
            'kernel.charset'    => 'UTF-8',
            'kernel.debug'      => false,
            'kernel.root_dir'   => __DIR__,
            'kernel.bundles'    => array()
        )));

        return $container;
    }

    private function compileContainer(ContainerBuilder $container)
    {
        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();
    }

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
                throw new \InvalidArgumentException('Unsupported format: '.$format);
        }

        $loader->load($file.'.'.$format);
    }
}
