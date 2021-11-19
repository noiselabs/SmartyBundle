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

use NoiseLabs\Bundle\SmartyBundle\Templating\Helper\RouterHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouterHelperTest extends TestCase
{
    public function testPath()
    {
        $urlReference = '/';
        $urlGenerator = $this->getUrlGenerator($urlReference);
        $routerHelper = new RouterHelper($urlGenerator);
        $this->assertSame($urlReference, $routerHelper->path('home'));
    }

    public function testUrl()
    {
        $urlReference = 'http://localhost/';
        $urlGenerator = $this->getUrlGenerator($urlReference);
        $routerHelper = new RouterHelper($urlGenerator);
        $this->assertSame($urlReference, $routerHelper->url('home'));
    }

    public function testGetName()
    {
        $urlGenerator = $this->getUrlGenerator('/');
        $routerHelper = new RouterHelper($urlGenerator);
        $this->assertSame('router', $routerHelper->getName());
    }

    private function getUrlGenerator(string $urlReference)
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator
            ->expects($this->any())
            ->method('generate')
            ->willReturn($urlReference)
        ;

        return $urlGenerator;
    }
}
