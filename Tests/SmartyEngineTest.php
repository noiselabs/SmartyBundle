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
 * Copyright (C) 2011-2012 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @author      Vítor Brandão <noisebleed@noiselabs.org>
 * @copyright   (C) 2011-2012 Vítor Brandão <noisebleed@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 * @since       0.1.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests;

use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;
use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\FilesystemLoader;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\SessionStorage\ArraySessionStorage;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Templating\Loader\Loader;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class SmartyEngineTest extends TestCase
{
    /**
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testEvaluateAddsAppGlobal()
    {
        $container = $this->getContainer();
        $app = new GlobalVariables($container);
        $engine = $this->getSmartyEngine(array(), $app);

        $request = $container->get('request');
        $globals = $engine->getGlobals();
        $this->assertSame($app, $globals['app']);
    }

    /**
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testEvaluateWithoutAvailableRequest()
    {
        $container = $this->getContainer();
        $app = new GlobalVariables($container);
        $engine = $this->getSmartyEngine(array(), $app);

        $container->set('request', null);

        $globals = $engine->getGlobals();
        $this->assertEmpty($globals['app']->getRequest());
    }

    /**
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testGlobalVariables()
    {
        $engine = $this->getSmartyEngine();
        $engine->addGlobal('global_variable', 'lorem ipsum');

        $this->assertEquals(array(
            'global_variable' => 'lorem ipsum',
        ), $engine->getGlobals());
    }

    /**
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testGlobalsGetPassedToTemplate()
    {
        $engine = $this->getSmartyEngine();
        $engine->setTemplate('global.tpl', '{$global}');
        $engine->addGlobal('global', 'global variable');

        $this->assertEquals($engine->render('global.tpl'), 'global variable');

        $this->assertEquals($engine->render('global.tpl', array('global' => 'overwritten')), 'overwritten');
    }

    /**
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testGetUnsetExtension()
    {
        $this->setExpectedException('InvalidArgumentException');

        $name = 'non-existent-extension';
        
        $engine = $this->getSmartyEngine(); 
        $engine->getExtension($name);
    }

    /**
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testGetSetRemoveExtension()
    {
        $extension = $this->getMock('NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface');
        $extension->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mock'));
            
        $engine = $this->getSmartyEngine();
        $engine->addExtension($extension);
        
        $this->assertEquals($engine->getExtension('mock'), $extension);

        $this->setExpectedException('InvalidArgumentException');
        $engine->removeExtension('mock');
        $engine->getExtension('mock');
    }

    /**
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testGetSetExtensionsArray()
    {
        $extensions = array();

        foreach (array('mock0', 'mock1', 'mock2', 'mock3') as $name) {
            $extensions[$name] = $this->getMock('NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface');
            $extensions[$name]->expects($this->any())
                ->method('getName')
                ->will($this->returnValue($name));
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
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testGetLoader()
    {
        $engine = new ProjectTemplateEngine($this->smarty, $this->container,
        new TemplateNameParser(), $this->loader, array());

        $this->assertSame($this->loader, $engine->getLoader());
    }

    /**
     * Creates a Container with a Session-containing Request service.
     *
     * @return Container
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    protected function getContainer()
    {
        $container = new Container();
        $request = new Request();
        $session = new Session(new ArraySessionStorage());

        $request->setSession($session);
        $container->set('request', $request);

        return $container;
    }
}
