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

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * SecurityExtension exposes security context features.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class SecurityExtension extends AbstractExtension
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    protected $csrfTokenManager;

    /**
     * Constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker = null, $csrfTokenManager = null)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new ModifierPlugin('is_granted', $this, 'isGranted'),
            new ModifierPlugin('csrf_token', $this, 'getCsrfToken'),
        );
    }

    public function isGranted($role, $object = null, $field = null)
    {
        if (null === $this->authorizationChecker) {
            return false;
        }

        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        return $this->authorizationChecker->isGranted($role, $object);
    }

    public function getCsrfToken($tokenId)
    {
        if ($this->csrfTokenManager instanceof CsrfProviderInterface) {
            $tokenValue = $this->csrfTokenManager->generateCsrfToken($tokenId);
        }
        elseif ($this->csrfTokenManager instanceof CsrfTokenManagerInterface) {
            $tokenValue = $this->csrfTokenManager->getToken($tokenId)->getValue();
        } else {
            $this->csrfTokenManager = null;
        }

        if (null === $this->csrfTokenManager) {
            throw new RuntimeException('CSRF tokens can only be generated if a CsrfProviderInterface or CsrfTokenManagerInterface is injected in SecurityExtension::__construct().');
        }

        return $tokenValue;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'security';
    }
}
