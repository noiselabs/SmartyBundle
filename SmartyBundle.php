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

namespace NoiseLabs\Bundle\SmartyBundle;

use NoiseLabs\Bundle\SmartyBundle\DependencyInjection\Compiler\MopaBootstrapPass;
use NoiseLabs\Bundle\SmartyBundle\DependencyInjection\Compiler\RegisterExtensionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Smarty Bundle.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class SmartyBundle extends Bundle
{
    const VERSION = '1.2.0';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MopaBootstrapPass());
        $container->addCompilerPass(new RegisterExtensionsPass());
    }
}
