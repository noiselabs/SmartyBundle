<?php
/*
 * This file is part of the NoiseLabs-SmartyBundle package.
 *
 * Copyright (c) 2011-2021 Vítor Brandão <vitor@noiselabs.io>
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
 */
declare(strict_types=1);

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
     * @throws TemplateNotFoundException
     *
     * @return string
     */
    public function load($name)
    {
        $templateId = $name instanceof TemplateReferenceInterface ? $name->getLogicalName() : $name;

        if (isset($this->cache[$templateId])) {
            return $this->cache[$templateId];
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
