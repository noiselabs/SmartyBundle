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
* Copyright (C) 2011 Vítor Brandão
*
* @category    NoiseLabs
* @package     SmartyBundle
* @author      Vítor Brandão <noisebleed@noiselabs.org>
* @copyright   (C) 2011 Vítor Brandão <noisebleed@noiselabs.org>
* @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
* @link        http://www.noiselabs.org
* @since       0.1.0
*/

namespace NoiseLabs\Bundle\SmartyBundle\Extension\Filter;

use NoiseLabs\Bundle\SmartyBundle\Extension\ExtensionInterface;

/**
* The Plugin base class represents a OO approach to the Smarty plugin
* architecture.
*
* See {@link http://www.smarty.net/docs/en/api.register.filter.tpl}.
*
* @since  0.1.0
* @author Vítor Brandão <noisebleed@noiselabs.org>
*/
abstract class Filter implements FilterInterface
{
    /**
     * Available filter types.
     * @var array
     */
    protected static $types = array('pre', 'post', 'output', 'variable');
    protected $extension;
    protected $method;

    public function __construct(ExtensionInterface $extension, $method)
    {
        $this->extension = $extension;
        $this->method = $method;
    }

    public function getCallback()
    {
        return array($this->extension, $this->method);
    }

    public function validateType()
    {
        if (!in_array($this->getType(), static::$types)) {
            throw new \RuntimeException("Filter type: '".$this->getType()."' is not allowed.");
        }
    }
}
