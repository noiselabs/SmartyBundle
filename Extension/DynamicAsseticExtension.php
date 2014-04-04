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
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetInterface;
use Assetic\ValueSupplierInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\RoutingExtension;
use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException;

/**
 * The "dynamic" reincarnation of AsseticExtension.
 *
 * @author Vítor Brandão <vitor@noiselabs.com>
 */
class DynamicAsseticExtension extends AsseticExtension
{
    protected $routingExtension;

    /**
     * Constructor.
     *
     * @param RoutingExtension $routingExtension The routing extension
     * @param AssetFactory     $factory          The asset factory
     * @param boolean          $useController    Handle assets dynamically
     *
     * @see Symfony\Bundle\AsseticBundle\Templating\DynamicAsseticHelper
     */
    public function __construct(RoutingExtension $routingExtension, AssetFactory $factory, $useController = false, $enabledBundles = array(), ValueSupplierInterface $valueSupplier = null)
    {
        $this->routingExtension = $routingExtension;

        parent::__construct($factory, $useController, $valueSupplier);
    }

    /**
     * Returns an URL for the supplied asset.
     *
     * @param AssetInterface $asset   An asset
     * @param array          $options An array of options
     *
     * @return string An echo-ready URL
     */
    protected function getAssetUrl(AssetInterface $asset, array $options = array())
    {
        try {
            return $this->routingExtension->getPath('_assetic_'.$options['name']);
        } catch (\Exception $e) {
            throw RuntimeException::createFromPrevious($e);
        }
    }
}
