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

namespace NoiseLabs\Bundle\SmartyBundle\Loader;

use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class TemplateLoader
{
    /**
     * @var TemplateNameParserInterface
     */
    private $defaultTemplateParser;

    /**
     * @var TemplateNameParser
     */
    private $fallbackTemplateParser;

    /**
     * @var LoaderInterface
     */
    private $templateLoader;

    /**
     * @var array
     */
    private $cache;

    /**
     * TemplateLoader constructor.
     *
     * @param TemplateNameParserInterface $parser
     * @param LoaderInterface $loader
     */
    public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader)
    {
        $this->defaultTemplateParser = $parser;
        $this->fallbackTemplateParser = new TemplateNameParser();
        $this->templateLoader = $loader;
        $this->cache = [];
    }

    /**
     * @param string|TemplateReferenceInterface $name
     *
     * @return string
     *
     * @throws TemplateNotFoundException
     */
    public function load($name)
    {
        $templateId = $name instanceof TemplateReferenceInterface ? $name->getLogicalName() : $name;

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $template = $name instanceof TemplateReferenceInterface ? $name : $this->defaultTemplateParser->parse($name);
        if (!$template) {
            throw TemplateNotFoundException::couldNotLocate($name);
        }

        if (!$this->supports($template)) {
            throw TemplateNotFoundException::couldNotLocate($name);
        }

        $file = $this->templateLoader->load($template);
        if (!$file && 'views/' === substr(($templatePath = $template->getPath()), 0, 6)) {
            $template = $this->fallbackTemplateParser->parse($name);
            $file = $this->templateLoader->load($template);
        }

        if (!$file) {
            throw TemplateNotFoundException::couldNotLocate($name);
        }

        return $this->cache[$templateId] = (string) $file;
    }

    /**
     * @param string|TemplateReferenceInterface $template
     *
     * @return bool
     */
    public function supports($template)
    {
        if (!$template instanceof TemplateReferenceInterface) {
            $template = $this->defaultTemplateParser->parse($template);
        }

        // Keep 'tpl' for backwards compatibility.
        return in_array($template->get('engine'), ['smarty', 'tpl'], true);
    }
}