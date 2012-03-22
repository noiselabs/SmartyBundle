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

namespace NoiseLabs\Bundle\SmartyBundle\Assetic;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;

use Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException;

/**
 * Loads asset formulae from Smarty templates.
 *
 * @author Vítor Brandão <noisebleed@noiselabs.com>
 */
class SmartyFormulaLoader implements FormulaLoaderInterface
{
    protected $engine;
    protected $extension;
    protected $tags = array();

    public function __construct(SmartyEngine $engine)
    {
        $this->engine = $engine;
        $this->extension = $this->engine->getExtension('assetic');

        $plugins = $engine->getPlugins('assetic');
        foreach (array_keys($plugins) as $k) {
            $this->tags[] = $plugins[$k]->getName();
        }
    }

    public function load(ResourceInterface $resource)
    {
        $smarty = $this->engine->getSmarty();
        $factory = $this->extension->getAssetFactory();

        $filename = $this->engine->load((string) $resource);
        $template = $smarty->createTemplate($filename);

        if (!is_file($compiledFilepath = $template->compiled->filepath)) {
            return array();
        }

        $content = file_get_contents($compiledFilepath);

        $tags = implode('|', $this->tags);
        preg_match_all('/\$_smarty_tpl-\>smarty-\>_tag_stack\[\] = array\([\'|"]('.$tags.')[\'|"], (.*?)\);/', $content, $matches, PREG_SET_ORDER);

        $formulae = array();

        foreach ($matches as $match) {
            if (!isset($match[1]) || !isset($match[2])) {
                continue;
            }

            $formulae = array_merge($formulae, $this->buildAssetParameters($match[1], $match[2]));
        }

        return $formulae;
    }

    /**
     * Say hello to `eval()`.
     *
     * @param string $block   Block tag name.
     * @param string $content String containing block attributes extracted
     * from the Smarty template.
     *
     * @return array An array with parsed attributes to be used as a assetic
     * formula.
     */
    protected function buildAssetParameters($block, $content)
    {
        $content = 'return '.$content.';';
        if (!is_array($params = eval($content))) {
            throw new \RuntimeException('Malformed '.$block.' block');
        }

        list($inputs, $filters, $attributes) = $this->extension->buildAttributes($params);

        return array($attributes['name'] => array(
            $inputs,
            $filters,
            $attributes
        ));
    }
}
