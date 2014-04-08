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

namespace NoiseLabs\Bundle\SmartyBundle\Bootstrap\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\AbstractExtension;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Mopa\Bundle\BootstrapBundle\Navbar\Renderer\NavbarRenderer;

/**
 * @see Mopa\Bundle\BootstrapBundle\Navbar\Twig\NavbarExtension
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class NavbarExtension extends AbstractExtension
{
    protected $renderer;

    /**
     * @param \Mopa\Bootstrap\Menu\Renderer\NavbarRenderer $renderer
     */
    public function __construct(NavbarRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getPlugins()
    {
        return array(
            new ModifierPlugin('mopa_bootstrap_navbar', $this, 'render')
        );
    }

    /**
     * Renders the whole Navbar with the specified renderer.
     *
     * @param  \Knp\Menu\ItemInterface|string|array $menu
     * @param  array                                $options
     * @param  string                               $renderer
     * @return string
     */
    public function render($name, array $options = array(), $renderer = null)
    {
        $options = array_merge(array('is_safe' => array('html')), $options);

        return $this->renderer->renderNavbar($name, $options, $renderer);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mopa_bootstrap_navbar';
    }
}
