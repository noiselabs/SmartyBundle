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

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Provides integration of the Routing component with Smarty[Bundle].
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class RoutingExtension extends AbstractExtension
{
    protected $generator;

    /**
     * Constructor.
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('path', $this, 'getPath_block'),
            new ModifierPlugin('path', $this, 'getPath_modifier'),
            new BlockPlugin('url', $this, 'getUrl_block'),
            new ModifierPlugin('url', $this, 'getUrl_modifier')
        );
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $name       The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function getPath($name, $parameters = array())
    {
        return $this->generator->generate($name, $parameters, false);
    }

    /**
     * Generates an absolute URL from the given parameters.
     *
     * @param string $name       The name of the route
     * @param mixed  $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function getUrl($name, $parameters = array())
    {
        return $this->generator->generate($name, $parameters, true);
    }

    public function getPath_block(array $parameters = array(), $name = null, $template, &$repeat)
    {
        // only output on the closing tag
        if (!$repeat) {
            return $this->generator->generate($name, $parameters, false);
        }
    }

    public function getPath_modifier($name, array $parameters = array())
    {
        return $this->generator->generate($name, $parameters, false);
    }

    public function getUrl_block(array $parameters = array(), $name = null, $template, &$repeat)
    {
        // only output on the closing tag
        if (!$repeat) {
            return $this->generator->generate($name, $parameters, true);
        }
    }

    public function getUrl_modifier($name, array $parameters = array())
    {
        return $this->generator->generate($name, $parameters, true);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'routing';
    }
}
