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

namespace NoiseLabs\Bundle\SmartyBundle;

use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\FilterInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\PluginInterface;
use Smarty_Internal_Template as SmartyTemplate;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

/**
 * SmartyEngine is an engine able to render Smarty templates.
 *
 * This class is heavily inspired by \Twig_Environment. 
 * See {@link http://twig.sensiolabs.org/doc/api.html} for details about \Twig_Environment.
 *
 * Thanks to Symfony developer Christophe Coevoet (@stof) for a carefully code
 * review of this bundle.
 *
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class SmartyEngine implements EngineInterface
{
    const TEMPLATE_SUFFIX = 'tpl';

    protected $extensions;
    protected $filters;
    protected $globals;
    protected $loader;
    protected $parser;
    protected $plugins;
    protected $smarty;

    /**
     * Constructor.
     *
     * @param \Smarty                     $smarty    A \Smarty instance
     * @param ContainerInterface          $container A ContainerInterface instance
     * @param TemplateNameParserInterface $parser    A TemplateNameParserInterface instance
     * @param LoaderInterface             $loader    A LoaderInterface instance
     * @param array                       $options   An array of \Smarty properties
     * @param GlobalVariables|null        $globals   A GlobalVariables instance or null
     * @param LoggerInterface|null        $logger    A LoggerInterface instance or null
     */
    public function __construct(\Smarty $smarty, ContainerInterface $container, 
    TemplateNameParserInterface $parser, LoaderInterface $loader, array $options,
    GlobalVariables $globals = null, LoggerInterface $logger = null)
    {
        $this->smarty = $smarty;
        $this->parser = $parser;
        $this->loader = $loader;
        $this->logger = $logger;
        $this->globals = array();

        // There are no default extensions.
        $this->extensions = array();

        /**
         * @warning If you added template dirs to the Smarty instance prior to
         * the loading of this engine these WILL BE LOST because the setter
         * method setTemplateDir() is used below. Please use the following
         * method instead:
         *   $container->get('templating.engine.smarty')->addTemplateDir(
         *   '/path/to/template_dir');
         */
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
        $bundlesTemplateDir = array();

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $name = explode('\\', $bundle);
            $name = end($name);
            $reflection = new \ReflectionClass($bundle);
            if (is_dir($dir = dirname($reflection->getFilename()).'/Resources/views')) {
                $bundlesTemplateDir[$name] = $dir;
            }
       }

        $this->smarty->addTemplateDir($bundlesTemplateDir);

        if (null !== $globals) {
            $this->addGlobal('app', $globals);
        }
    }

    /**
     * Pass methods not available in this engine to the Smarty instance.
     *
     * @since  0.2.0
     * @author Vítor Brandão <vbrandao@nexttoyou.pt>
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->smarty, $name), $args);
    }

    /**
     * Returns the Smarty instance.
     *
     * @since  0.2.0
     * @author Vítor Brandão <vbrandao@nexttoyou.pt>
     */
    public function getSmarty()
    {
        return $this->smarty;
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
        $template = $this->load($name);

        $this->registerFilters();
        $this->registerPlugins();

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
        if ($name instanceof SmartyTemplate) {
            return true;
        }

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
     * @return mixed The resource handle of the template file or template object
     *
     * @throws \InvalidArgumentException if the template cannot be found
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     *
     * @todo Check windows filepaths as defined in
     * {@link http://www.smarty.net/docs/en/resources.tpl#templates.windows.filepath}.
     */
    public function load($name)
    {
        if ($name instanceof SmartyTemplate) {
            return $name;
        }

        $template = $this->parser->parse($name);

        $template = $this->loader->load($template);
        if (false === $template) {
            throw new \InvalidArgumentException(sprintf('The template "%s" does not exist.', $name));
        }

        return (string) $template;
    }

    /**
     * Returns true if the given extension is registered.
     *
     * @param string $name The extension name
     *
     * @return Boolean Whether the extension is registered or not
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function hasExtension($name)
    {
        return isset($this->extensions[$name]);
    }

    /**
     * Gets an extension by name.
     *
     * @param string $name The extension name
     *
     * @return ExtensionInterface An ExtensionInterface instance
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getExtension($name)
    {
        if (!isset($this->extensions[$name])) {
            throw new \InvalidArgumentException(sprintf('The "%s" extension is not enabled.', $name));
        }

        return $this->extensions[$name];
    }

    /**
     * Registers an extension.
     *
     * @param ExtensionInterface $extension An ExtensionInterface instance
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
    }

    /**
     * Removes an extension by name.
     *
     * @param string $name The extension name
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function removeExtension($name)
    {
        unset($this->extensions[$name]);
    }

    /**
     * Registers an array of extensions.
     *
     * @param array $extensions An array of extensions
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = array();

        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    /**
     * Returns all registered extensions.
     *
     * @return array An array of extensions
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Adds a filter to the collection.
     *
     * @param mixed  $filter A FilterInterface instance
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function addFilter(FilterInterface $filter)
    {
        if (null === $this->filters) {
            $this->getFilters();
        }

        $this->filters[] = $filter;
    }

    /**
     * Gets the collection of filters.
     *
     * @return array An array of Filters
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getFilters()
    {
        if (null === $this->filters) {
            $this->filters = array();
            foreach ($this->getExtensions() as $extension) {
                $this->filters = array_merge($this->filters, $extension->getFilters());
            }
        }

        return $this->filters;
    }

    /**
     * Dynamically register filters to Smarty.
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function registerFilters()
    {
        foreach ($this->getFilters() as $filter) {
            try {
                $this->smarty->registerFilter($filter->getType(), $filter->getCallback());
            }
            catch (\SmartyException $e) {
                if (null !== $this->logger) {
                    $this->logger->warn(sprintf("SmartyException caught: %s.", $e->getMessage()));
                }
            }
        }
    }

    /**
     * Adds a plugin to the collection.
     *
     * @param mixed  $plugin A PluginInterface instance
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function addPlugin(PluginInterface $plugin)
    {
        if (null === $this->plugins) {
            $this->getPlugins();
        }

        $this->plugins[] = $plugin;
    }

    /**
     * Gets the collection of plugins.
     *
     * @return array An array of plugins
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getPlugins()
    {
        if (null === $this->plugins) {
            $this->plugins = array();
            foreach ($this->getExtensions() as $extension) {
                $this->plugins = array_merge($this->plugins, $extension->getPlugins());
            }
        }

        return $this->plugins;
    }

    /**
     * Dynamically register plugins to Smarty.
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function registerPlugins()
    {
        foreach ($this->getPlugins() as $plugin) {
            try {
                $this->smarty->registerPlugin($plugin->getType(), $plugin->getName(), $plugin->getCallback());
            }
            catch (\SmartyException $e) {
                if (null !== $this->logger) {
                    $this->logger->warn(sprintf("SmartyException caught: %s.", $e->getMessage()));
                }
            }
        }
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
     * Gets the registered Globals.
     *
     * @return array An array of Globals
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getGlobals($load_extensions = true)
    {
        if (true === $load_extensions) {
            foreach ($this->getExtensions() as $extension) {
                $this->globals = array_merge($this->globals, $extension->getGlobals());
            }
        }

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
