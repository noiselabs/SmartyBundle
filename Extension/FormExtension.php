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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Util\FormUtil;

/**
 * SmartyBundle extension to render Symfony forms.
 *
 * The behavior and implementation of this class is inspired in the
 * following implementations:
 *  - Symfony\Bridge\TwigExtension\FormExtension
 *  - Symfony\Bundle\FrameworkBundle\Helper\FormHelper
 *
 * @since  0.2.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class FormExtension extends AbstractExtension
{
	/**
	 * Constructor.
	 *
	 * @param array           $resources An array of theme name
	 *
	 * @since  0.2.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function __construct(array $resources = array())
	{
		$this->themes = new \SplObjectStorage();
		$this->varStack = array();
		$this->blocks = new \SplObjectStorage();
		$this->resources = $resources;
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
		$this->blocks = new \SplObjectStorage();
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
			new ModifierPlugin('form_enctype', $this, 'renderEnctype'),
			new ModifierPlugin('form_widget', $this, 'renderWidget'),
			new FunctionPlugin('form_errors', $this, 'renderErrors'),
			new FunctionPlugin('form_label', $this, 'renderLabel'),
			new FunctionPlugin('form_row', $this, 'renderRow'),
			new FunctionPlugin('form_rest', $this, 'renderRest'),
			new FunctionPlugin('_form_is_choice_group', $this, 'isChoiceGroup'),
			new FunctionPlugin('_form_is_choice_selected', $this, 'isChoiceSelected'),
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
	 *     <form action="..." method="post" {$form|form_enctype}>
	 *
	 * @param FormView $view  The view for which to render the encoding type
	 *
	 * @return string The html markup
	 *
	 * @since  0.2.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function renderEnctype(FormView $view)
	{
		return $this->render($view, 'enctype');
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
	public function renderRow(FormView $view, array $variables = array())
	{
		return $this->render($view, 'row', $variables);
	}

	/**
	 * Renders views which have not already been rendered.
	 *
	 * @param FormView $view      The parent view
	 * @param array    $variables An array of variables
	 *
	 * @return string The html markup
	 *
	 * @since  0.2.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function renderRest(FormView $view, array $variables = array())
	{
		return $this->render($view, 'rest', $variables);
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
	 * @param FormView        $view      The view to render
	 * @param array           $variables Additional variables passed to the template
	 *
	 * @return string The html markup
	 *
	 * @since  0.2.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function renderWidget(FormView $view, array $variables = array())
	{
		return $this->render($view, 'widget', $variables);
	}

    /**
	 * Renders the errors of the given view.
	 *
	 * @param FormView $view The view to render the errors for
	 *
	 * @return string The html markup
	 *
	 * @since  0.2.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function renderErrors(FormView $view)
	{
		return $this->render($view, 'errors');
	}

    /**
	 * Renders the label of the given view.
	 *
	 * @param FormView $view  The view to render the label for
	 * @param string   $label Label name
	 * @param array    $variables Additional variables passed to the template
	 *
	 * @return string The html markup
	 *
	 * @since  0.2.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function renderLabel(FormView $view, $label = null, array $variables = array())
	{
		if ($label !== null) {
			$variables += array('label' => $label);
		}

		return $this->render($view, 'label', $variables);
	}

	/**
	 * Renders a template.
	 *
	 * 1. This function first looks for a block named "_<view id>_<section>",
	 * 2. if such a block is not found the function will look for a block named
	 *    "<type name>_<section>",
	 * 3. the type name is recursively replaced by the parent type name until a
	 *    corresponding block is found
	 *
	 * @param FormView  $view       The form view
	 * @param string    $section    The section to render (i.e. 'row', 'widget',
	 * 'label', ...)
	 * @param array     $variables  Additional variables
	 *
	 * @return string The html markup
	 *
	 * @throws FormException if no template block exists to render the given section of the view
	 */
	protected function render(FormView $view, $section, array $variables = array())
	{
		$mainTemplate = in_array($section, array('widget', 'row'));
		if ($mainTemplate && $view->isRendered()) {
				return '';
		}

        if (null === $this->template) {
            $this->template = reset($this->resources);
            if (!$this->template instanceof \Twig_Template) {
                $this->template = $this->environment->loadTemplate($this->template);
            }
        }

        $custom = '_'.$view->get('id');
        $rendering = $custom.$section;
        $blocks = $this->getBlocks($view);

        if (isset($this->varStack[$rendering])) {
            $typeIndex = $this->varStack[$rendering]['typeIndex'] - 1;
            $types = $this->varStack[$rendering]['types'];
            $this->varStack[$rendering]['variables'] = array_replace_recursive($this->varStack[$rendering]['variables'], $variables);
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
            $types[$typeIndex] .= '_'.$section;

            if (isset($blocks[$types[$typeIndex]])) {

                $this->varStack[$rendering]['typeIndex'] = $typeIndex;

                // we do not call renderBlock here to avoid too many nested level calls (XDebug limits the level to 100 by default)
                ob_start();
                $this->template->displayBlock($types[$typeIndex], $this->varStack[$rendering]['variables'], $blocks);
                $html = ob_get_clean();

                if ($mainTemplate) {
                    $view->setRendered();
                }

                unset($this->varStack[$rendering]);

                return $html;
            }
        } while (--$typeIndex >= 0);

        throw new FormException(sprintf(
            'Unable to render the form as none of the following blocks exist: "%s".',
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
}