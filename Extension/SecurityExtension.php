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
 * Copyright (C) 2011-2015 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * SecurityExtension exposes security context features.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class SecurityExtension extends AbstractExtension
{
    protected $context;
    protected $csrfTokenManager;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $context A SecurityContext instance
     */
    public function __construct(SecurityContextInterface $context = null, CsrfTokenManagerInterface $csrfTokenManager = null)
    {
        $this->context = $context;
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
        if (null === $this->context) {
            return false;
        }

        if (null !== $field) {
            $object = new FieldVote($object, $field);
        }

        return $this->context->isGranted($role, $object);
    }

    public function getCsrfToken($tokenId)
    {
        if (null === $this->csrfTokenManager) {
            throw new RuntimeException('CSRF tokens can only be generated if a CsrfTokenManagerInterface is injected in SecurityExtension::__construct().');
        }

        return $this->csrfTokenManager->getToken($tokenId)->getValue();
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
