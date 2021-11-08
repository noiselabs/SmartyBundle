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

namespace NoiseLabs\Bundle\SmartyBundle;

use InvalidArgumentException;
use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException;
use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\FilterInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\PluginInterface;
use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateLoader;
use NoiseLabs\Bundle\SmartyBundle\Templating\GlobalVariables;
use Psr\Log\LoggerInterface;
use Smarty;
use Smarty_Internal_Runtime_TplFunction;
use Smarty_Internal_Template;
use SmartyException;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * SmartyEngine is an engine able to render Smarty templates.
 *
 * This class is heavily inspired by \Twig_Environment.
 * See {@link http://twig.sensiolabs.org/doc/api.html} for details about \Twig_Environment.
 *
 * Thanks to Symfony developer Christophe Coevoet (@stof) for a thorough code review of this bundle.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class SmartyEngine implements EngineInterface
{
    /**
     * @var ExtensionInterface[]
     */
    protected $extensions;

    /**
     * @var FilterInterface[]
     */
    protected $filters;

    /**
     * @var array
     */
    protected $globals;

    /**
     * @var PluginInterface[]
     */
    protected $plugins;

    /**
     * @var Smarty
     */
    protected $smarty;

    /**
     * @var null|LoggerInterface
     */
    protected $logger;

    /**
     * @var TemplateLoader
     */
    private $templateLoader;

    /**
     * Constructor.
     *
     * @param Smarty               $smarty    A \Smarty instance
     * @param ContainerInterface   $container A ContainerInterface instance
     * @param array                $options   An array of \Smarty properties
     * @param null|GlobalVariables $globals   A GlobalVariables instance or null
     * @param null|LoggerInterface $logger    A LoggerInterface instance or null
     *
     * @throws \ReflectionException
     */
    public function __construct(
        Smarty $smarty,
        TemplateLoader $templateLoader,
        ContainerInterface $container,
        array $options,
        GlobalVariables $globals = null,
        LoggerInterface $logger = null
    ) {
        $this->smarty = $smarty;
        $this->templateLoader = $templateLoader;
        $this->logger = $logger;
        $this->globals = [];

        // There are no default extensions.
        $this->extensions = [];

        foreach (['autoload_filters'] as $property) {
            if (isset($options[$property])) {
                $this->smarty->{$property} = $options[$property];
                unset($options[$property]);
            }
        }

        $extraTemplateDirs = $options['templates_dir'] ?? [];
        unset($options['templates_dir']);

        // addSomeProperty()
        foreach (['plugins_dir', 'template_dir'] as $property) {
            if (!isset($options[$property])) {
                continue;
            }

            $value = $options[$property];
            $this->smarty->{$this->smartyPropertyToSetter($property, 'add')}($value);
            unset($options[$property]);
        }

        // setSomeProperty()
        foreach ($options as $property => $value) {
            $this->smarty->{$this->smartyPropertyToSetter($property, 'set')}($value);
        }

        /*
         * Register an handler for 'logical' filenames of the type:
         * <code>file:AcmeHelloBundle:Default:layout.html.tpl</code>
         */
        $this->smarty->default_template_handler_func = [$this, 'smartyDefaultTemplateHandler'];

        /**
         * Define a set of template dirs to look for. This will allow the
         * usage of the following syntax:
         * <code>file:[WebkitBundle]/Default/layout.html.tpl</code>.
         *
         * See {@link http://www.smarty.net/docs/en/resources.tpl} for details
         */
        $bundlesTemplateDir = [];

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $name = explode('\\', $bundle);
            $name = end($name);
            $reflection = new \ReflectionClass($bundle);
            if (is_dir($dir = dirname($reflection->getFilename()).'/Resources/views')) {
                $bundlesTemplateDir[$name] = $dir;
            }
        }
        $this->smarty->addTemplateDir($bundlesTemplateDir);
        foreach ($extraTemplateDirs as $templateDir) {
            $this->smarty->addTemplateDir($templateDir);
        }

        if (null !== $globals) {
            $this->addGlobal('app', $globals);
        }
    }

    /**
     * Pass methods not available in this engine to the Smarty instance.
     *
     * @param mixed $name
     * @param mixed $args
     */
    public function __call($name, $args)
    {
        return call_user_func_array([$this->smarty, $name], $args);
    }

    /**
     * Returns the Smarty instance.
     *
     * @return Smarty The Smarty instance
     */
    public function getSmarty(): Smarty
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
     * @throws RuntimeException
     *
     * @return string The evaluated template as a string
     */
    public function render($name, array $parameters = []): string
    {
        $template = $this->load($name);

        $this->registerFilters();
        $this->registerPlugins();

        // attach the global variables
        $parameters = array_replace($this->getGlobals(), $parameters);

        /*
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

        /*
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
        } catch (SmartyException $e) {
            throw RuntimeException::createFromPrevious($e, $template);
        } catch (\ErrorException $e) {
            throw RuntimeException::createFromPrevious($e, $template);
        }
    }

    /**
     * Creates a template object.
     *
     * @param mixed $name A template name
     * @param bool  $load If we should load template content right away. Default: true
     *
     *@throws SmartyException
     * @throws RuntimeException
     */
    public function createTemplate($name, bool $load = true): Smarty_Internal_Template
    {
        $template = $this->load($name);
        $template = $this->smarty->createTemplate($template, $this->smarty);

        if (true === $load) {
            /*
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
     *@throws SmartyException
     * @throws RuntimeException
     */
    public function compileTemplate($name, bool $forceCompile = false): Smarty_Internal_Template
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
     *
     * @throws RuntimeException
     * @throws SmartyException
     */
    public function renderTemplateFunction($template, string $name, array $attributes = [])
    {
        if (!$template instanceof Smarty_Internal_Template) {
            $template = $this->createTemplate($template);
        }

        if ($template->caching) {
            try {
                (new Smarty_Internal_Runtime_TplFunction())->callTemplateFunction($template, $name, $attributes, false);
            } catch (SmartyException $e) {
                throw new RuntimeException($e->getMessage());
            }
        } else {
            if (!isset($this->smarty->registered_plugins[PluginInterface::TYPE_FUNCTION][$name])
            || empty($this->smarty->registered_plugins[PluginInterface::TYPE_FUNCTION][$name])) {
                throw new RuntimeException(sprintf("Unable to find template function '%s'", $name));
            }

            $function = $this->smarty->registered_plugins[PluginInterface::TYPE_FUNCTION][$name][0];

            if (!is_callable($function)) {
                throw new RuntimeException(sprintf("Template function '%s' is not callable", $name));
            }

            echo $function($attributes, $template);
        }
    }

    /**
     * @param \Smarty_Internal_Template|string $template   A template object or resource path
     * @param string                           $name       Function name
     * @param array                            $attributes Attributes to pass to the template function
     *
     *@throws SmartyException
     * @throws RuntimeException
     *
     * @return string the output returned by the template function
     */
    public function fetchTemplateFunction($template, string $name, array $attributes = []): string
    {
        ob_start();
        $this->renderTemplateFunction($template, $name, $attributes);

        return ob_get_clean();
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @throws RuntimeException
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists($name): bool
    {
        try {
            $this->load($name);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param string $name A template name
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($name): bool
    {
        if ($name instanceof Smarty_Internal_Template) {
            return true;
        }

        return $this->templateLoader->supports($name);
    }

    /**
     * Renders a view and returns a Response.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A Response instance
     *
     * @throws RuntimeException
     *
     * @return Response A Response instance
     */
    public function renderResponse($view, array $parameters = [], Response $response = null): ?Response
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
     * @param Smarty_Internal_Template|string $name A template name
     *
     * @throws RuntimeException
     *
     * @return Smarty_Internal_Template|string The resource handle of the template file or template object
     *
     * @todo Check windows filepaths as defined in
     * {@link http://www.smarty.net/docs/en/resources.tpl#templates.windows.filepath}.
     */
    public function load($name)
    {
        if ($name instanceof Smarty_Internal_Template) {
            return $name;
        }

        return $this->templateLoader->load($name);
    }

    /**
     * Returns true if the given extension is registered.
     *
     * @param string $name The extension name
     *
     * @return bool Whether the extension is registered or not
     */
    public function hasExtension(string $name): bool
    {
        return array_key_exists($name, $this->extensions);
    }

    /**
     * Gets an extension by name.
     *
     * @param string $name The extension name
     *
     * @return ExtensionInterface An ExtensionInterface instance
     */
    public function getExtension(string $name): ExtensionInterface
    {
        if (false === $this->hasExtension($name)) {
            throw new InvalidArgumentException(sprintf('The "%s" extension is not enabled.', $name));
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
    public function removeExtension(string $name)
    {
        unset($this->extensions[$name]);
    }

    /**
     * Registers an array of extensions.
     *
     * @param ExtensionInterface[] $extensions An array of extensions
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = [];

        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    /**
     * Returns all registered extensions.
     *
     * @return ExtensionInterface[] An array of extensions
     */
    public function getExtensions(): array
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
     * @return FilterInterface[] An array of Filters
     */
    public function getFilters(): array
    {
        if (null === $this->filters) {
            $this->filters = [];
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
            } catch (SmartyException $e) {
                if (null !== $this->logger) {
                    $this->logger->warning(sprintf('SmartyException caught: %s.', $e->getMessage()));
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
     * @return Extension\Plugin\PluginInterface[] An array of plugins
     */
    public function getPlugins(string $extensionName = ''): array
    {
        if (null === $this->plugins) {
            $this->plugins = [];
            foreach ($this->getExtensions() as $extension) {
                $this->plugins = array_merge($this->plugins, $extension->getPlugins());
            }
        }

        // filter plugins that belong to $extension
        if ($extensionName) {
            $plugins = [];
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
            $this->registerPlugin($plugin);
        }
    }

    /**
     * register plugin to Smarty.
     *
     * @throws SmartyException
     */
    protected function registerPlugin(PluginInterface $plugin)
    {
        // verify that plugin isn't registered yet. That would cause a SmartyException.
        if (!isset($this->smarty->registered_plugins[$plugin->getType()][$plugin->getName()])) {
            $this->smarty->registerPlugin($plugin->getType(), $plugin->getName(), $plugin->getCallback());
        }
    }

    /**
     * Registers a Global.
     *
     * @param string $name  The global name
     * @param mixed  $value The global value
     */
    public function addGlobal(string $name, $value)
    {
        $this->globals[$name] = $value;
    }

    /**
     * Gets the registered Globals.
     *
     * @param bool $loadExtensions
     *
     * @return array An array of Globals
     */
    public function getGlobals($loadExtensions = true)
    {
        if (true === $loadExtensions) {
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
     * @param mixed $type
     * @param mixed $name
     * @param mixed $content
     * @param mixed $modified
     */
    public function smartyDefaultTemplateHandler($type, $name, &$content, &$modified, Smarty $smarty)
    {
        return ('file' === $type) ? (string) $this->load($name) : false;
    }

    /**
     * Get the setter method for a Smarty class variable (property).
     *
     * You may use this method to generate addSomeProperty() or getSomeProperty()
     * kind of methods by setting the $prefix parameter to "add" or "get".
     *
     * @param string $property
     * @param string $prefix
     *
     * @return string
     */
    protected function smartyPropertyToSetter($property, $prefix = 'set')
    {
        return $prefix.str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
    }
}
