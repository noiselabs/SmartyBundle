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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\SecurityExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Test suite for the security extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class SecurityExtensionTest extends TestCase
{
    public function testExtensionName()
    {
        $context = $this->createSecurityContext();
        $extension = new SecurityExtension($context);

        $this->assertEquals('security', $extension->getName());
    }

    public function testGetExtensionFromSmartyEngine()
    {
        $context = $this->createSecurityContext();
        $extension = new SecurityExtension($context);

        $this->engine->addExtension($extension);

        $this->assertInstanceOf('NoiseLabs\Bundle\SmartyBundle\Extension\SecurityExtension', $this->engine->getExtension('security'));
    }

    /**
     * @dataProvider getIsGrantedTests
     */
    public function testTrans($content, $expected, $granted)
    {
        static $test = 0;
        $template = 'translation_test_'.$test++.'.html.tpl';

        $this->engine->setTemplate($template, $content);
        $context = $this->createSecurityContext($granted);
        $this->engine->addExtension(new SecurityExtension($context));

        $this->assertEquals($expected, $this->engine->render($template));
    }

    /**
     * Returns is_granted tests (data provider).
     */
    public function getIsGrantedTests()
    {
        return array(
            // is_granted modifier
            array('{if "IS_AUTHENTICATED_FULLY"|is_granted}access granted{else}access denied{/if}', 'access granted', true),
            array('{if "IS_AUTHENTICATED_FULLY"|is_granted}access granted{else}access denied{/if}', 'access denied', false)
        );
    }

    protected function createSecurityContext($granted = false)
    {
        $authManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $decisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');
        $decisionManager->expects($this->any())->method('decide')->will($this->returnValue($granted));

        $context = new SecurityContext($authManager, $decisionManager, false);
        $context->setToken($token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
        $token
            ->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;

        return $context;
    }
}
