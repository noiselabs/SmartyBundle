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

namespace NoiseLabs\Bundle\SmartyBundle;

use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException;
use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\FilterInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\PluginInterface;
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
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class SmartyEngine implements EngineInterface
{
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

        foreach (array('autoload_filters') as $property) {
            if (isset($options[$property])) {
                $this->smarty->$property = $options[$property];
                unset($options[$property]);
            }
        }

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
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->smarty, $name), $args);
    }

    /**
     * Returns the Smarty instance.
     *
     * @return Smarty The Smarty instance
     */
    public function getSmarty()
    {
        $this->registerFilters();
        $this->registerPlugins();
        $this->smarty->assign($this->getGlobals());

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
         $this->smarty->assign($parameters);

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

         try {
            return $this->smarty->fetch($template);
        } catch (\SmartyException $e) {
            throw RuntimeException::createFromPrevious($e, $template);
        } catch (\ErrorException $e) {
            throw RuntimeException::createFromPrevious($e, $template);
        }
    }

    /**
     * Creates a template object.
     *
     * @param mixed   $name A template name
     * @param boolean $load If we should load template content right away. Default: true
     *
     * @return Smarty_Internal_Template
     */
    public function createTemplate($name, $load = true)
    {
        $template = $this->load($name);
        $template = $this->smarty->createTemplate($template, $this->smarty);

        if (true === $load) {
            /**
             * We use `$template->fetch()`because `$template->compileTemplateSource()`
             * doesn't seem to be enough.
             */
            $template->fetch();
        }

        return $template;
    }

    /**
     * Compiles a template object.
     *
     * @param mixed $name A template name
     *
     * @return Smarty_Internal_Template
     */
    public function compileTemplate($name, $forceCompile = false)
    {
        $template = $this->load($name);
        $template = $this->smarty->createTemplate($template, $this->smarty);

        if ($forceCompile || $template->mustCompile()) {
            $template->compileTemplateSource();
        }

        return $template;
    }

    /**
     * Renders the Smarty template function.
     *
     * Thanks to Uwe Tews for providing information on Smarty inner workings
     * allowing the call to the template function from within the plugin:
     * {@link http://stackoverflow.com/questions/9152047/in-smarty3-call-a-template-function-defined-by-the-function-tag-from-within-a}.
     *
     * \Smarty_Internal_Function_Call_Handler is defined in file
     * smarty/libs/sysplugins/smarty_internal_function_call_handler.php.
     *
     * @note The template functions do not return the HTML output, but put it
     * directly into the output buffer.
     *
     * @param Smarty_Internal_Template|string $template   A template object or resource path
     * @param string                          $name       Function name
     * @param array                           $attributes Attributes to pass to the template function
     */
    public function renderTemplateFunction($template, $name, array $attributes = array())
    {
        if (!$template instanceof \Smarty_Internal_Template) {
            $template = $this->createTemplate($template);
        }

        if ($template->caching) {
            \Smarty_Internal_Function_Call_Handler::call ($name, $template, $attributes, $template->properties['nocache_hash'], false);
        } else {
            if (is_callable($function = 'smarty_template_function_'.$name)) {
                $function($template, $attributes);
            } else {
                throw new RuntimeException(sprintf('Template function "%s" is not defined in "%s".', $name, $template->source->filepath), -1, null, $template);
            }
        }
    }

    /**
     * @param Smarty_Internal_Template|string $template   A template object or resource path
     * @param string                          $name       Function name
     * @param array                           $attributes Attributes to pass to the template function
     *
     * @return string The output returned by the template function.
     */
    public function fetchTemplateFunction($template, $name, array $attributes = array())
    {
        ob_start();
        $this->renderTemplateFunction($template, $name, $attributes);
        $html = ob_get_clean();

        return $html;
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return Boolean true if the template exists, false otherwise
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
     */
    public function supports($name)
    {
        if ($name instanceof \Smarty_Internal_Template) {
            return true;
        }

        $template = $this->parser->parse($name);

        // Keep 'tpl' for backwards compatibility.
        return in_array($template->get('engine'), array('smarty', 'tpl'));
    }

    /**
     * Renders a view and returns a Response.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A Response instance
     *
     * @return Response A Response instance
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
     * @todo Check windows filepaths as defined in
     * {@link http://www.smarty.net/docs/en/resources.tpl#templates.windows.filepath}.
     */
    public function load($name)
    {
        if ($name instanceof \Smarty_Internal_Template) {
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
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
    }

    /**
     * Removes an extension by name.
     *
     * @param string $name The extension name
     */
    public function removeExtension($name)
    {
        unset($this->extensions[$name]);
    }

    /**
     * Registers an array of extensions.
     *
     * @param array $extensions An array of extensions
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
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Adds a filter to the collection.
     *
     * @param mixed $filter A FilterInterface instance
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
     */
    public function registerFilters()
    {
        foreach ($this->getFilters() as $filter) {
            try {
                $this->smarty->registerFilter($filter->getType(), $filter->getCallback());
            } catch (\SmartyException $e) {
                if (null !== $this->logger) {
                    $this->logger->warn(sprintf("SmartyException caught: %s.", $e->getMessage()));
                }
            }
        }
    }

    /**
     * Adds a plugin to the collection.
     *
     * @param mixed $plugin A PluginInterface instance
     */
    public function addPlugin(PluginInterface $plugin)
    {
        if (null === $this->plugins) {
            $this->getPlugins();
        }

        $this->plugins[] = $plugin;
    }

    /**
     * Gets the collection of plugins, optionally filtered by an extension
     * name.
     *
     * @return array An array of plugins
     */
    public function getPlugins($extensionName = false)
    {
        if (null === $this->plugins) {
            $this->plugins = array();
            foreach ($this->getExtensions() as $extension) {
                $this->plugins = array_merge($this->plugins, $extension->getPlugins());
            }
        }

        // filter plugins that belong to $extension
        if ($extensionName) {

            $plugins = array();
            foreach (array_keys($this->plugins) as $k) {
                if ($extensionName == $this->plugins[$k]->getExtension()->getName()) {
                    $plugins[] = $this->plugins[$k];
                }
            }

            return $plugins;
        }

        return $this->plugins;
    }

    /**
     * Dynamically register plugins to Smarty.
     */
    public function registerPlugins()
    {
        foreach ($this->getPlugins() as $plugin) {
            try {
                $this->smarty->registerPlugin($plugin->getType(), $plugin->getName(), $plugin->getCallback());
            } catch (\SmartyException $e) {
                if (null !== $this->logger) {
                    $this->logger->debug(sprintf("SmartyException caught: %s.", $e->getMessage()));
                }
            }
        }
    }

    /**
     * Registers a Global.
     *
     * @param string $name  The global name
     * @param mixed  $value The global value
     */
    public function addGlobal($name, $value)
    {
        $this->globals[$name] = $value;
    }

    /**
     * Gets the registered Globals.
     *
     * @return array An array of Globals
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
     */
    public function smartyDefaultTemplateHandler($type, $name, &$content, &$modified, \Smarty $smarty)
    {
        return ($type == 'file') ? (string) $this->load($name) : false;
    }

    /**
     * Get the setter method for a Smarty class variable (property).
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
