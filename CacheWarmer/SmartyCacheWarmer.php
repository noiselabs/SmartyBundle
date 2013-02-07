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
 * Copyright (C) 2011-2012 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2012 Vítor Brandão <noisebleed@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;

/**
 * Generates the Smarty cache for all templates.
 *
 * @author Thomas Rabaix <rabaix@fullsix.com>
 * @author Fabien Potencier <fabien.potencier@symfony.com>
 */
class SmartyCacheWarmer implements CacheWarmerInterface
{
    protected $container;
    protected $warmer;

    /**
     * Constructor.
     *
     * @param ContainerInterface      $container The dependency injection container
     * @param TemplateFinderInterface $finder    The template paths cache warmer
     */
    public function __construct(ContainerInterface $container, TemplateFinderInterface $finder)
    {
        // We don't inject the Twig environment directly as it depends on the
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
        $smarty = $this->container->get('templating.engine.smarty');

        foreach ($this->finder->findAllTemplates() as $template) {
            if ('smarty' !== $template->get('engine')) {
                continue;
            }

            try {
                $smarty->createTemplate($template);
            } catch (\Exception $e) {
                // problem during compilation, give up
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
