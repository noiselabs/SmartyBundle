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
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * SmartyBundle extension for Symfony actions helper.
 *
 * This extension tries to provide the same functionality described in
 * {@link http://symfony.com/doc/current/book/security.html#book-security-template-expression}.
 *
 * @author Matt Labrum
 */
class ExpressionExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return [
            new ModifierPlugin('expression', $this, 'createExpression'),
        ];
    }

    /**
     * Creates an expression from a string.
     *
     * @param string $expression A symfony expression
     *
     * @see Symfony\Bridge\Twig\Extension\ExpressionExtension::createExpression
     */
    public function createExpression($expression)
    {
        return new Expression($expression);
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
        return 'expression';
    }
}
