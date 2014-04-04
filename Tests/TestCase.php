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
 * @copyright   (C) 2011-2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests;

use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\Loader\Loader;
use Symfony\Component\Templating\Storage\StringStorage;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Component\Templating\TemplateReference;

/**
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Smarty')) {
            $this->markTestSkipped('Smarty is not available.');
        }

        $this->tmpDir = sys_get_temp_dir().'/noiselabs-smarty-bundle-test';

        $this->smarty = $this->getSmarty();
        $this->loader = new ProjectTemplateLoader();
        $this->engine = $this->getSmartyEngine();
    }

    public function tearDown()
    {
        $this->deleteTmpDir();
    }

    protected function deleteTmpDir()
    {
        if (!file_exists($dir = $this->tmpDir)) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    public function getSmarty()
    {
        return new \Smarty();
    }

    public function getSmartyOptions()
    {
        return array(
            'caching'       => false,
            'compile_dir'   => $this->tmpDir.'/templates_c'
        );
    }

    public function createTemplate($filepath)
    {
        return $this->engine->getSmarty()->createTemplate($filepath);
    }

    protected function renderXml($name, $context = array())
    {
        $template = $this->createTemplate($name);

        return new \SimpleXMLElement($this->engine->render($template));
    }

    public function getSmartyEngine(array $options = array(), $global = null, $logger = null)
    {
        $container = $this->createContainer();
        $options = array_merge(
            $this->getSmartyOptions(),
            $options
        );

        return new ProjectTemplateEngine(
            $this->smarty,
            $container,
            new TemplateNameParser(),
            $this->loader,
            $options,
            $global,
            $logger
        );
    }

    public function getKernel()
    {
        return new KernelForTest('test', true);
    }

    protected function createContainer(array $data = array())
    {
        return new ContainerBuilder(new ParameterBag(array_merge(array(
            'kernel.bundles'          => array('SmartyBundle' => 'NoiseLabs\\Bundle\\SmartyBundle\\SmartyBundle'),
            'kernel.cache_dir'        => __DIR__,
            'kernel.compiled_classes' => array(),
            'kernel.debug'            => false,
            'kernel.environment'      => 'test',
            'kernel.name'             => 'kernel',
            'kernel.root_dir'         => __DIR__,
        ), $data)));
    }
}

/**
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class KernelForTest extends Kernel
{
    public function getName()
    {
        return 'testkernel';
    }

    public function registerBundles()
    {
    }

    public function init()
    {
    }

    public function getBundles()
    {
        return array();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}

/**
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class ProjectTemplateEngine extends SmartyEngine
{
    public function setTemplate($name, $content)
    {
        $this->loader->setTemplate($name, $content);
    }

    public function getLoader()
    {
        return $this->loader;
    }
}

/**
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class ProjectTemplateLoader extends Loader
{
    public $templates = array();

    public function setTemplate($name, $content)
    {
        $template = new TemplateReference($name, 'smarty');
        $this->templates[$template->getLogicalName()] = $content;
    }

    public function load(TemplateReferenceInterface $template)
    {
        if (isset($this->templates[$template->getLogicalName()])) {
            $storage = new StringStorage($this->templates[$template->getLogicalName()]);

            return 'string:'.$storage->getContent();
        }

        return false;
    }

    public function isFresh(TemplateReferenceInterface $template, $time)
    {
        return false;
    }
}
