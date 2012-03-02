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
 * @author      Vítor Brandão  <noisebleed@noiselabs.org>
 * @copyright   (C) 2011 Vítor Brandão  <noisebleed@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 * @since       0.1.0
 * 
 * @todo Use Symfony's built in assetic bundle/template helper so that the generated assets
 * are stored in the same places, can be cleared together, etc. Maybe they need to use
 * the same AssetManagers (which the AsseticBundle defines as a service) or Asset caches.
 * $this->container->get('templating.helper.assetic')
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\FunctionPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Assetic\AssetManager;
use Assetic\FilterManager;
use Assetic\Filter;
use Assetic\Factory\AssetFactory;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;

if (isset($_SERVER['LESSPHP']))
{
    require_once $_SERVER['LESSPHP'];
}


/**
 * Provides helper functions to link to assets (images, Javascript,
 * stylesheets, etc.).
 *
 * @since  0.1.0
 * @author Vítor Brandão  <noisebleed@noiselabs.org>
 */
class AssetsExtension extends AbstractExtension
{

    protected $container;

    /**
     * Constructor.
     *
     * @since  0.1.0
     * @author Vítor Brandão  <noisebleed@noiselabs.org>
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @since  0.1.0
     * @author Vítor Brandão  <noisebleed@noiselabs.org>
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('asset', $this, 'getAssetUrl_block'),
            new ModifierPlugin('asset', $this, 'getAssetUrl_modifier'),
            new FunctionPlugin('assets_version', $this, 'getAssetsVersion'),
            new BlockPlugin('assetic', $this, 'assetic_block')
        );
    }

    /**
     * Returns the public path of an asset
     *
     * Absolute paths (i.e. http://...) are returned unmodified.
     *
     * @param string $path        A public path
     *
     * @return string A public path which takes into account the base path and URL path
     *
     * @since  0.1.0
     * @author Vítor Brandão  <noisebleed@noiselabs.org>
     */
    public function getAssetUrl_block(array $parameters = array(), $path = null, $template, &$repeat)
    {
        // only output on the closing tag
        if (!$repeat) {
            $parameters = array_merge(array(
                'package'   => null,
            ), $parameters);

            return $this->container->get('templating.helper.assets')->getUrl($path, $parameters['package']);
        }
    }

    /**
     * Returns the public path of an asset
     *
     * Absolute paths (i.e. http://...) are returned unmodified.
     *
     * @param string $path        A public path
     *
     * @return string A public path which takes into account the base path and URL path
     *
     * @since  0.1.1
     * @author Vítor Brandão  <noisebleed@noiselabs.org>
     */
    public function getAssetUrl_modifier($path, $package = null)
    {
        return $this->container->get('templating.helper.assets')->getUrl($path, $package);
    }

    /**
     * Returns the version of the assets in a package
     *
     * @return int
     *
     * @since  0.1.0
     * @author Vítor Brandão  <noisebleed@noiselabs.org>
     */
    public function getAssetsVersion(array $parameters = array(), \Smarty_Internal_Template $template)
    {
        $parameters = array_merge(array(
                'package'   => null,
        ), $parameters);

        return $this->container->get('templating.helper.assets')->getVersion($parameters['package']);
    }

    /**
     * Provides Smarty integration for Assetic Symfony2 component
     * 
     * Assetic allows assets (JavaScript, stylesheets) to be included in a smart way,
     * making minification and caching easier. It also allows the use of various filters
     * on your assets.
     *
     * @since  0.1.0
     * @author Pierre-Jean Parra <parra.pj@gmail.com> and Ethan Resnick <hi@ethanresnick.com>
     */
    public function assetic_block(array $params = array(), $content = null, $template, &$repeat)
    {
        //In debug mode, we have to be able to loop a certain number of times, so we use a static counter
        static $count;
        static $assetsUrls;

        //Read config config file
        $config_base_path = $this->container->getParameter('kernel.root_dir') . '/' . (isset($params['config_path']) ? ($params['config_path']) : ('config/smarty-assetic'));
        $config = json_decode(file_get_contents($config_base_path . '/config.json'));
        
        //Set defaults
        $build_path = $this->container->getParameter('assetic.write_to');
        $debug      = isset($params['debug'])      ? $params['debug']      : $this->container->getParameter('kernel.debug');
        $asset_url  = isset($params['asset_url'])  ? $params['asset_url']  : 'asset_url'; 

        //Opening tag (first call only)
        if ($repeat) 
        {
            //Read bundles and dependencies config files
            $bundles = json_decode(file_get_contents($config_base_path . '/bundles.json'));
            $dependencies = json_decode(file_get_contents($config_base_path . '/dependencies.json'));


            //Build & configure requisite objects
            $am = new AssetManager();
            
            $fm = new FilterManager();
            $fm->set('yui_js', new Filter\Yui\JsCompressorFilter($config->yuicompressor_path, $config->java_path));
            $fm->set('yui_css', new Filter\Yui\CssCompressorFilter($config->yuicompressor_path, $config->java_path));
            $fm->set('less', new Filter\LessphpFilter());
            $fm->set('sass', new Filter\Sass\SassFilter());
            $fm->set('closure_api', new Filter\GoogleClosure\CompilerApiFilter());
            $fm->set('closure_jar', new Filter\GoogleClosure\CompilerJarFilter($config->closurejar_path, $config->java_path));          

            $factory = new AssetFactory($_SERVER['DOCUMENT_ROOT']);            
            $factory->setAssetManager($am);
            $factory->setFilterManager($fm);
            $factory->setDefaultOutput('assetic/*.'.$params['output']);

            $writer = new AssetWriter($build_path);

            //Determine lists of filters and assets for this group
            $filters = isset($params['filters']) ? explode(',', $params['filters']) : array();
            $assets  = isset($params['bundle'])  ? $bundles->$params['output']->$params['bundle'] : array();
            
            if (empty($assets) && isset($params['assets']))
            {
                // Include only the references first
                foreach (explode(',', $params['assets']) as $a)
                {
                    if (isset($dependencies->$params['output']->assets->$a)) 
                    {
                        //For each asset requested, look at what files that asset references
                        foreach ($dependencies->$params['output']->assets->$a as $ref)
                        {
                            //And create the reference assets if they don't exist
                            if(!$am->has($ref))
                            {
                                $assetTmp = $factory->createAsset(
                                        $dependencies->$params['output']->references->$ref
                                );
                                $am->set($ref, $assetTmp);
                                $assets[] = '@' . $ref;
                            }
                        }
                    }
                }
                
                //Now, include assets
                foreach (explode(',', $params['assets']) as $a)
                {
                    // Add the asset to the list if not already present, as a reference or as a simple asset
                    $ref = null;
                    if (isset($dependencies->$params['output']))
                    {
                        foreach ($dependencies->$params['output']->references as $name => $file)
                        {
                            if ($file == $a)
                            {
                                $ref = $name;
                                break;
                            }
                        }
                    }

                    if (array_search($a, $assets) === FALSE && ($ref === null || array_search('@' . $ref, $assets) === FALSE))
                    {
                        $assets[] = $a;
                    }
                }
            }
            
            //Actually build the assets and stuff
            if(isset($params['bundle']) || isset($params['assets']))
            {
                $assetCollection = $factory->createAsset($assets, $filters, array($debug));
                $cache = new AssetCache($assetCollection, new FilesystemCache($build_path));
                $writer->writeAsset($cache);
            }       
            
            // If debug mode is active, we want to include assets separately
            if ($debug)
            {
                $assetsUrls = array();
                foreach ($assetCollection as $a)
                {
                    $cache = new AssetCache(
                                    $a,
                                    new FilesystemCache($build_path)
                    );
                    $writer->writeAsset($cache);
                    $assetsUrls[] = $a->getTargetPath();
                }
                // It's easier to fetch the array backwards, so we reverse it to insert assets in the right order
                $assetsUrls = array_reverse($assetsUrls);

                $count = count($assetsUrls);

                $template->assign($asset_url, $this->getAssetUrl_modifier($assetsUrls[$count - 1]));


                // Production mode, include an all-in-one asset
            } 
            else
            {
                $template->assign(
                        $asset_url, 
                        $this->getAssetUrl_modifier($assetCollection->getTargetPath()));
            }


        }

        // Closing tag
        else
        {
            if (isset($content))
            {
                // If debug mode is active, we want to include assets separately
                if ($debug)
                {
                    $count--;
                    if ($count > 0)
                    {
                        $template->assign($asset_url, $this->getAssetUrl_modifier($assetsUrls[$count - 1]));
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
     *
     * @since  0.1.0
     * @author Vítor Brandão  <noisebleed@noiselabs.org>
     */
    public function getName()
    {
        return 'assets';
    }
}