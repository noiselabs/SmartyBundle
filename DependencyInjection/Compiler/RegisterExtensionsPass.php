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

namespace NoiseLabs\Bundle\SmartyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds tagged smarty.extension services to smarty service.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class RegisterExtensionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('templating.engine.smarty')) {
            return;
        }

        $definition = $container->getDefinition('templating.engine.smarty');

        $calls = $definition->getMethodCalls();
        $definition->setMethodCalls([]);
        foreach ($container->findTaggedServiceIds('smarty.extension') as $id => $attributes) {
            $definition->addMethodCall('addExtension', [new Reference($id)]);
        }
        $definition->setMethodCalls(array_merge($definition->getMethodCalls(), $calls));
    }
}
