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
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\FunctionPlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides helper functions to link to assets (images, Javascript,
 * stylesheets, etc.).
 *
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class AssetsExtension extends Extension
{
	protected $container;

	/**
	 * Constructor.
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
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
			new BlockPlugin('asset', $this, 'getAssetUrl'),
			new FunctionPlugin('assets_version', $this, 'getAssetsVersion')
		);
	}

	/**
	 * Returns the public path of an asset
	 *
	 * Absolute paths (i.e. http://...) are returned unmodified.
	 *
	 * @param string $path        A public path
	 *
	 * @return string A public path which takes into account the base path and URL path
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function getAssetUrl(array $parameters = array(), $path = null, $template, &$repeat)
	{
		// only output on the closing tag
		if (!$repeat) {
			$parameters = array_merge(array(
				'package'	=> null,
			), $parameters);

			return $this->container->get('templating.helper.assets')->getUrl($path, $parameters['package']);
		}
	}

	/**
	 * Returns the version of the assets in a package
	 *
	 * @return int
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function getAssetsVersion(array $parameters = array(), \Smarty_Internal_Template $template)
	{
		$parameters = array_merge(array(
				'package'	=> null,
		), $parameters);

		return $this->container->get('templating.helper.assets')->getVersion($parameters['package']);
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
		return 'assets';
	}
}