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

use InvalidArgumentException;
use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\AbstractPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\FunctionPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\PluginInterface;
use NoiseLabs\Bundle\SmartyBundle\Templating\GlobalVariables;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Smarty;
use Smarty_Internal_Template;
use Symfony\Component\HttpFoundation\Request;

class SmartyEngineTest extends TestCase
{
    /**
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function testEvaluateAddsAppGlobal()
    {
        $container = $this->createContainer();
        $app = new GlobalVariables($container);
        $engine = $this->getSmartyEngine([], $app);

        $request = $container->get('request');
        $globals = $engine->getGlobals();
        $this->assertSame($app, $globals['app']);
    }

    /**
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function testEvaluateWithoutAvailableRequest()
    {
        $container = $this->createContainer();
        $app = new GlobalVariables($container);
        $engine = $this->getSmartyEngine([], $app);

        $container->set('request', null);

        $globals = $engine->getGlobals();
        $this->assertEmpty($globals['app']->getRequest());
    }

    /**
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function testGlobalVariables()
    {
        $engine = $this->getSmartyEngine();
        $engine->addGlobal('global_variable', 'lorem ipsum');

        $this->assertEquals([
            'global_variable' => 'lorem ipsum',
        ], $engine->getGlobals());
    }

    /**
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function testGlobalsGetPassedToTemplate()
    {
        $engine = $this->getSmartyEngine();
        $engine->setTemplate('global.tpl', '{$global}');
        $engine->addGlobal('global', 'global variable');

        $this->assertEquals($engine->render('global.tpl'), 'global variable');

        $this->assertEquals($engine->render('global.tpl', ['global' => 'overwritten']), 'overwritten');
    }

    /**
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function testGetUnsetExtension()
    {
        $this->expectException(InvalidArgumentException::class);

        $name = 'non-existent-extension';

        $engine = $this->getSmartyEngine();
        $engine->getExtension($name);
    }

    /**
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function testGetSetRemoveExtension()
    {
        $extension = $this->createMock(ExtensionInterface::class);
        $extension->expects($this->any())->method('getName')->will($this->returnValue('mock'));

        $engine = $this->getSmartyEngine();
        $engine->addExtension($extension);

        $this->assertEquals($engine->getExtension('mock'), $extension);

        $this->expectException(InvalidArgumentException::class);
        $engine->removeExtension('mock');
        $engine->getExtension('mock');
    }

    /**
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function testGetSetExtensionsArray()
    {
        $extensions = [];

        foreach (['mock0', 'mock1', 'mock2', 'mock3'] as $name) {
            $extensions[$name] = $this->createMock(ExtensionInterface::class);
            $extensions[$name]->expects($this->any())->method('getName')->will($this->returnValue($name));
        }

        $engine = $this->getSmartyEngine();

        $engine->addExtension($extensions['mock1']);
        $this->assertEquals($engine->getExtension('mock1'), $extensions['mock1']);

        unset($extensions['mock1']);
        $engine->setExtensions($extensions);

        $engine->removeExtension('mock1');
        $this->assertEquals($engine->getExtensions(), $extensions);
    }

    /**
     * test if plugins can be registered between two renders.
     */
    public function testAddPlugin()
    {
        $engine = $this->getSmartyEngine();

        $plugin1 = $this->createMock(PluginInterface::class);
        $plugin1->expects($this->any())->method('getName')->will($this->returnValue('plugin1'));
        $plugin1->expects($this->any())->method('getType')->will($this->returnValue(PluginInterface::TYPE_MODIFIER));
        $plugin1->expects($this->any())->method('getCallback')->will($this->returnValue([$this, 'render']));

        // Register first plugin
        $engine->addPlugin($plugin1);

        $engine->setTemplate('plugin_test_1.tpl', '{$var|plugin1}');
        $engine->addGlobal('var', 'foo');

        $this->assertEquals('foo', $engine->render('plugin_test_1.tpl'));

        $plugin2 = $this->createMock(PluginInterface::class);
        $plugin2->expects($this->any())->method('getName')->will($this->returnValue('plugin2'));
        $plugin2->expects($this->any())->method('getType')->will($this->returnValue(PluginInterface::TYPE_MODIFIER));
        $plugin2->expects($this->any())->method('getCallback')->will($this->returnValue([$this, 'render']));

        // Register second plugin
        $engine->addPlugin($plugin2);

        $engine->setTemplate('plugin_test_2.tpl', '{$foo|plugin1} {$bar|plugin2}');
        $engine->addGlobal('foo', 'foo');
        $engine->addGlobal('bar', 'bar');

        $this->assertEquals('foo bar', $engine->render('plugin_test_2.tpl'));
    }

    public function testCompileTemplate()
    {
        $engine = $this->getSmartyEngine();
        $engine->setTemplate('compile-template.html.smarty', 'test-compile');
        $template = $engine->compileTemplate('compile-template.html.smarty');
        $this->assertInstanceOf(Smarty_Internal_Template::class, $template);
    }

    public function testRenderAndFetchTemplateFunctionWithoutCaching()
    {
        $functionPlugin = $this->createMock(FunctionPlugin::class);
        $functionPlugin->expects($this->any())->method('getName')->will($this->returnValue('sb_test_function'));
        $functionPlugin->expects($this->any())->method('getType')->will($this->returnValue(PluginInterface::TYPE_FUNCTION));
        $functionPlugin->expects($this->any())->method('getCallback')->will($this->returnValue(function (array $params, Smarty_Internal_Template $template) {
            return 'Hello from '.$params['functionName'].' function!';
        }));

        $options = ['caching' => Smarty::CACHING_OFF];
        $engine = $this->getSmartyEngine($options);
        $engine->addPlugin($functionPlugin);
        $engine->registerPlugins();

        $engine->setTemplate('template-function.html.smarty', '');
        $this->assertTrue($engine->exists('template-function.html.smarty'));
        $this->assertTrue($engine->supports('template-function.html.smarty'));
        $content = $engine->fetchTemplateFunction('template-function.html.smarty', 'sb_test_function', ['functionName' => 'test template']);
        $this->assertSame('Hello from test template function!', $content);
    }

    public function render(string $var): string
    {
        return $var;
    }

    /**
     * @since  0.2.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    protected function createContainer(array $data = [])
    {
        $container = parent::createContainer($data);

        $request = new Request();
        $container->set('request', $request);

        return $container;
    }
}

class TestPlugin extends AbstractPlugin
{
    public function getType()
    {
        return 'test';
    }
}
