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

namespace NoiseLabs\Bundle\SmartyBundle\Command;

use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException as SmartyBundleRuntimeException;
use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateFinder;
use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;
use Smarty_Internal_Template;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Compile Smarty templates.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class CompileCommand extends Command
{
    /**
     * @var SmartyEngine
     */
    private $engine;

    /**
     * @var TemplateFinder
     */
    private $finder;

    protected static $defaultName = 'smarty:compile';

    public function __construct(SmartyEngine $engine, TemplateFinder $finder)
    {
        parent::__construct();

        $this->engine = $engine;
        $this->finder = $finder;
    }

    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('bundle', InputArgument::OPTIONAL, 'The bundle name or directory where to load the templates'),
            ])
            ->setDescription('Compiles all known Smarty templates')
            ->setHelp(
                <<<'EOF'
                    The following command finds all known Smarty templates and compiles them:

                    <info>php %command.full_name%</info>

                    Alternatively you may pass an optional <comment>@AcmeMyBundle</comment> argument to only search
                    for templates in a specific bundle:

                    <info>php %command.full_name% @AcmeMyBundle</info>
                    EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundleName = $input->getArgument('bundle');
        $verbose = $input->getOption('verbose');

        if ($bundleName && (0 === strpos($bundleName, '@'))) {
            $bundle = $this->finder->getBundle(trim($bundleName, '@'));
            $templates = $this->finder->findTemplatesInBundle($bundle);
        } else {
            $templates = $this->finder->findAllTemplates();
        }

        $totalCtime = 0;
        $count = ['ok' => 0, 'failed' => 0];
        foreach ($templates as $template) {
            try {
                $startTime = microtime(true);
                $tpl = $this->engine->compileTemplate($template, false);
                if ($tpl instanceof Smarty_Internal_Template) {
                    $ctime = microtime(true) - $startTime;
                    $totalCtime += $ctime;
                    $source = $tpl->source;
                    $compiled = $tpl->compiled;

                    if ($verbose) {
                        $output->writeln(sprintf("Compiled <info>%s</info>\n(into \"%s\") <comment>in %f secs</comment>", $source->resource, $compiled->filepath, $ctime));
                    }
                    ++$count['ok'];
                } else {
                    throw new \RuntimeException('Unable to create a Smarty_Internal_Template instance');
                }
            } catch (\Exception $e) {
                $e = SmartyBundleRuntimeException::createFromPrevious($e, $template);
                $output->writeln(sprintf("<error>ERROR: Failed to compile Smarty template \"%s\"</error>\n-> %s\n", (string) $template, $e->getMessage()));
                ++$count['failed'];
            }
        }

        $output->write("\n<comment>Summary:</comment>\n");
        if ($count['ok'] > 0) {
            $output->write(sprintf("- Successfully compiled <info>%s</info> files.\n", $count['ok']));
        }
        if ($count['failed'] > 0) {
            $output->write(sprintf("- Failed to compile <error>%d</error> files.\n", $count['failed']));
        }
        $output->write(sprintf("- Total compilation time: <comment>%f secs</comment>.\n", $totalCtime));

        return $count['failed'] > 0 ? -1 : 0;
    }
}
