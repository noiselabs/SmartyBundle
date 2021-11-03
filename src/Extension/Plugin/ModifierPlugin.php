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

/**
 * Variable modifiers can be applied to variables, custom functions or strings.
 * To apply a modifier, specify the value followed by a | (pipe) and the
 * modifier name. A modifier may accept additional parameters that affect its
 * behavior. These parameters follow the modifier name and are separated by a :
 * (colon). Also, all php-functions can be used as modifiers implicitly (more
 * below) and modifiers can be combined.
 *
 * See {@link http://www.smarty.net/docs/en/language.modifiers.tpl}.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class ModifierPlugin extends AbstractPlugin
{
    public function getType()
    {
        return 'modifier';
    }
}
