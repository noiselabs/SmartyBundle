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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Reads initializr configuration file and generates corresponding Smarty Globals
 *
 * @see Mopa\Bundle\BootstrapBundle\Twig\MopaBootstrapTwigExtension
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class InitializrExtension extends AbstractExtension
{
    protected $container;

    /**
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns array of Smarty Global Variables
     *
     * @return array Smarty Globals
     */
    public function getGlobals()
    {
        $meta = $this->container->getParameter('mopa_bootstrap.initializr.meta');
        $dns_prefetch = $this->container->getParameter('mopa_bootstrap.initializr.dns_prefetch');
        $google = $this->container->getParameter('mopa_bootstrap.initializr.google');

        // TODO: think about setting this default as kernel debug,
        // what about PROD env which does not need diagnostic mode and test
        $diagnostic_mode = $this->container->getParameter('mopa_bootstrap.initializr.diagnostic_mode');

        return array(
            'dns_prefetch'      => $dns_prefetch,
            'meta'              => $meta,
            'google'            => $google,
            'diagnostic_mode'   => $diagnostic_mode
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'initializr';
    }
}
