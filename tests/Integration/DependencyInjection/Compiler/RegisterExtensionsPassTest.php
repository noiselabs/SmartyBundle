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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Integration\DependencyInjection\Compiler;

use NoiseLabs\Bundle\SmartyBundle\DependencyInjection\Compiler\RegisterExtensionsPass;
use NoiseLabs\Bundle\SmartyBundle\Extension\RoutingExtension;
use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterExtensionsPassTest extends TestCase
{
    public function testThatItReturnsNoMethodCallsWhenSmartyEngineIsNotEnabled()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);

        $smartyEngineDefinition = new Definition('templating.engine.smarty');

        $extensionPass = new RegisterExtensionsPass();
        $extensionPass->process($container);

        self::assertFalse($container->hasDefinition('templating.engine.smarty'));
        self::assertEmpty($smartyEngineDefinition->getMethodCalls());
    }

    public function testThatItReturnsNoMethodCallsWhenNoExtensionsAreRegistered()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);

        $smartyEngineDefinition = new Definition('templating.engine.smarty');
        $container->register('templating.engine.smarty', SmartyEngine::class);

        $extensionPass = new RegisterExtensionsPass();
        $extensionPass->process($container);

        self::assertEmpty($smartyEngineDefinition->getMethodCalls());
    }

    public function testThatItReturnsMethodCallsWhenExtensionsAreRegistered()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', false);

        $smartyEngineDefinition = new Definition(SmartyEngine::class);
        $container->setDefinition('templating.engine.smarty', $smartyEngineDefinition);

        $routingExtensionDefinition = new Definition(RoutingExtension::class);
        $container->setDefinition('smarty.extension.routing', $routingExtensionDefinition)->addTag('smarty.extension');

        $extensionPass = new RegisterExtensionsPass();
        $extensionPass->process($container);

        self::assertCount(1, $container->findTaggedServiceIds('smarty.extension'));
        self::assertCount(1, $smartyEngineDefinition->getMethodCalls());
    }
}
