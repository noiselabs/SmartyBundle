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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Templating\Helper;

use NoiseLabs\Bundle\SmartyBundle\Templating\Helper\ActionsHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class ActionsHelperTest extends TestCase
{
    public function testRender()
    {
        $responseContent = '<div>Hello!</div>';
        $handler = $this->getHandler($responseContent);
        $actionsHelper = new ActionsHelper($handler);
        $this->assertSame($responseContent, $actionsHelper->render('/'));
    }

    public function testController()
    {
        $controllerName = 'HelloController';
        $handler = $this->getHandler('');
        $actionsHelper = new ActionsHelper($handler);
        $controllerReference = $actionsHelper->controller($controllerName);
        $this->assertSame($controllerName, $controllerReference->controller);
    }

    public function testGetName()
    {
        $handler = $this->getHandler('');
        $actionsHelper = new ActionsHelper($handler);
        $this->assertSame('actions', $actionsHelper->getName());
    }

    private function getHandler($responseContent): FragmentHandler
    {
        $handler = $this->createMock(FragmentHandler::class);
        $handler
            ->expects($this->any())
            ->method('render')
            ->willReturn($responseContent)
        ;

        return $handler;
    }
}
