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
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\FunctionPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use NoiseLabs\Bundle\SmartyBundle\Form\SmartyRendererInterface;
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
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class FormExtension extends AbstractExtension
{
    /**
     * @var \Symfony\Component\Form\FormRendererInterface
     */
    protected $renderer;

    /**
     * Constructor.
     *
     * @param SmartyRendererInterface $renderer A SmartyRendererInterface instance
     */
    public function __construct(SmartyRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
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
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'form';
    }

    /**
     */
    public function isChoiceGroup($label)
    {
        return FormUtil::isChoiceGroup($label);
    }

    /**
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
     * @param object $template The \Smarty_Internal_Template instance.
     *
     * @return string The HTML markup
     */
    public function renderEnctype($params, \Smarty_Internal_Template $template)
    {
        list($view, $parameters) = $this->extractFunctionParameters($params);

        return $this->renderer->searchAndRenderBlock($view, 'enctype');
    }

    /**
     * Renders a row for the view.
     *
     * @param FormView $view      The view to render as a row
     * @param array    $variables An array of variables
     *
     * @return string The html markup
     */
    public function renderRest($params, \Smarty_Internal_Template $template)
    {
        list($view, $variables) = $this->extractFunctionParameters($params);

        return $this->render($view, $template, 'row', $variables);
    }

    /**
     * Renders views which have not already been rendered.
     *
     * @param array  $params   Attributes passed from the template.
     * @param object $template The \Smarty_Internal_Template instance.
     *
     * @return string The html markup
     */
    public function renderRow($params, \Smarty_Internal_Template $template)
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
     * @param object $template The \Smarty_Internal_Template instance.
     *
     * @return string The html markup
     */
    public function renderWidget($params, \Smarty_Internal_Template $template)
    {
        list($view, $variables) = $this->extractFunctionParameters($params);

        return $this->render($view, $template, 'widget', $variables);
    }

    /**
     * Renders the errors of the given view.
     *
     * @param array  $params   Attributes passed from the template.
     * @param object $template The \Smarty_Internal_Template instance.
     *
     * @return string The html markup
     */
    public function renderErrors($params, \Smarty_Internal_Template $template)
    {
        list($view) = $this->extractFunctionParameters($params);

        return $this->render($view, $template, 'errors');
    }

    /**
     * Renders the label of the given view.
     *
     * @param array  $params   Attributes passed from the template.
     * @param object $template The \Smarty_Internal_Template instance.
     *
     * @return string The html markup
     */
    public function renderLabel($params, \Smarty_Internal_Template $template)
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
    protected function render(FormView $view, \Smarty_Internal_Template $template, $section, array $variables = array())
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
                $functionExists = $this->engine->renderTemplateFunction($template,
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
     * Returns, if available, the $form parameter from the parameters array
     * passed to the Smarty plugin function. When missing a FormException is
     * thrown.
     */
    protected function extractFunctionParameters(array $parameters)
    {
        if (!isset($parameters['form'])) {
            throw new FormException('"form" parameter missing in Smarty template function.');
        }

        if (!$parameters['form'] instanceof FormView) {
            throw new \InvalidArgumentException('"form" parameter must be an instance of Symfony\Component\Form\FormView');
        }

        $view = $parameters['form'];
        unset($parameters['form']);

        return array($view, $parameters);
    }

    /**
     * Creates \Smarty_Internal_Template instances for every resource (template filename)
     * set.
     *
     * @return \SplObjectStorage Collection of \Smarty_Internal_Template instances
     */
    protected function loadTemplates(FormView $view, \Smarty_Internal_Template $template = null)
    {
        $resources = $this->resources;

        if (isset($this->themes[$view])) {
            $resources = array_merge($resources, $this->themes[$view]);
        }

        foreach ($resources as $resource) {
            if ($this->engine->exists($resource)) {
                $this->templates->attach($this->engine->createTemplate($resource));
            }
        }

        if (isset($template)) {
            $this->templates->attach($template);
        }

        return $this->templates;
    }

    /**
     * Returns the Smarty functions used to render the view.
     *
     * @note This method may return a function that is not callable because it
     * looks for function templates defined in the template file which may not
     * be accessible due to be outside template blocks for instance (in a child
     * template).
     *
     * @param string $block The name of the function
     *
     * @return \Smarty_Internal_Template\false Return the \Smarty_Internal_Template where the function
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
}
