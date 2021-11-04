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

namespace NoiseLabs\Bundle\SmartyBundle\Extension\Filter;

use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;
use RuntimeException;

/**
 * The Plugin base class represents an OO approach to the Smarty plugin architecture.
 *
 * See {@link http://www.smarty.net/docs/en/api.register.filter.tpl}.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * Available filter types.
     *
     * @var array
     */
    protected static $types = ['pre', 'post', 'output', 'variable'];

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
     * @param string             $name      The filter name
     * @param ExtensionInterface $extension A ExtensionInterface instance
     * @param string             $method    Method name
     */
    public function __construct($name, ExtensionInterface $extension, $method)
    {
        $this->name = $name;
        $this->extension = $extension;
        $this->method = $method;
    }

    public function getCallback()
    {
        return [$this->extension, $this->method];
    }

    public function validateType()
    {
        if (!in_array($this->getType(), static::$types)) {
            throw new RuntimeException("Filter type: '".$this->getType()."' is not allowed.");
        }
    }
}
