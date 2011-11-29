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

namespace NoiseLabs\Bundle\SmartyBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as SymfonyGlobalVariables;

/**
 * Extends the GlobalVariables found in FrameworkBundle to allow access to the
 * container using ArrayAccess.
 *
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class GlobalVariables extends SymfonyGlobalVariables implements \ArrayAccess, \IteratorAggregate, \Countable
{
	/**
	 * Set behavior is disabled.
	 *
	 * It could be enabled by replacing the return statement with:
	 * <code>
	 * $this->container->set($offset, $value);
	 * </code>
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function offsetSet($offset, $value)
	{
		return false;
    }

	/**
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function offsetExists($offset)
	{
		return $this->container->has($offset);
    }

	/**
	 * Unset behavior is disabled.
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function offsetUnset($offset)
	{
		return false;
	}

	/**
	 * Returns a service from the container.
	 *
	 * @since  0.1.0
	 * @author Vítor Brandão <noisebleed@noiselabs.org>
	 */
	public function offsetGet($offset)
	{
		return $this->container->get($offset);
	}

	/**
	 * Returns the iterator for this group.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->container);
	}

	/**
	 * Implements the \Countable interface
	 *
	 * @return integer The number of sections
	 */
	public function count()
	{
		return count($this->container);
	}
}
