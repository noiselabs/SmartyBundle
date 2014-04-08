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

namespace NoiseLabs\Bundle\SmartyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Overrides some MopaBootstrap configuration to make it work with Smarty.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class MopaBootstrapPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('templating.engine.smarty')) {
            return;
        }

        if (false === $container->getParameter('smarty.bootstrap')) {
            return;
        }

        if (!$container->hasDefinition('mopa_bootstrap.navbar_renderer')) {
            $container->removeDefinition('smarty.extension.bootstrap_navbar');
        } else {
            $definition = $container->getDefinition('mopa_bootstrap.navbar_renderer');
            $definition->setClass($container->getParameter('smarty.bootstrap.navbar_renderer.class'));
        }

        if (!$container->hasParameter('mopa_bootstrap.initializr.meta')) {
            $container->removeDefinition('smarty.extension.bootstrap_initializr');
        }
    }
}
