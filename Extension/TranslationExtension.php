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

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Provides integration of the Translation component with Smarty[Bundle].
 *
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class TranslationExtension extends Extension
{
	protected $translator;

	/**
	 * Constructor.
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function getPlugins()
	{
		return array(
			new BlockPlugin('trans', $this, 'trans_block'),
			new ModifierPlugin('trans', $this, 'trans_modifier')
		);
	}

	/**
	 * @param string Message to translate
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function trans_block(array $parameters = array(), $message = null, $template, &$repeat)
	{
		// only output on the closing tag
		if (!$repeat) {
			$parameters = array_merge(array(
				'arguments'	=> array(),
				'domain'	=> 'messages',
				'locale'	=> null,
			), $parameters);

			return $this->translator->trans($message, $parameters['arguments'], $parameters['domain'], $parameters['locale']);
		}
	}

	/**
	 * Usage in template context:
	 * <code>
	 * {"text to be translated"|trans}
	 * </code>
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
    public function trans_modifier($message, array $arguments = array(), $domain = 'messages', $locale = null)
    {
        return $this->translator->trans($message, $arguments, $domain, $locale);
    }

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function getName()
	{
		return 'translator';
	}
}