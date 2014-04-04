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

namespace NoiseLabs\Bundle\SmartyBundle\Bootstrap\Navbar\Renderer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\NavbarInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\OptionNotFoundException;
use Mopa\Bundle\BootstrapBundle\Navbar\Renderer\NavbarRenderer as BaseNavbarRenderer;

class NavbarRenderer extends BaseNavbarRenderer
{
    protected $container;
    protected $navbars;

    public function __construct(ContainerInterface $container, array $navbars)
    {
        $this->container = $container;
        $this->navbars = $navbars;
    }

    /**
     * Renders the navbar with the specified renderer.
     *
     * @param  \Knp\Menu\ItemInterface $item
     * @param  array                   $options
     * @return string
     */
    public function renderNavbar($name, array $options = array())
    {
        $options = array_merge($this->getNavbarDefaultOptions(), $options);

        $navbar = $this->getNavbar($name);
        $navbar = $this->createFormViews($navbar);
        $function = 'navbar';

        try {
            $template = $navbar->getOption('template');
        } catch (OptionNotFoundException $e) {
            $template = $options['template'];
        }

        $html = $this->getSmartyEngine()->fetchTemplateFunction($template, $function,
            array('navbar' => $navbar));

        return $html;
    }

    protected function getSmartyEngine()
    {
        return $this->container->get('templating.engine.smarty');
    }

    protected function createFormViews(NavbarInterface $navbar)
    {
        foreach ($navbar->getFormClasses() as $key => $formTypeString) {
            $formType = null;
            if (is_string($formTypeString) && strlen($formTypeString) > 0) {
                $formType = new $formTypeString();
            }
            if ($formType && $formType instanceof NavbarFormInterface) {
                $navbar->setFormType($key, $formType);
                $form = $this->container->get('form.factory')->create($formType);
                $navbar->setFormView($key, $form->createView());
            } else {
                throw new \Exception("Form Type Created ". $formTypeString . " is not a NavbarFormInterface");
            }
        }

        return $navbar;
    }

    protected function getNavbar($name)
    {
        if (!in_array($name, array_keys($this->navbars))) {
            throw new \Exception(sprintf('The given Navbar alias "%s" was not found', $name));
        }

        return $this->container->get($this->navbars[$name]);
    }

    protected function getNavbarDefaultOptions()
    {
        return array(
            'template' => $this->container->getParameter("mopa_bootstrap.navbar.template")
        );
    }
}
