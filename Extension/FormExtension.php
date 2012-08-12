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
 * @since       0.2.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\FunctionPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Smarty_Internal_Template as SmartyTemplate;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\Templating\EngineInterface;

/**
 * SmartyBundle extension to render Symfony forms.
 *
 * The behavior and implementation of this class is inspired in the
 * following implementations:
 *  - Symfony\Bridge\TwigExtension\FormExtension
 *  - Symfony\Bundle\FrameworkBundle\Helper\FormHelper
 *
 * Symfony documentation:
 *  - {@link http://symfony.com/doc/current/book/forms.html#rendering-a-form-in-a-template}
 *  - {@link http://symfony.com/doc/current/book/forms.html#form-theming}
 *  - {@link http://symfony.com/doc/current/cookbook/form/form_customization.html}
 *
 * Thanks to Smarty developers Uwe Tews and Rodney Rehm for 1) patience and 2)
 * insight on Smarty internals.
 *
 * @since  0.2.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class FormExtension extends AbstractExtension
{
    protected $engine;
    protected $resources;
    protected $functions;
    protected $themes;
    protected $varStack;
    protected $templates;

    /**
     * Constructor.
     *
     * @param EngineInterface $engine    The templating engine
     * @param array           $resources An array of theme names
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function __construct(EngineInterface $engine, array $resources = array())
    {
        $this->engine = $engine;
        $this->resources = $resources;
        $this->varStack = array();
        $this->themes = new \SplObjectStorage();
        $this->templates = new \SplObjectStorage();
    }

    /**
     * Sets a theme for a given view.
     *
     * @param FormView $view      A FormView instance
     * @param array    $resources An array of resources
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function setTheme(FormView $view, array $resources)
    {
        $this->themes->attach($view, $resources);
        $this->templates = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getPlugins()
    {
        return array(
            new FunctionPlugin('form_enctype', $this, 'renderEnctype'),
            new FunctionPlugin('form_widget', $this, 'renderWidget'),
            new FunctionPlugin('form_errors', $this, 'renderErrors'),
            new FunctionPlugin('form_label', $this, 'renderLabel'),
            new FunctionPlugin('form_row', $this, 'renderRow'),
            new FunctionPlugin('form_rest', $this, 'renderRest'),
            new ModifierPlugin('_form_is_choice_group', $this, 'isChoiceGroup'),
            new ModifierPlugin('_form_is_choice_selected', $this, 'isChoiceSelected'),
        );
    }

    /**
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function isChoiceGroup($label)
    {
        return FormUtil::isChoiceGroup($label);
    }

    /**
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function isChoiceSelected(FormView $view, $choice)
    {
        return FormUtil::isChoiceSelected($choice, $view->get('value'));
    }

    /**
     * Renders the HTML enctype in the form tag, if necessary.
     *
     * Example usage in Smarty templates:
     *
     *     <form action="..." method="post" {form_enctype form=$form}>
     *
     * @param array  $params   Attributes passed from the template.
     * @param object $template The SmartyTemplate instance.
     *
     * @return string The html markup
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function renderEnctype($params, SmartyTemplate $template)
    {
        $view = $this->extractFunctionParameters($params);

        return $this->render(reset($view), $template, 'enctype');
    }

    /**
     * Renders a row for the view.
     *
     * @param FormView $view      The view to render as a row
     * @param array    $variables An array of variables
     *
     * @return string The html markup
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function renderRest($params, SmartyTemplate $template)
    {
        list($view, $variables) = $this->extractFunctionParameters($params);

        return $this->render($view, $template, 'row', $variables);
    }

    /**
     * Renders views which have not already been rendered.
     *
     * @param array  $params   Attributes passed from the template.
     * @param object $template The SmartyTemplate instance.
     *
     * @return string The html markup
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function renderRow($params, SmartyTemplate $template)
    {
        list($view, $variables) = $this->extractFunctionParameters($params);

        return $this->render($view, $template, 'rest', $variables);
    }

    /**
     * Renders the HTML for a given view
     *
     * Example usage in Smarty:
     *
     *     {$form|form_widget}
     *
     * You can pass options during the call:
     *
     *     {$form|form_widget:array('attr' => array('class' => 'foo'))}
     *
     *     {$form|form_widget:array('separator' => '+++++')}
     *
     * @param array  $params   Attributes passed from the template.
     * @param object $template The SmartyTemplate instance.
     *
     * @return string The html markup
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function renderWidget($params, SmartyTemplate $template)
    {
        list($view, $variables) = $this->extractFunctionParameters($params);

        return $this->render($view, $template, 'widget', $variables);
    }

    /**
     * Renders the errors of the given view.
     *
     * @param array  $params   Attributes passed from the template.
     * @param object $template The SmartyTemplate instance.
     *
     * @return string The html markup
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function renderErrors($params, SmartyTemplate $template)
    {
        list($view) = $this->extractFunctionParameters($params);

        return $this->render($view, $template, 'errors');
    }

    /**
     * Renders the label of the given view.
     *
     * @param array  $params   Attributes passed from the template.
     * @param object $template The SmartyTemplate instance.
     *
     * @return string The html markup
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function renderLabel($params, SmartyTemplate $template)
    {
        list($view, $variables) = $this->extractFunctionParameters($params);

        return $this->render($view, 'label', $variables);
    }

    /**
     * Renders a template.
     *
     * 1. This function first looks for a function named "_<view id>_<section>",
     * 2. if such a block is not found the function will look for a block named
     *    "<type name>_<section>",
     * 3. the type name is recursively replaced by the parent type name until a
     *    corresponding block is found
     *
     * @param FormView $view    The form view
     * @param string   $section The section to render (i.e. 'row', 'widget',
     * 'label', ...)
     * @param array $variables Additional variables
     *
     * @return string The html markup
     *
     * @throws FormException if no template block exists to render the given section of the view
     */
    protected function render(FormView $view, SmartyTemplate $template, $section, array $variables = array())
    {
        $mainTemplate = in_array($section, array('widget', 'row'));
        if ($mainTemplate && $view->isRendered()) {
            return '';
        }

        $this->loadTemplates($view, $template);

        $custom = '_'.$view->get('id');
        $rendering = $custom.$section;

        if (isset($this->varStack[$rendering])) {
            $typeIndex = $this->varStack[$rendering]['typeIndex'] - 1;
            $types = $this->varStack[$rendering]['types'];
            $this->varStack[$rendering]['variables'] = array_replace_recursive(
                $this->varStack[$rendering]['variables'], $variables);
        } else {
            $types = $view->get('types');
            $types[] = $custom;
            $typeIndex = count($types) - 1;
            $this->varStack[$rendering] = array (
                'variables' => array_replace_recursive($view->all(), $variables),
                'types'     => $types,
            );
        }

        do {
            $function = $types[$typeIndex] .= '_'.$section;
            $template = $this->lookupTemplateFunction($function);

            if ($template) {

                $this->varStack[$rendering]['typeIndex'] = $typeIndex;

                ob_start();
                $functionExists = $this->displayTemplateFunction($template,
                    $function, $this->varStack[$rendering]['variables']);
                $html = ob_get_clean();

                if ($functionExists) {

                    unset($this->varStack[$rendering]);

                    if ($mainTemplate) {
                        $view->setRendered();
                    }

                    return $html;
                }
            }
        } while (--$typeIndex >= 0);

        throw new FormException(sprintf(
            'Unable to render the form as none of the following functions exist: "%s".',
            implode('", "', array_reverse($types))
        ));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getName()
    {
        return 'form';
    }

    /**
     * Returns, if available, the $form parameter from the parameters array
     * passed to the Smarty plugin function. When missing a FormException is
     * thrown.
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    protected function extractFunctionParameters(array $parameters)
    {
        if (!isset($parameters['form'])) {
            throw new FormException("'form' parameter missing in Smarty template function.");
        }

        $view = $parameters['form'];
        unset($parameters['form']);

        return array($view, $parameters);
    }

    /**
     * Creates SmartyTemplate instances for every resource (template filename)
     * set.
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     *
     * @return \SplObjectStorage Collection of SmartyTemplate instances
     */
    protected function loadTemplates(FormView $view, SmartyTemplate $template = null)
    {
        $resources = $this->resources;

        if (isset($this->themes[$view])) {
            $resources = array_merge($resources, $this->themes[$view]);
        }

        foreach ($resources as $resource) {
            if ($this->engine->exists($resource)) {
                $this->templates->attach($this->loadTemplate($resource));
            }
        }

        if (isset($template)) {
            $this->templates->attach($template);
        }

        return $this->templates;
    }

    /**
     * Creates a SmartyTemplate from a logical template name.
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     *
     * @param string  $name       A template name
     * @param mixed   $cache_id   cache id to be used with this template
     * @param mixed   $compile_id compile id to be used with this template
     * @param object  $parent     next higher level of Smarty variables
     * @param boolean $do_clone   flag is Smarty object shall be cloned
     *
     * @return SmartyTemplate A SmartyTemplate instance.
     */
    protected function loadTemplate($name, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true)
    {
        $smarty = $this->engine->getSmarty();
        $filename = $this->engine->load($name);

        $template = $smarty->createTemplate($filename, $cache_id, $compile_id,
        $parent, $do_clone);
        $template->fetch();

        return $template;
    }

    /**
     * Returns the Smarty functions used to render the view.
     *
     * @note This method may return a function that is not callable because it
     * looks for function templates defined in the template file which may not
     * be accessible due to be outside template blocks for instance (in a child
     * template).
     *
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     *
     * @param string $block The name of the function
     *
     * @return SmartyTemplate\false Return the SmartyTemplate where the function
     * is found or false if it doesn't exist in any loaded template object.
     */
    protected function lookupTemplateFunction($function)
    {
        foreach ($this->templates as $template) {
            if (in_array($function, array_keys($template->template_functions))) {
                return $template;
            }
        }

        return false;
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
     * @since  0.2.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     *
     * @deprecated by SmartyEngine::renderTemplateFunction()
     */
    protected function displayTemplateFunction(SmartyTemplate $template, $function,
    array $attributes = array())
    {
        if ($template->caching) {
            \Smarty_Internal_Function_Call_Handler::call($function,
            $template, $attributes, $template->properties['nocache_hash'], false);

            return true;
        } else {
            if (is_callable($function = 'smarty_template_function_'.$function)) {
                $function($template, $attributes);

                return true;
            }
        }

        return false;
    }
}
