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
 * Block functions are functions of the form: {func} .. {/func}. In other
 * words, they enclose a template block and operate on the contents of this
 * block. Block functions take precedence over custom functions of the same
 * name, that is, you cannot have both custom function {func} and block
 * function {func}..{/func}.
 *
 * By default your function implementation is called twice by Smarty: once for
 * the opening tag, and once for the closing tag. (See $repeat below on how to
 * change this).
 *
 * Starting with Smarty 3.1 the returned value of the opening tag call is
 * displayed as well.
 *
 * Only the opening tag of the block function may have attributes. All
 * attributes passed to template functions from the template are contained in
 * the $params variable as an associative array. The opening tag attributes are
 * also accessible to your function when processing the closing tag.
 *
 * The value of the $content variable depends on whether your function is
 * called for the opening or closing tag. In case of the opening tag, it will
 * be NULL, and in case of the closing tag it will be the contents of the
 * template block. Note that the template block will have already been
 * processed by Smarty, so all you will receive is the template output, not the
 * template source.
 *
 * The parameter $repeat is passed by reference to the function implementation
 * and provides a possibility for it to control how many times the block is
 * displayed. By default $repeat is TRUE at the first call of the block
 * -function (the opening tag) and FALSE on all subsequent calls to the block
 * function (the block's closing tag). Each time the function implementation
 * returns with $repeat being TRUE, the contents between {func}...{/func} are
 * evaluated and the function implementation is called again with the new block
 * contents in the parameter $content.
 *
 * If you have nested block functions, it's possible to find out what the
 * parent block function is by accessing $smarty->_tag_stack variable. Just do
 * a var_dump() on it and the structure should be apparent.
 *
 * See {@link http://www.smarty.net/docs/en/plugins.functions.tpl} and {@link http://www.smarty.net/docs/en/plugins.block.functions.tpl}.
 *
 * The callback for this class must use the following parameters:
 * <code>
 * void function($params, $content, $template, &$repeat);
 * </code>
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class BlockPlugin extends AbstractPlugin
{
    public function getType()
    {
        return 'block';
    }
}
