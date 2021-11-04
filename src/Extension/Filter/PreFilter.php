<?php
/**
 * This file is part of NoiseLabs-SmartyBundle.
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
 * Copyright (C) 2011-2018 Vítor Brandão
 *
 * @category    NoiseLabs
 *
 * @copyright   (C) 2011 Vítor Brandão <vitor@noiselabs.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 *
 * @see        https://www.noiselabs.io
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension\Filter;

/**
 * Prefilters are used to process the source of the template immediately before
 * compilation. The first parameter to the prefilter function is the template
 * source, possibly modified by some other prefilters. The plugin is supposed
 * to return the modified source. Note that this source is not saved anywhere,
 * it is only used for compilation.
 *
 * See {@link http://www.smarty.net/docs/en/plugins.prefilters.postfilters.tpl}.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class PreFilter extends AbstractFilter
{
    public function getType()
    {
        return 'pre';
    }
}
