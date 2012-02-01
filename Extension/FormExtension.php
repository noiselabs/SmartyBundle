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

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Util\FormUtil;

/**
 * SmartyBundle extension to render Symfony forms.
 *
 * @since  0.2.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class FormExtension extends AbstractExtension
{
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
	 *     <form action="..." method="post" {{ form_enctype(form) }}>
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
	 * Example usage in Twig:
	 *
	 *     {{ form_widget(view) }}
	 *
	 * You can pass options during the call:
	 *
	 *     {{ form_widget(view, {'attr': {'class': 'foo'}}) }}
	 *
	 *     {{ form_widget(view, {'separator': '+++++'}) }}
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