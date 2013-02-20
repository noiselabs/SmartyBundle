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
 * @author      Vítor Brandão <vitor@noiselabs.org>
 * @copyright   (C) 2011 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 * @since       0.1.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

/**
 * Base Extension class.
 *
 * @since  0.1.0
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * Returns a list of Plugins to add to the existing list.
     *
     * @return array An array of Plugins
     *
     * @since  0.1.0
     * @author Vítor Brandão <vitor@noiselabs.org>
     */
    public function getPlugins()
    {
        return array();
    }

    /**
     * Returns a list of Filters to add to the existing list.
     *
     * @return array An array of Filters
     *
     * @since  0.1.0
     * @author Vítor Brandão <vitor@noiselabs.org>
     */
    public function getFilters()
    {
        return array();
    }

    /**
     * Returns a list of globals to add to the existing list.
     *
     * @return array An array of globals
     *
     * @since  0.1.0
     * @author Vítor Brandão <vitor@noiselabs.org>
     */
    public function getGlobals()
    {
        return array();
    }
}
