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

namespace NoiseLabs\Bundle\SmartyBundle;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * SmartyEngine is an engine able to render Smarty templates.
 *
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class SmartyEngine implements EngineInterface
{
	const TEMPLATE_SUFFIX = 'tpl';

	protected $globals;
	protected $loader;
	protected $parser;
	protected $smarty;

	/**
	 * Constructor.
	 *
	 * @param \Smarty                     $smarty  A \Smarty instance
	 * @param KernelInterface             $kernel  A KernelInterface instance
	 * @param TemplateNameParserInterface $parser      A TemplateNameParserInterface instance
	 * @param LoaderInterface             $loader  A LoaderInterface instance
	 * @param array                       $options An array of \Smarty properties
	 * @param GlobalVariables|null        $globals A GlobalVariables instance or null
	 */
	public function __construct(\Smarty $smarty, KernelInterface $kernel, TemplateNameParserInterface $parser, LoaderInterface $loader, array $options, GlobalVariables $globals = null)
	{
		$this->smarty = $smarty;
		$this->parser = $parser;
		$this->loader = $loader;
		$this->globals = array();

		foreach ($options as $property => $value) {
			$this->smarty->{$this->smartyPropertyToSetter($property)}($value);
		}

		/**
		 * Register an handler for 'logical' filenames of the type:
		 * <code>file:AcmeHelloBundle:Default:layout.html.tpl</code>
		 */
		$this->smarty->default_template_handler_func = array($this,  'smartyDefaultTemplateHandler');

		/**
		 * Define a set of template dirs to look for. This will allow the
		 * usage of the following syntax:
		 * <code>file:[WebkitBundle]/Default/layout.html.tpl</code>
		 *
		 * See {@link http://www.smarty.net/docs/en/resources.tpl} for details
		 */
		$templatesDir = array();

		foreach ($kernel->getBundles() as $bundle) {
			if (is_dir($path = $bundle->getPath().'/Resources/views/')) {
				$templatesDir[$bundle->getName()] = $path;
			}
		}

		$this->smarty->setTemplateDir(array_merge(
			$this->smarty->getTemplateDir(),
			$templatesDir
		));

		if (null !== $globals) {
			$this->addGlobal('app', $globals);
		}

		$extension = new \NoiseLabs\Bundle\SmartyBundle\Extension\TranslationExtension(1);
	}

    /**
     * Renders a template.
     *
     * @param mixed $name       A template name
     * @param array $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \InvalidArgumentException if the template does not exist
     * @throws \RuntimeException         if the template cannot be rendered
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function render($name, array $parameters = array())
    {
		$template = (string) $this->load($name);

		// attach the global variables
		$parameters = array_replace($this->getGlobals(), $parameters);

		/**
		 * Assign variables/objects to the templates.
		 *
		 * Description
		 *  void assign(mixed var);
		 *  void assign(string varname, mixed var, bool nocache);
		 *
		 * You can explicitly pass name/value pairs, or associative arrays
		 * containing the name/value pairs.
		 *
		 * If you pass the optional third nocache parameter of TRUE, the
		 * variable is assigned as nocache variable. See {@link http://www.smarty.net/docs/en/caching.cacheable.tpl#cacheability.variables} for details.
		 *
		 * Too learn more see {@link http://www.smarty.net/docs/en/api.assign.tpl}
		 */
		foreach ($parameters as $varname => $var) {
			$this->smarty->assign($varname, $var);
		}

		/**
		 * This returns the template output instead of displaying it. Supply a
		 * valid template resource type and path. As an optional second
		 * parameter, you can pass a $cache id, see the caching section for more
		 * information.
		 *
		 * As an optional third parameter, you can pass a $compile_id. This is
		 * in the event that you want to compile different versions of the same
		 * template, such as having separate templates compiled for different
		 * languages. You can also set the $compile_id variable once instead of
		 * passing this to each call to this function.
		 *
		 * Too learn more see {@link http://www.smarty.net/docs/en/api.fetch.tpl}
		 */
        return $this->smarty->fetch($template);
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return Boolean true if the template exists, false otherwise
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function exists($name)
    {
        try {
            $this->load($name);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param string $name A template name
     *
     * @return Boolean True if this class supports the given resource, false otherwise
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function supports($name)
    {
        $template = $this->parser->parse($name);

        return static::TEMPLATE_SUFFIX === $template->get('engine');
    }

    /**
     * Renders a view and returns a Response.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A Response instance
     *
     * @return Response A Response instance
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->render($view, $parameters));

        return $response;
    }

    /**
     * Loads the given template.
     *
     * @param string $name A template name
     *
     * @return Storage A Storage instance
     *
     * @throws \InvalidArgumentException if the template cannot be found
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function load($name)
    {
        $template = $this->parser->parse($name);

        $template = $this->loader->load($template);
        if (false === $template) {
            throw new \InvalidArgumentException(sprintf('The template "%s" does not exist.', $name));
        }

        return $template;
    }

    /**
     * Registers a Global.
     *
     * @param string $name  The global name
     * @param mixed  $value The global value
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
	public function addGlobal($name, $value)
	{
		$this->globals[$name] = $value;
	}

    /**
     * Returns the assigned globals.
     *
     * @return array
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getGlobals()
    {
        return $this->globals;
    }

	/**
	 * This method is called whenever Smarty fails to find a resource. We use
	 * this to load a 'real' template from a 'logical' one.
	 *
	 * To learn more see {@link http://www.smarty.net/docs/en/variable.default.template.handler.func.tpl}
	 *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
	public function smartyDefaultTemplateHandler($type, $name, &$content, &$modified, \Smarty $smarty)
	{
		return ($type == 'file') ? (string) $this->load($name) : false;
	}

	/**
	 * Get the setter method for a Smarty class variable (property).
	 *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
	protected function smartyPropertyToSetter($property)
	{
		$words = explode('_', strtolower($property));

		$setter = 'set';
		foreach ($words as $word) {
			$setter .= ucfirst(trim($word));
		}

		return $setter;
	}
}
