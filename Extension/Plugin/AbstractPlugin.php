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

namespace NoiseLabs\Bundle\SmartyBundle\Extension\Plugin;

use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;

/**
 * The Plugin base class represents a OO approach to the Smarty plugin
 * architecture.
 *
 * See {@link http://www.smarty.net/docs/en/plugins.tpl}.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * Available plugin types.
     * @var array
     */
    protected static $types = array('function', 'modifier', 'block',
    'compiler', 'prefilter', 'postfilter', 'outputfilter', 'resource',
    'insert');
    protected $name;
    protected $extension;
    protected $method;

    /**
     * Constructor.
     *
     * @param string             $name      The plugin name
     * @param ExtensionInterface $extension A ExtensionInterface instance
     * @param string             $method    Method name
     */
    public function __construct($name, ExtensionInterface $extension, $method)
    {
        $this->name = $name;
        $this->extension = $extension;
        $this->method = $method;
    }

    /**
     * Get the plugin name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the plugin name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return the plugin callback.
     */
    public function getCallback()
    {
        return array($this->extension, $this->method);
    }

    /**
     * Return the Extension.
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Check if type is in the supported list.
     */
    public function validateType()
    {
        if (!in_array($this->getType(), static::$types)) {
            throw new \RuntimeException("Plugin type: '".$this->getType()."' is not allowed.");
        }
    }
}
