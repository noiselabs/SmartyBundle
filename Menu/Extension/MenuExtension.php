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
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Menu\Extension;

use Knp\Menu\Twig\Helper;
use NoiseLabs\Bundle\SmartyBundle\Extension\AbstractExtension;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;

/**
 * @see Knp\Menu\Twig\MenuExtension
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class MenuExtension extends AbstractExtension
{
    protected $helper;

    /**
     * @param \Knp\Menu\Twig\Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function getPlugins()
    {
        return array(
            new ModifierPlugin('knp_menu_get', $this, 'get'),
            new ModifierPlugin('knp_menu_render', $this, 'render'),
        );
    }

    /**
     * Retrieves an item following a path in the tree.
     *
     * @param  ItemInterface|string $menu
     * @param  array                $path
     * @param  array                $options
     * @return ItemInterface
     */
    public function get($menu, array $path = array(), array $options = array())
    {
        return $this->helper->get($menu, $path, $options);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param  ItemInterface|string|array $menu
     * @param  array                      $options
     * @param  string                     $renderer
     * @return string
     */
    public function render($menu, array $options = array(), $renderer = null)
    {
        $options = array_merge(array('is_safe' => array('html')), $options);

        return $this->helper->render($menu, $options, $renderer);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'smartybundle_menu';
    }
}
