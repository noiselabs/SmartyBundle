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

use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateFilenameParser;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * Finds all the templates.
 *
 * @author Victor Berchet <victor@suumit.com>
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class TemplateFinder implements TemplateFinderInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var TemplateFilenameParser
     */
    private $parser;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var array
     */
    private $templateDirs;

    /**
     * Constructor.
     *
     * @param KernelInterface        $kernel  A KernelInterface instance
     * @param TemplateFilenameParser $parser  A TemplateNameParserInterface instance
     * @param string                 $rootDir The directory where global templates can be stored
     */
    public function __construct(
        KernelInterface $kernel,
        TemplateFilenameParser $parser,
        string $rootDir,
        array $smartyOptions
    ) {
        $this->kernel = $kernel;
        $this->parser = $parser;

        $this->templateDirs = [
            $rootDir.'/Resources/views',
            $rootDir.'/templates',
        ];

        if (isset($smartyOptions['template_dir'])) {
            $this->templateDirs[] = $smartyOptions['template_dir'];
        }
        if (isset($smartyOptions['templates_dir']) && is_array($smartyOptions['templates_dir'])) {
            $this->templateDirs = array_merge($this->templateDirs, $smartyOptions['templates_dir']);
        }
        $this->templateDirs = array_unique($this->templateDirs);
    }

    /**
     * Find all the templates in the bundle and in the kernel Resources folder.
     *
     * @return TemplateReferenceInterface[] An array of templates of type TemplateReferenceInterface
     */
    public function findAllTemplates()
    {
        $templates = [];

        foreach ($this->templateDirs as $templateDir) {
            $templates += $this->findTemplatesInFolder($templateDir);
        }

        foreach ($this->kernel->getBundles() as $name => $bundle) {
            $templates += $this->findTemplatesInBundle($bundle);
        }

        return $templates;
    }

    public function getBundle(string $name): BundleInterface
    {
        return $this->kernel->getBundle($name);
    }

    /**
     * Find templates in the given bundle.
     *
     * @param BundleInterface $bundle The bundle where to look for templates
     *
     * @return array An array of templates of type TemplateReferenceInterface
     */
    public function findTemplatesInBundle(BundleInterface $bundle): array
    {
        $bundleTemplatesDir = is_dir($bundle->getPath().'/Resources/views') ?
            $bundle->getPath().'/Resources/views' : $bundle->getPath().'/templates';
        $templates = $this->findTemplatesInFolder($bundleTemplatesDir);
        $name = $bundle->getName();

        foreach (array_keys($templates) as $k) {
            $templates[$k]->set('bundle', $name);
        }

        return $templates;
    }

    /**
     * Find Smarty templates in the given directory.
     *
     * @param string $dir The folder where to look for templates
     *
     * @return array|TemplateReferenceInterface[] An array of templates of type TemplateReferenceInterface
     */
    private function findTemplatesInFolder(string $dir): array
    {
        if (!$dir || !is_dir($dir)) {
            return [];
        }

        $templates = [];
        $finder = new Finder();
        foreach ($finder->files()->followLinks()->in($dir)->name('/\.(smarty|tpl)$/') as $file) {
            /** @var SplFileInfo $file */
            $template = $this->parser->parse($file->getRelativePathname());
            if (!$template) {
                continue;
            }

            $templates[$file->getPathname()] = $template;
        }

        return $templates;
    }
}
