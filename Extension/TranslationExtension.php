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
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Provides integration of the Translation component with Smarty[Bundle].
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class TranslationExtension extends AbstractExtension
{
    protected $translator;

    /**
     * Constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return the translator instance.
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return array(
            new BlockPlugin('trans', $this, 'transBlock'),
            new ModifierPlugin('trans', $this, 'transModifier'),
            new BlockPlugin('transchoice', $this, 'transchoiceBlock'),
            new ModifierPlugin('transchoice', $this, 'transchoiceModifier')
        );
    }

    /**
     * Block plugin for 'trans'.
     *
     * @see TranslatorInterface::trans()
     *
     * @param array  $params  Parameters to pass to the translator
     * @param string $message Message to translate
     */
    public function transBlock(array $params = array(), $message = null, \Smarty_Internal_Template $template, &$repeat)
    {
        // only output on the closing tag
        if (!$repeat && isset($message)) {
            $params = array_merge(array(
                'vars'      => array(),
                'domain'    => 'messages',
                'locale'    => null,
            ), $params);

            return $this->translator->trans($message, $params['vars'], $params['domain'], $params['locale']);
        }
    }

    /**
     * Modifier plugin for 'trans'.
     *
     * @see TranslatorInterface::trans()
     *
     * Usage in template context:
     * <code>
     * {"text to be translated"|trans}
     * </code>
     */
    public function transModifier($message, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        return $this->translator->trans($message, $parameters, $domain, $locale);
    }

    /**
     * Block plugin for 'transchoice'.
     *
     * @param string $message Message to translate
     */
    public function transchoiceBlock(array $params = array(), $message = null, \Smarty_Internal_Template $template, &$repeat)
    {
        // only output on the closing tag
        if (!$repeat && isset($message)) {
            $params = array_merge(array(
                'count'     => null,
                'vars'      => array(),
                'domain'    => 'messages',
                'locale'    => null,
            ), $params);

            // Replace [123] with {123}
            if ($template->smarty->left_delimiter == '{' || $template->smarty->right_delimiter == '}') {
                $message = preg_replace("/\[([0-9]*)\] (.*?)/i", '{$1} $2', $message);
            }

            return $this->translator->transchoice($message, $params['count'], array_merge(array('%count%' => $params['count']), $params['vars']), $params['domain'], $params['locale']);
        }
    }

    /**
     * Modifier plugin for 'transchoice'.
     */
    public function transchoiceModifier($message, $count, array $parameters = array(), $domain = 'messages', $locale = null)
    {
        return $this->translator->transChoice($message, $count, array_merge(array('%count%' => $count), $parameters), $domain, $locale);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'translator';
    }
}
