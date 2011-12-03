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
 * Copyright (C) 2011 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @author      Vítor Brandão <noisebleed@noiselabs.org>
 * @copyright   (C) 2011 Vítor Brandão <noisebleed@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 * @since       0.1.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests;

use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Templating\Loader\Loader;
use Symfony\Component\Templating\Storage\StringStorage;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Component\Templating\TemplateReference;

/**
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	protected function setUp()
	{
		if (!class_exists('Smarty')) {
			$this->markTestSkipped('Smarty is not available.');
		}
		
		$this->smarty = $this->getSmarty();
		$this->kernel = $this->getKernel();
		$this->loader = new ProjectTemplateLoader();
	}

	/**
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	protected function tearDown()
	{
		if (!is_dir($compile_dir = $this->smarty->getCompileDir())) {
			return;
		}
		
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($compile_dir), \RecursiveIteratorIterator::CHILD_FIRST);

		foreach ($iterator as $path) {
			if ($path->isDir()) {
				@rmdir($path);
			} else {
				@unlink($path);
			}
		}

        @rmdir($compile_dir);
	}

	public function getSmarty()
	{
		return new \Smarty();
	}
	
	public function getSmartyOptions()
	{
		return array(
			'compile_dir'	=> sys_get_temp_dir().'/noiselabs-smarty-bundle-test/templates_c',
			'template_dir' 	=> __DIR__.'/Fixtures/views'
		);
	}

	/**
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function getSmartyEngine(array $options = array(), $global = null, $logger = null)
	{
		$options = array_merge(
			$this->getSmartyOptions(),
			$options
		);
		
		return new ProjectTemplateEngine(
			$this->smarty,
			$this->kernel,
			new TemplateNameParser(),
			$this->loader,
			$options,
			$global,
			$logger
		);
	}

	/**
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function getKernel()
	{
		return new KernelForTest('test', true);
	}
	
	public function getLoader()
	{
		return ;
	}
}

/**
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
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
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class ProjectTemplateLoader extends Loader
{
	public $templates = array();

	public function setTemplate($name, $content)
	{
		$template = new TemplateReference($name, 'tpl');
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