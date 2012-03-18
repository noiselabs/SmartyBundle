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
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use Assetic\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Util\TraversableString;
use Symfony\Bundle\AsseticBundle\Exception\InvalidBundleException;

// non-Symfony
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
 * Provides Smarty integration for Assetic Symfony2 component
 *
 * Assetic allows assets (JavaScript, stylesheets) to be included in a smart way,
 * making minification and caching easier. It also allows the use of various filters
 * on your assets.
 *
 * @author Pierre-Jean Parra <parra.pj@gmail.com>
 * @author Vítor Brandão <noisebleed@noiselabs.com>
 *
 * Pierre-Jean Parra articles about Assetic and Smarty:
 * @link   http://blog.pierrejeanparra.com/2011/12/assets-management-assetic-and-smarty/
 * @link   https://github.com/pjparra/assetic-smarty/blob/master/README.md
 *
 * Assetic documentation:
 * @link   https://github.com/kriswallsmith/assetic/blob/master/README.md
 *
 * Assetic in Symfony2:
 * @link   http://symfony.com/doc/2.0/cookbook/assetic/asset_management.html
 */
abstract class AsseticExtension extends AbstractExtension
{
    protected $factory;
    protected $useController;

    /**
     * Constructor.
     *
     * @param AssetFactory     $factory          The asset factory
     * @param boolean          $useController    Handle assets dynamically
     */
    public function __construct(AssetFactory $factory, $useController = false)
    {
        $this->factory = $factory;
        $this->useController = $useController;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('javascripts', $this, 'javascriptsBlock'),
            new BlockPlugin('stylesheets', $this, 'stylesheetsBlock'),
            new BlockPlugin('image', $this, 'imageBlock'),
            new BlockPlugin('assetic', $this, 'nonSymfonyAsseticBlock'),
        );
    }

    public function javascriptsBlock(array $params = array(), $content = null, $template, &$repeat)
    {
        if (!isset($params['output'])) {
            $params['output'] = 'js/*.js';
        }

        $params['smartyBlockName'] = 'javascripts';

        return $this->asseticBlock($params, $content, $template, $repeat);
    }

    public function stylesheetsBlock(array $params = array(), $content = null, $template, &$repeat)
    {
        if (!isset($params['output'])) {
            $params['output'] = 'css/*.css';
        }

        $params['smartyBlockName'] = 'stylesheets';

        return $this->asseticBlock($params, $content, $template, $repeat);
    }

    public function imageBlock(array $params = array(), $content = null, $template, &$repeat)
    {
        if (!isset($params['output'])) {
            $params['output'] = 'images/*';
        }

        $params['single'] = true;
        $params['smartyBlockName'] = 'image';

        return $this->asseticBlock($params, $content, $template, $repeat);
    }

    /**
     * Generic block function called be every other block function (javascripts,
     * stylesheets, image).
     */
    protected function asseticBlock(array $params = array(), $content = null, $template, &$repeat)
    {
        /*
         * In debug mode, we have to be able to loop a certain number of times,
         * so we store some variables using a static array
         */
        static $store = array();

        // Opening tag (first call only)
        if ($repeat) {

            $explode = function($value) {
                return array_map('trim', explode(',', $value));
            };

            /*
             * The variable name that will be used to pass the asset URL to the
             * <link> tag
             */
            if (!isset($params['var_name'])) {
                $params['var_name'] = 'asset_url';
            }

            if (isset($params['assets'])) {
                $inputs = $explode($params['assets']);
                unset($params['assets']);
            } else {
                $inputs = array();
            }

            if (isset($params['filters'])) {
                $filters = $explode($params['filters']);
                unset($params['filters']);
            } else {
                $filters = array();
            }

            if (!isset($params['debug'])) {
                $params['debug'] = $this->factory->isDebug();
            }

            if (!isset($params['combine'])) {
                $params['combine'] = !$params['debug'];
            }

            if (isset($params['single']) && $params['single'] && 1 < count($inputs)) {
                $inputs = array_slice($inputs, -1);
            }

            if (!isset($params['name'])) {
                $params['name'] = $this->factory->generateAssetName($inputs, $filters, $params);
            }

            $asset = $this->factory->createAsset($inputs, $filters, $params);

            $one = $this->getAssetUrl($asset, $params);
            $many = array();
            if ($params['combine']) {
                $many[] = $one;
            } else {
                $i = 0;
                foreach ($asset as $leaf) {
                    $many[] = $this->getAssetUrl($leaf, array_replace($params, array(
                        'name' => $params['name'].'_'.$i++,
                    )));
                }
            }

            // AsseticHelper: $urls = new TraversableString($one, $many);
            $store['urls'] = $many;
            $store['debug'] = $params['debug'];
            $store['count'] = count($store['urls']);

            // If debug mode is active, we want to include assets separately
            if ($store['count']>0 && $params['debug']) {
                // save parameters for next block calls until $repeat reaches 0
                $store['debug'] = $params['debug'];
                $store['varName'] = $params['var_name'];


                $store['urls'] = array_reverse($store['urls']);
                $template->assign($params['var_name'], $store['urls'][$store['count']-1]);

            // Production mode, include an all-in-one asset
            } else {
                $template->assign($params['var_name'], $one);
            }

        // Closing tag
        } else {
            if (isset($content)) {
                // If debug mode is active, we want to include assets separately
                if ($store['debug']) {
                    $store['count']--;
                    if ($store['count'] > 0) {
                        $template->assign($store['varName'],$store['urls'][$store['count']-1]);
                    }
                    $repeat = $store['count'] > 0;
                }

                return $content;
            }
        }
    }

    /**
     * Returns an URL for the supplied asset.
     *
     * @param AssetInterface $asset   An asset
     * @param array          $params An array of options
     *
     * @return string An echo-ready URL
     */
    abstract protected function getAssetUrl(AssetInterface $asset, $params = array());
    
    /**
     * Returns the public path of an asset
     *
     * @return string A public path
     */
    public function nonSymfonyAsseticBlock(array $params = array(), $content = null, $template, &$repeat)
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

            if (isset($params['filters'])) {
                $filters = explode(',', $params['filters']);
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
                            }
                            catch (InvalidArgumentException $e) {
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

    public function getGlobals()
    {
        return array(
            'assetic' => array(
                'debug'             => $this->factory->isDebug(),
                'use_controller'    => $this->useController
        ));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'assetic';
    }

    /* TODO: Add the necessary corrections to include this method.
     *
     * Check the bundle
     *
     * @see Symfony\Bundle\AsseticBundle\Twig\AsseticTokenParser::parse()
     * @see Symfony\Bundle\AsseticBundle\Twig\AsseticNodeVisitor::leaveNode()

    [Resources/config/smarty.xml]

    <service id="smarty.extension.assetic" class="%smarty.extension.assetic.class%" public="false">
        <tag name="smarty.extension" />
        <argument type="service" id="assetic.asset_factory" />
        <argument type="service" id="templating.name_parser" />
        <argument>%assetic.use_controller%</argument>
        <argument>%assetic.bundles%</argument>
        <argument type="service" id="smarty.extension.assets" on-invalid="null"/>
        <argument type="service" id="smarty.extension.routing" on-invalid="null"/>
    </service>


    [Extension/AsseticExtension]

    // ...
    use Symfony\Component\Templating\TemplateNameParserInterface;

    class AsseticExtension extends AbstractExtension
    {
        protected $factory;
        protected $templateNameParser;
        protected $enabledBundles;
        protected $useController;
        protected $extension;
        protected $urlResolverExtension;

        public function __construct(AssetFactory $factory, TemplateNameParserInterface $templateNameParser, $useController = false, $enabledBundles = array(), AssetsExtension $assetsExtension = null, RoutingExtension $routingExtension = null)
        {
            $this->factory = $factory;
            $this->useController = $useController;
            $this->templateNameParser = $templateNameParser;
            $this->enabledBundles = $enabledBundles;

            //...
        }

        // ...

        protected function checkBundle($filename, $blockName)
        {
            if ($this->templateNameParser && is_array($this->enabledBundles)) {
                // check the bundle
                $templateRef = $this->templateNameParser->parse($filename);
                $bundle = $templateRef->get('bundle');
                if ($bundle && !in_array($bundle, $this->enabledBundles)) {
                    throw new InvalidBundleException($bundle, "the {$blockName} block function", $templateRef->getLogicalName(), $this->enabledBundles);
                }
            }
        }

    */
}
