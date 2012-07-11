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
 * Copyright (C) 2011-2012 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2012 Vítor Brandão <noisebleed@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SmartyBundle extension for Symfony actions helper.
 *
 * This extension tries to provide the same funcionality described in
 * {@link http://symfony.com/doc/current/book/templating.html#embedding-controllers}.
 *
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class ActionsExtension extends AbstractExtension
{
    protected $container;

    /**
     * Constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('render', $this, 'renderBlockAction'),
            new ModifierPlugin('render', $this, 'renderModifierAction')
        );
    }

    /**
     * Returns the Response content for a given controller or URI.
     *
     * @param string $controller A controller name to execute (a string like BlogBundle:Post:index), or a relative URI
     *
     * @see Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper::render()
     * @see Symfony\Bundle\TwigBundle\Extension\ActionsExtension::renderAction()
     */
    public function renderBlockAction(array $parameters = array(), $controller, $template, &$repeat)
    {
        // only output on the closing tag
        if (!$repeat) {
            $parameters = array_merge(array(
                'attributes'    => array(),
                'options'       => array()
            ), $parameters);

            return $this->container->get('templating.helper.actions')->render($controller, $parameters['attributes'], $parameters['options']);
        }
    }

    /**
     * Returns the Response content for a given controller or URI.
     *
     * @param string $controller A controller name to execute (a string like BlogBundle:Post:index), or a relative URI
     *
     * @see Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper::render()
     * @see Symfony\Bundle\TwigBundle\Extension\ActionsExtension::renderAction()
     */
    public function renderModifierAction($controller, array $attributes = array(), array $options = array())
    {
        return $this->container->get('templating.helper.actions')->render($controller, $attributes, $options);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     *
     * @since  0.1.0
     */
    public function getName()
    {
        return 'actions';
    }
}