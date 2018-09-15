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
 * Copyright (C) 2011-2018 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @author      Vítor Brandão <vitor@noiselabs.io>
 * @copyright   (C) 2011-2018 Vítor Brandão <vitor@noiselabs.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        https://www.noiselabs.io
 */

namespace NoiseLabs\Bundle\SmartyBundle\Form;

use Symfony\Component\Form\AbstractRendererEngine;
use Symfony\Component\Form\FormView;

/**
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class SmartyRendererEngine extends AbstractRendererEngine implements SmartyRendererEngineInterface
{
    /**
     * {@inheritdoc}
     */
    public function renderBlock(FormView $view, $resource, $blockName, array $variables = array())
    {
        $cacheKey = $view->vars[self::CACHE_KEY_VAR];

        // TODO: Move body of FormExtension::render() to here.
    }

    /**
     * Loads the cache with the resource for a given block name.
     *
     * @see getResourceForBlock()
     *
     * @param string   $cacheKey  The cache key of the form view.
     * @param FormView $view      The form view for finding the applying themes.
     * @param string   $blockName The name of the block to load.
     *
     * @return Boolean True if the resource could be loaded, false otherwise.
     */
    protected function loadResourceForBlockName($cacheKey, FormView $view, $blockName)
    {
    }
}
