<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Templating;

use NoiseLabs\Bundle\SmartyBundle\Templating\GlobalVariables;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Fixtures\TokenInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GlobalVariablesTest extends TestCase
{
    private $container;
    private $globals;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->globals = new GlobalVariables($this->container);
    }

    public function testGetTokenNoTokenStorage()
    {
        $this->assertNull($this->globals->getToken());
    }

    public function testGetTokenNoToken()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->container->set('security.token_storage', $tokenStorage);
        $this->assertNull($this->globals->getToken());
    }

    public function testGetToken()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);

        $this->container->set('security.token_storage', $tokenStorage);

        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn('token')
        ;

        $this->assertSame('token', $this->globals->getToken());
    }

    public function testGetUserNoTokenStorage()
    {
        $this->assertNull($this->globals->getUser());
    }

    public function testGetUserNoToken()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->container->set('security.token_storage', $tokenStorage);
        $this->assertNull($this->globals->getUser());
    }

    /**
     * @dataProvider getUserProvider
     *
     * @param mixed $user
     * @param mixed $expectedUser
     */
    public function testGetUser($user, $expectedUser)
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $token = $this->createMock(TokenInterface::class);

        $this->container->set('security.token_storage', $tokenStorage);

        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;

        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token)
        ;

        $this->assertSame($expectedUser, $this->globals->getUser());
    }

    public function getUserProvider()
    {
        $user = $this->createMock(UserInterface::class);
        $std = new \stdClass();
        $token = $this->createMock(TokenInterface::class);

        return [
            [$user, $user],
            [$std, $std],
            [$token, $token],
            ['Anon.', null],
            [null, null],
            [10, null],
            [true, null],
        ];
    }
}
