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
 * @copyright   (C) 2011 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\FunctionPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\AssetsHelper;

/**
 * Provides helper functions to link to assets (images, Javascript,
 * stylesheets, etc.).
 *
 * If you need to use this class without the Symfony Container use this:
 * <code>
 * class MyAssetsExtension extends AssetsExtension
 * {
 *   public function __construct(\Symfony\Component\Templating\Helper\AssetsHelper $helper) {
 *      $this->helper = $helper;
 *   }
 * }
 * </code>
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class AssetsExtension extends AbstractExtension
{
    protected $helper;
    protected $container;

    /**
     * Constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->helper = null;
        $this->container = $container;
    }

    public function getHelper()
    {
        return null === $this->helper ?
            $this->container->get('templating.helper.assets') : $this->helper;
    }

    public function setHelper(AssetsHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('asset', $this, 'getAssetUrl_block'),
            new ModifierPlugin('asset', $this, 'getAssetUrl_modifier'),
            new FunctionPlugin('assets_version', $this, 'getAssetsVersion')
        );
    }

    /**
     * Returns the public path of an asset.
     *
     * Absolute paths (i.e. http://...) are returned unmodified.
     *
     * @param string $path        A public path
     * @param string $packageName The name of the asset package to use
     *
     * @return string A public path which takes into account the base path and URL path
     */
    public function getAssetUrl($path, $packageName = null)
    {
        return $this->getHelper()->getUrl($path, $packageName);
    }

    /**
     * Returns the public path of an asset
     *
     * Absolute paths (i.e. http://...) are returned unmodified.
     *
     * @param string $path A public path
     *
     * @return string A public path which takes into account the base path and URL path
     */
    public function getAssetUrl_block(array $parameters = array(), $path = null, $template, &$repeat)
    {
        // only output on the closing tag
        if (!$repeat) {
            $parameters = array_merge(array(
                'package'   => null,
            ), $parameters);

            return $this->getHelper()->getUrl($path, $parameters['package']);
        }
    }

    /**
     * Returns the public path of an asset
     *
     * Absolute paths (i.e. http://...) are returned unmodified.
     *
     * @param string $path A public path
     *
     * @return string A public path which takes into account the base path
     * and URL path
     */
    public function getAssetUrl_modifier($path, $package = null)
    {
        return $this->getHelper()->getUrl($path, $package);
    }

    /**
     * Returns the version of the assets in a package
     *
     * @return int
     */
    public function getAssetsVersion(array $parameters = array(), \Smarty_Internal_Template $template)
    {
        $parameters = array_merge(array(
            'package'   => null,
        ), $parameters);

        return $this->getHelper()->getVersion($parameters['package']);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'assets';
    }
}
