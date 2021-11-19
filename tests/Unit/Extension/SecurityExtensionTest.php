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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\SecurityExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Test suite for the security extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
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

        self::assertInstanceOf(SecurityExtension::class, $this->engine->getExtension('security'));
    }

    /**
     * @dataProvider getIsGrantedTests
     *
     * @param mixed $content
     * @param mixed $expected
     * @param mixed $granted
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
            ['{if "IS_AUTHENTICATED_FULLY"|is_granted}access granted{else}access denied{/if}', 'access denied', false],
        ];
    }

    public function testCsrfTokenWithCsrfTokenManager()
    {
        $tokenId = 'foo';
        $tokenValue = 'xsrf';
        $content = "{'{$tokenId}'|csrf_token}";
        $template = 'csrf_token_manager_test.html.tpl';

        // symfony 2.3+
        $this->engine->setTemplate($template, $content);
        $authorizationChecker = $this->createAuthorizationChecker();
        $csrfTokenManager = $this->createCsrfTokenManager($tokenValue);
        $this->engine->addExtension(new SecurityExtension($authorizationChecker, $csrfTokenManager));

        $this->assertEquals($tokenValue, $this->engine->render($template));
    }

    private function createSecurityTokenStorage()
    {
        $tokenStorage = new TokenStorage();

        $tokenStorage->setToken($token = $this->createMock(TokenInterface::class));
        $token
            ->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true))
        ;

        return $tokenStorage;
    }

    private function createAuthorizationChecker($granted = false)
    {
        $authManager = $this->createMock(AuthenticationManagerInterface::class);
        $decisionManager = $this->createMock(AccessDecisionManagerInterface::class);
        $decisionManager->expects($this->any())->method('decide')->will($this->returnValue($granted));

        return new AuthorizationChecker(
            $this->createSecurityTokenStorage(),
            $authManager,
            $decisionManager
        );
    }

    private function createCsrfTokenManager($value)
    {
        $csrfToken = $this->createMock(CsrfToken::class);
        $csrfToken->expects($this->any())->method('getValue')->will($this->returnValue($value));
        //$csrfTokenManager = $this->getMockForAbstractClass(CsrfTokenManagerInterface::class, array('getToken',
        // 'refreshToken', 'removeToken', 'isTokenValid'));
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $csrfTokenManager->expects($this->any())->method('getToken')->will($this->returnValue($csrfToken));

        return $csrfTokenManager;
    }
}
