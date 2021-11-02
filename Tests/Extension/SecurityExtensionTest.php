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
 * Copyright (C) 2011-2016 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2016 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\SecurityExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Test suite for the security extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class SecurityExtensionTest extends TestCase
{
    public function testExtensionName()
    {
        $authorizationChecker = $this->createAuthorizationChecker();
        $extension = new SecurityExtension($authorizationChecker);

        $this->assertEquals('security', $extension->getName());
    }

    public function testGetExtensionFromSmartyEngine()
    {
        $authorizationChecker = $this->createAuthorizationChecker();
        $extension = new SecurityExtension($authorizationChecker);

        $this->engine->addExtension($extension);

        $this->assertInstanceOf(SecurityExtension::class, $this->engine->getExtension('security'));
    }

    /**
     * @dataProvider getIsGrantedTests
     */
    public function testTrans($content, $expected, $granted)
    {
        static $test = 0;
        $template = 'translation_test_'.$test++.'.html.tpl';

        $this->engine->setTemplate($template, $content);
        $authorizationChecker = $this->createAuthorizationChecker($granted);
        $this->engine->addExtension(new SecurityExtension($authorizationChecker));

        $this->assertEquals($expected, $this->engine->render($template));
    }

    /**
     * Returns is_granted tests (data provider).
     */
    public function getIsGrantedTests()
    {
        return [
            // is_granted modifier
            ['{if "IS_AUTHENTICATED_FULLY"|is_granted}access granted{else}access denied{/if}', 'access granted', true],
            ['{if "IS_AUTHENTICATED_FULLY"|is_granted}access granted{else}access denied{/if}', 'access denied', false]
        ];
    }

    public function testCsrfTokenWithCsrfTokenManager()
    {
        $this->markTestSkipped();

        $tokenId = 'foo';
        $tokenValue = 'xsrf';
        $content = "{'$tokenId'|csrf_token}";
        $template = 'csrf_token_manager_test.html.tpl';

        // symfony 2.3+
        $this->engine->setTemplate($template, $content);
        $authorizationChecker = $this->createAuthorizationChecker();
        $csrfTokenManager = $this->createCsrfTokenManager($tokenValue);
        $this->engine->addExtension(new SecurityExtension($authorizationChecker, $csrfTokenManager));

        $this->assertEquals($tokenValue, $this->engine->render($template));
    }

    protected function createSecurityTokenStorage()
    {
        $tokenStorage = new TokenStorage();

        $tokenStorage->setToken($token = $this->getMock(TokenInterface::class));
        $token
            ->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;

        return $tokenStorage;
    }

    protected function createAuthorizationChecker($granted = false)
    {
        $authManager = $this->getMock(AuthenticationManagerInterface::class);
        $decisionManager = $this->getMock(AccessDecisionManagerInterface::class);
        $decisionManager->expects($this->any())->method('decide')->will($this->returnValue($granted));

        return new AuthorizationChecker(
            $this->createSecurityTokenStorage(),
            $authManager,
            $decisionManager
        );
    }

    protected function createCsrfTokenManager($value)
    {
        $csrfToken = $this->getMock('stdClass', ['getValue']);
        $csrfToken->expects($this->any())->method('getValue')->will($this->returnValue($value));
        $csrfTokenManager = $this->getMockForAbstractClass(CsrfTokenManagerInterface::class, ['getToken', 'refreshToken', 'removeToken', 'isTokenValid']);
        $csrfTokenManager->expects($this->any())->method('getToken')->will($this->returnValue($csrfToken));

        return $csrfTokenManager;
    }
}
