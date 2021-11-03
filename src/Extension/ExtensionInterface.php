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

use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\FilterInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\PluginInterface;

interface ExtensionInterface
{
    /**
     * Returns a list of Plugins to add to the existing list.
     *
     * @return PluginInterface[] An array of Plugins
     *
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function getPlugins();

    /**
     * Returns a list of Filters to add to the existing list.
     *
     * @return FilterInterface[] An array of Filters
     *
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function getFilters();

    /**
     * Returns a list of Globals to add to the existing list.
     *
     * @return array An array of Globals
     *
     * @since  0.1.0
     *
     * @author Vítor Brandão <vitor@noiselabs.io>
     */
    public function getGlobals();

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName();
}
