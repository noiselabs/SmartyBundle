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
 * Copyright (C) 2011-2014 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 */

 namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use Assetic\AssetManager;
use Assetic\FilterManager;
use Assetic\Filter;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;

if (isset($_SERVER['LESSPHP'])) {
    require_once $_SERVER['LESSPHP'];
}

/**
 * A non-Symfony, standalone, implementation that provides Smarty integration for Assetic.
 *
 * Assetic allows assets (JavaScript, stylesheets) to be included in a smart way,
 * making minification and caching easier. It also allows the use of various filters
 * on your assets.
 *
 * @author Pierre-Jean Parra <parra.pj@gmail.com>
 * @author Vítor Brandão <vitor@noiselabs.com>
 *
 * Pierre-Jean Parra articles about Assetic and Smarty:
 * - {@link http://blog.pierrejeanparra.com/2011/12/assets-management-assetic-and-smarty/}
 * - {@link https://github.com/pjparra/assetic-smarty/blob/master/README.md}
 *
 * Assetic documentation:
 * - {@link https://github.com/kriswallsmith/assetic/blob/master/README.md}
 */
 class StandaloneAsseticExtension extends AbstractExtension
 {
    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('assetic', $this, 'asseticBlock'),
        );
    }

    /**
     * Returns the public path of an asset
     *
     * @return string A public path
     */
    public function asseticBlock(array $params = array(), $content = null, $template, &$repeat)
    {
        // In debug mode, we have to be able to loop a certain number of times, so we use a static counter
        static $count;
        static $assetsUrls;

        // Read config file
        if (isset($params['config_path'])) {
            $base_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $params['config_path'];
        } else {
            // Find the config file in Symfony2 config dir
            $base_path = __DIR__.'/../../../../app/config/smarty-assetic';
        }

        $config = json_decode(file_get_contents($base_path . '/config.json'));

        // Opening tag (first call only)
        if ($repeat) {
            // Read bundles and dependencies config files
            $bundles = json_decode(file_get_contents($base_path . '/bundles.json'));
            $dependencies = json_decode(file_get_contents($base_path . '/dependencies.json'));

            $am = new AssetManager();

            $fm = new FilterManager();

            $fm->set('yui_js', new Filter\Yui\JsCompressorFilter($config->yuicompressor_path, $config->java_path));
            $fm->set('yui_css', new Filter\Yui\CssCompressorFilter($config->yuicompressor_path, $config->java_path));
            $fm->set('less', new Filter\LessphpFilter());
            $fm->set('sass', new Filter\Sass\SassFilter());
            $fm->set('closure_api', new Filter\GoogleClosure\CompilerApiFilter());
            $fm->set('closure_jar', new Filter\GoogleClosure\CompilerJarFilter($config->closurejar_path, $config->java_path));

            // Factory setup
            $factory = new AssetFactory($_SERVER['DOCUMENT_ROOT']);
            $factory->setAssetManager($am);
            $factory->setFilterManager($fm);
            $factory->setDefaultOutput('assetic/*.'.$params['output']);

            if (isset($params['filter'])) {
                $filters = explode(',', $params['filter']);
            } else {
                $filters = array();
            }

            // Prepare the assets writer
            $writer = new AssetWriter($params['build_path']);

            // If a bundle name is provided
            if (isset($params['bundle'])) {
                $asset = $factory->createAsset(
                    $bundles->$params['output']->$params['bundle'],
                    $filters,
                    array($params['debug'])
                );

                $cache = new AssetCache(
                    $asset,
                    new FilesystemCache($params['build_path'])
                );

                $writer->writeAsset($cache);
            // If individual assets are provided
            } elseif (isset($params['assets'])) {
                $assets = array();
                // Include only the references first
                foreach (explode(',', $params['assets']) as $a) {
                    // If the asset is found in the dependencies file, let's create it
                    // If it is not found in the assets but is needed by another asset and found in the references, don't worry, it will be automatically created
                    if (isset($dependencies->$params['output']->assets->$a)) {
                        // Create the reference assets if they don't exist
                        foreach ($dependencies->$params['output']->assets->$a as $ref) {
                            try {
                                $am->get($ref);
                            } catch (InvalidArgumentException $e) {
                                $assetTmp = $factory->createAsset(
                                    $dependencies->$params['output']->references->$ref
                                );
                                $am->set($ref, $assetTmp);
                                $assets[] = '@'.$ref;
                            }
                        }
                    }
                }

                // Now, include assets
                foreach (explode(',', $params['assets']) as $a) {
                    // Add the asset to the list if not already present, as a reference or as a simple asset
                    $ref = null;
                    if (isset($dependencies->$params['output'])) {
                        foreach ($dependencies->$params['output']->references as $name => $file) {
                            if ($file == $a) {
                                $ref = $name;
                                break;
                            }
                        }
                    }

                    if (array_search($a, $assets) === FALSE && ($ref === null || array_search('@' . $ref, $assets) === FALSE)) {
                        $assets[] = $a;
                    }
                }

                // Create the asset
                $asset = $factory->createAsset(
                    $assets,
                    $filters,
                    array($params['debug'])
                );

                $cache = new AssetCache(
                    $asset,
                    new FilesystemCache($params['build_path'])
                );

                $writer->writeAsset($cache);
            }

            // If debug mode is active, we want to include assets separately
            if ($params['debug']) {
                $assetsUrls = array();
                foreach ($asset as $a) {
                    $cache = new AssetCache(
                        $a,
                        new FilesystemCache($params['build_path'])
                    );
                    $writer->writeAsset($cache);
                    $assetsUrls[] = $a->getTargetPath();
                }

                // It's easier to fetch the array backwards, so we reverse it to insert assets in the right order
                $assetsUrls = array_reverse($assetsUrls);

                $count = count($assetsUrls);

                if (isset($config->site_url)) {
                    $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
                } else {
                    $template->assign($params['asset_url'], '/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
                }
            // Production mode, include an all-in-one asset
            } else {
                if (isset($config->site_url)) {
                    $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$asset->getTargetPath());
                } else {
                    $template->assign($params['asset_url'], '/'.$params['build_path'].'/'.$asset->getTargetPath());
                }
            }

        // Closing tag
        } else {
            if (isset($content)) {
                // If debug mode is active, we want to include assets separately
                if ($params['debug']) {
                    $count--;
                    if ($count > 0) {
                        if (isset($config->site_url)) {
                            $template->assign($params['asset_url'], $config->site_url.'/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
                        } else {
                            $template->assign($params['asset_url'], '/'.$params['build_path'].'/'.$assetsUrls[$count-1]);
                        }
                    }
                    $repeat = $count > 0;
                }

                return $content;
            }
        }
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'standalone-assetic';
    }
}
