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

namespace NoiseLabs\Bundle\SmartyBundle\Extension\Plugin;

use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;

/**
 * The Plugin base class represents an OO approach to the Smarty plugin
 * architecture.
 *
 * See {@link http://www.smarty.net/docs/en/plugins.tpl}.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * Available plugin types.
     *
     * @var array
     */
    protected static $types = [
        PluginInterface::TYPE_BLOCK,
        PluginInterface::TYPE_COMPILER,
        PluginInterface::TYPE_FUNCTION,
        PluginInterface::TYPE_INSERT,
        PluginInterface::TYPE_MODIFIER,
        PluginInterface::TYPE_OUTPUTFILTER,
        PluginInterface::TYPE_POSTFILTER,
        PluginInterface::TYPE_PREFILTER,
        PluginInterface::TYPE_RESOURCE,
    ];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ExtensionInterface
     */
    protected $extension;

    /**
     * @var string
     */
    protected $method;

    /**
     * Constructor.
     *
     * @param string             $name      The plugin name
     * @param ExtensionInterface $extension A ExtensionInterface instance
     * @param string             $method    Method name
     */
    public function __construct(string $name, ExtensionInterface $extension, string $method)
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
     *
     * @param mixed $name
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
        return [$this->extension, $this->method];
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
