<?php
/*
 * This file is part of the NoiseLabs-SmartyBundle package.
 *
 * Copyright (c) 2011-2019 Vítor Brandão <vitor@noiselabs.io>
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

namespace NoiseLabs\Bundle\SmartyBundle\Loader;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;

class FileLocatorFactory
{
    /**
     * @param KernelInterface $kernel A KernelInterface instance
     * @param string|null $path The path the global resource directory
     * @param array $paths An array of paths where to look for resources
     * @param array $extraTemplatePaths
     *
     * @return FileLocator
     */
    public static function createFileLocator(KernelInterface $kernel, $path, array $paths, array $extraTemplatePaths)
    {
        return new FileLocator($kernel, $path, array_merge($paths, $extraTemplatePaths));
    }
}