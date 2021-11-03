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

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;

/**
 * Some useful modifiers not available in the Smarty distribution.
 *
 * @see Twig_Extension_Core
 */
class CoreExtension extends AbstractExtension
{
    /**
     * @var string
     */
    protected $charset;

    public function __construct($charset = 'UTF-8')
    {
        $this->charset = $charset;
    }

    public function getPlugins()
    {
        return [
            new ModifierPlugin('length', $this, 'length_modifier'),
        ];
    }

    /**
     * Sets the default template charset.
     *
     * @param string $charset The default charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Gets the default template charset.
     *
     * @return string The default charset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Returns the length of a variable.
     *
     * @param mixed $thing A variable
     *
     * @return int The length of the value
     */
    public function length_modifier($thing)
    {
        // add multibyte extensions if possible
        if (function_exists('mb_get_info')) {
            return is_scalar($thing) ? mb_strlen($thing, $this->getCharset()) : count($thing);
            // and byte fallback
        }

        return is_scalar($thing) ? strlen($thing) : count($thing);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'smartybundle_core';
    }
}
