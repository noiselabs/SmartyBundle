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
 * Copyright (C) 2011-2018 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2018 Vítor Brandão <vitor@noiselabs.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        https://www.noiselabs.io
 */

namespace NoiseLabs\Bundle\SmartyBundle\CacheWarmer;

use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException as SmartyBundleRuntimeException;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Generates the Smarty cache for all templates.
 *
 * @author Thomas Rabaix <rabaix@fullsix.com>
 * @author Fabien Potencier <fabien.potencier@symfony.com>
 */
class SmartyCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var TemplateFinderInterface
     */
    protected $finder;

    /**
     * Constructor.
     *
     * @param ContainerInterface      $container The dependency injection container
     * @param TemplateFinderInterface $finder    The template paths cache warmer
     */
    public function __construct(ContainerInterface $container, TemplateFinderInterface $finder)
    {
        // We don't inject the SmartyEngine directly as it depends on the
        // template locator (via the loader) which might be a cached one.
        // The cached template locator is available once the TemplatePathsCacheWarmer
        // has been warmed up
        $this->container = $container;
        $this->finder = $finder;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $engine = $this->container->get('templating.engine.smarty');
        $logger = $this->container->has('logger') ? $this->container->get('logger') : null;

        foreach ($this->finder->findAllTemplates() as $template) {
            try {
                $engine->compileTemplate($template, false);
            } catch (\Exception $e) {
                // problem during compilation, log it and give up
                $e = SmartyBundleRuntimeException::createFromPrevious($e, $template);
                if ($logger) {
                    $logger->warn(sprintf('Failed to compile Smarty template "%s": "%s"', (string) $template, $e->getMessage()));
                }
            }
        }
    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * @return Boolean always true
     */
    public function isOptional()
    {
        return true;
    }
}
