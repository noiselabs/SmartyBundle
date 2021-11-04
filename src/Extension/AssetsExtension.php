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

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\FunctionPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Smarty_Internal_Template;
use Symfony\Component\Asset\Packages;

/**
 * Provides helper functions to link to assets (images, Javascript,
 * stylesheets, etc.).
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class AssetsExtension extends AbstractExtension
{
    /**
     * @var Packages
     */
    protected $packages;

    /**
     * Constructor.
     */
    public function __construct(Packages $packages = null)
    {
        $this->packages = $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return [
            new BlockPlugin('asset', $this, 'getAssetUrl_block'),
            new ModifierPlugin('asset', $this, 'getAssetUrl_modifier'),
            new FunctionPlugin('assets_version', $this, 'getAssetsVersion'),
        ];
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
        return $this->packages->getUrl($path, $packageName);
    }

    /**
     * Returns the public path of an asset.
     *
     * Absolute paths (i.e. http://...) are returned unmodified.
     *
     * @param string     $path     A public path
     * @param null|mixed $template
     * @param null|mixed $repeat
     *
     * @return string A public path which takes into account the base path and URL path
     */
    public function getAssetUrl_block(array $parameters = [], $path = null, $template = null, &$repeat = null)
    {
        // only output on the closing tag
        if (!$repeat) {
            $parameters = array_merge([
                'package' => null,
            ], $parameters);

            return $this->packages->getUrl($path, $parameters['package']);
        }
    }

    /**
     * Returns the public path of an asset.
     *
     * Absolute paths (i.e. http://...) are returned unmodified.
     *
     * @param string     $path    A public path
     * @param null|mixed $package
     *
     * @return string A public path which takes into account the base path
     *                and URL path
     */
    public function getAssetUrl_modifier($path, $package = null)
    {
        return $this->packages->getUrl($path, $package);
    }

    /**
     * Returns the version of the assets in a package.
     *
     * @return string
     */
    public function getAssetsVersion(array $parameters = [], Smarty_Internal_Template $template = null)
    {
        $parameters = array_merge([
            'package' => null,
        ], $parameters);

        return $this->packages->getVersion('', $parameters['package']);
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
