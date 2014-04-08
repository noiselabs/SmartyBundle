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

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use Assetic\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use Assetic\ValueSupplierInterface;

/**
 * Provides Smarty integration for Assetic Symfony2 component
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
 *
 * Assetic in Symfony2:
 * - {@link http://symfony.com/doc/2.0/cookbook/assetic/asset_management.html}
 *
 * Asset variables in Assetic
 * - {@link http://jmsyst.com/blog/asset-variables-in-assetic}
 * - {@link https://github.com/kriswallsmith/assetic/pull/142}
 */
abstract class AsseticExtension extends AbstractExtension
{
    const OPTION_SMARTY_BLOCK_NAME = '_smarty_block_name';
    protected $factory;
    protected $useController;
    protected $valueSupplier;
    protected $defaultOutput;

    /**
     * Constructor.
     *
     * @param AssetFactory           $factory       The asset factory
     * @param boolean                $useController Handle assets dynamically
     * @param ValueSupplierInterface $valueSupplier Runtime values for compile-time variables.
     */
    public function __construct(AssetFactory $factory, $useController = false, ValueSupplierInterface $valueSupplier = null)
    {
        $this->factory = $factory;
        $this->useController = $useController;
        $this->valueSupplier = $valueSupplier;

        $this->defaultOutput = array(
            'javascripts'   => 'js/*.js',
            'stylesheets'   => 'css/*.css',
            'image'         => 'images/*',
        );
    }

    public function getAssetFactory()
    {
        return $this->factory;
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
        );
    }

    public function javascriptsBlock(array $params = array(), $content = null, $template, &$repeat)
    {
        $params[self::OPTION_SMARTY_BLOCK_NAME] = 'javascripts';

        return $this->asseticBlock($params, $content, $template, $repeat);
    }

    public function stylesheetsBlock(array $params = array(), $content = null, $template, &$repeat)
    {
        $params[self::OPTION_SMARTY_BLOCK_NAME] = 'stylesheets';

        return $this->asseticBlock($params, $content, $template, $repeat);
    }

    public function imageBlock(array $params = array(), $content = null, $template, &$repeat)
    {
        $params[self::OPTION_SMARTY_BLOCK_NAME] = 'image';
        $params['single'] = true;

        return $this->asseticBlock($params, $content, $template, $repeat);
    }

    /**
     * @param array $params An array of parameters coming straight from the
     * Smarty block.
     *
     * @return array ($inputs, $filters, $params)
     */
    public function buildAttributes(array $params = array())
    {
        $explode = function($value) {
            return array_map('trim', explode(',', $value));
        };

        if (!isset($params[self::OPTION_SMARTY_BLOCK_NAME])) {
            throw new \RuntimeException('The Smarty block name is undefined');
        }

        if (!isset($params['output'])) {
            $params['output'] = $this->defaultOutput[$params[self::OPTION_SMARTY_BLOCK_NAME]];
        }

        /*
         * The variable name that will be used to pass the asset URL to the
         * <link> tag
         */
        if (isset($params['as'])) {
            $params['var_name'] = $params['as'];
        } elseif (!isset($params['var_name'])) {
            $params['var_name'] = 'asset_url';
        }

        if (!isset($params['vars'])) {
            $params['vars'] = array();
        }

        if (isset($params['assets'])) {
            $inputs = $explode($params['assets']);
            unset($params['assets']);
        } else {
            $inputs = array();
        }

        if (isset($params['filter'])) {
            $filters = $explode($params['filter']);
            unset($params['filter']);
        } else {
            $filters = array();
        }

        if (!isset($params['debug'])) {
            $params['debug'] = $this->factory->isDebug();
        }

        if (!isset($params['combine'])) {
            $params['combine'] = null;
        }

        if (isset($params['single']) && $params['single'] && 1 < count($inputs)) {
            $inputs = array_slice($inputs, -1);
        }

        // Replace [foo] with {foo}
        if (!empty($params['vars'])) {
            $vars = implode('|', $params['vars']);
            foreach (array_keys($inputs) as $k) {
                $inputs[$k] = preg_replace("/(.*?)\[(".$vars.")\](.*?)/i", '$1{$2}$3', $inputs[$k]);
            }
        }

        if (!isset($params['name'])) {
            $params['name'] = $this->factory->generateAssetName($inputs, $filters, $params);
        }

        return array($inputs, $filters, $params);
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
            list($inputs, $filters, $options) = $this->buildAttributes($params);

            $asset = $this->factory->createAsset($inputs, $filters, $options);

            $one = $this->getAssetUrl($asset, $options);
            $many = array();
            if ($options['combine']) {
                $many[] = $one;
            } else {
                $i = 0;
                foreach ($asset as $leaf) {
                    $many[] = $this->getAssetUrl($leaf, array_replace($options, array(
                        'name' => $options['name'].'_'.$i++,
                    )));
                }
            }

            $store['urls'] = $many;
            $store['debug'] = $options['debug'];
            $store['count'] = count($store['urls']);

            if (!empty($options['vars'])) {
                $store['urls'] = $this->replaceVars($store['urls'], $options['vars']);
            }

            // If debug mode is active, we want to include assets separately
            if ($store['count']>0 && $options['debug']) {
                // save parameters for next block calls until $repeat reaches 0
                $store['debug'] = $options['debug'];
                $store['varName'] = $options['var_name'];

                $store['urls'] = array_reverse($store['urls']);
                $template->assign($options['var_name'], $store['urls'][$store['count']-1]);

            // Production mode, include an all-in-one asset
            } else {
                $template->assign($options['var_name'], $one);
            }

        // Closing tag
        } else {
            if (isset($content) && !empty($store['urls'])) {
                // If debug mode is active, we want to include assets separately
                if ($store['debug']) {
                    $store['count']--;
                    if ($store['count'] > 0) {
                        $template->assign($store['varName'], $store['urls'][$store['count']-1]);
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
     * @param array          $options An array of options
     *
     * @return string An echo-ready URL
     */
    abstract protected function getAssetUrl(AssetInterface $asset, array $options = array());

    public function getGlobals()
    {
        return array(
            'assetic' => array(
                'debug'             => $this->factory->isDebug(),
                'use_controller'    => $this->useController,
                'vars'              => $this->getVarValues(),
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

    protected function getVarValues()
    {
        return null !== $this->valueSupplier ? $this->valueSupplier->getValues() : array();
    }

    /**
     * Process the urls arrays for vars and replace the placeholder with the
     * value set in the ValueSupplierInterface instance.
     *
     * @return array Urls array after the vars replacement.
     */
    protected function replaceVars($urls, $vars)
    {
        $patterns = array();
        $replacements = array();
        $values = $this->getVarValues();

        foreach (array_keys($vars) as $k) {
            $patterns[$k] = '/\{'.$vars[$k].'\}/';
            $replacements[$k] = $values[$vars[$k]];
        }

        foreach (array_keys($urls) as $k) {
            $urls[$k] = preg_replace($patterns, $replacements, $urls[$k]);
        }

        return $urls;
    }
}
