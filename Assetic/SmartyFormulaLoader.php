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

namespace NoiseLabs\Bundle\SmartyBundle\Assetic;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\AsseticExtension;
use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;

/**
 * Loads asset formulae from Smarty templates.
 *
 * @author Vítor Brandão <vitor@noiselabs.com>
 */
class SmartyFormulaLoader implements FormulaLoaderInterface
{
    protected $engine;
    protected $extension;
    protected $factory;
    protected $tags = array();

    public function __construct(SmartyEngine $engine)
    {
        $this->engine = $engine;
        $this->extension = $this->engine->getExtension('assetic');
        $this->factory = $this->extension->getAssetFactory();

        $plugins = $engine->getPlugins('assetic');
        foreach (array_keys($plugins) as $k) {
            $this->tags[] = $plugins[$k]->getName();
        }
    }

    public function load(ResourceInterface $resource)
    {
        $formulae = array();

        // template source
        $templateSource = $resource->getContent();

        $smarty = $this->engine->getSmarty();
        // ask Smarty which delimiters to use
        $ldelim = $smarty->left_delimiter;
        $rdelim = $smarty->right_delimiter;
        $_ldelim = preg_quote($ldelim);
        $_rdelim = preg_quote($rdelim);

        // template block tags to look for
        $tags = implode('|', $this->tags);

        /**
         * Thanks Rodney!
         *
         * @see https://gist.github.com/483465490f738d1b2b5e
         */
        if (preg_match_all('#'.$_ldelim.'(?<type>'.$tags.').*?'.$_rdelim.'#s', $templateSource, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (preg_match_all('#(?<key>[a-zA-Z0-9_]+)\s*=\s*(["\']?)(?<value>[^\2]*?)\2(\s|'.$_rdelim.')#s', $match[0], $_matches, PREG_SET_ORDER)) {
                    $t = array(
                        'type'          => $match['type'],
                        'attributes'    => array(),
                    );

                    foreach ($_matches as $_match) {
                        if (empty($_match[2])) {
                            // make eval a little bit safer
                            preg_match('#[^\w|^\.]#', $_match['value'], $evalMatches);
                            $_match['value'] = ($evalMatches) ? null : eval(sprintf('return %s;', $_match['value']));
                        }
                        $t['attributes'][$_match['key']] = $_match['value'];
                    }

                    $formulae += $this->buildFormula($match['type'], $t['attributes']);
                }
            }
        }

        return $formulae;
    }

    /**
     * Builds assetic attributes from parameters extracted from template
     * source.
     *
     * @param string $blockName Smarty block name
     * @param array  $params    Block attributes
     *
     * @return An array with assetic attributes ready to append to $formulae.
     */
    protected function buildFormula($blockName, array $params = array())
    {
        // inject the block name into the $params array
        $params[AsseticExtension::OPTION_SMARTY_BLOCK_NAME] = $blockName;
        list($inputs, $filters, $options) = $this->extension->buildAttributes($params);

        $asset = $this->factory->createAsset($inputs, $filters, $options);
        $options['output'] = $asset->getTargetPath();
        unset($options[AsseticExtension::OPTION_SMARTY_BLOCK_NAME]);

        return array($options['name'] => array($inputs, $filters, $options));
    }
}
