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
 * Copyright (C) 2011-2013 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2013 Vítor Brandão <noisebleed@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Compile Smarty templates.
 *
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class CompileCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('smarty:compile')
            ->setDescription('Compiles all known Smarty templates')
            ->addArgument(
                'bundle',
                InputArgument::OPTIONAL,
                'A bundle name'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the compilation of all templates even if they weren\'t modified'
            )
            ->setHelp(<<<EOF
<info>php %command.full_name%</info>

<info>php %command.full_name% @AcmeMyBundle</info>

The command finds all Smarty templates in the <comment>AcmeMyBundle</comment> bundle and compiles each Smarty template.

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = $this->getContainer()->get('smarty.templating.finder');
        $engine = $this->getContainer()->get('templating.engine.smarty');
        $smarty = $engine->getSmarty();

        $bundleName = $input->getArgument('bundle');
        $force = (boolean) $input->getOption('force');
        $verbose = $input->getOption('verbose');

        if ($bundleName && (0 === strpos($bundleName, '@'))) {
            $bundle = $finder->getBundle(trim($bundleName, '@'));
            $templates = $finder->findTemplatesInBundle($bundle);
        } else {
            $templates = $finder->findAllTemplates();
        }

        $totalCtime = 0;
        $count = array('ok' => 0, 'failed' => 0);
        foreach ($templates as $template) {
            try {
                $startTime = microtime(true);
                $tpl = $engine->compileTemplate($template, false);
                if ($tpl instanceof \Smarty_Internal_Template) {
                    $ctime = microtime(true) - $startTime;
                    $totalCtime += $ctime;
                    $source = $tpl->source;
                    $compiled = $tpl->compiled;

                    if ($verbose) {
                        $output->writeln(sprintf("Compiled <info>%s</info>\n(into \"%s\", <comment>in %f secs</comment>)", $source->resource, $compiled->filepath, $ctime));
                    }
                    $count['ok']++;
                } else {
                    throw new \RuntimeException('Unable to create a Smarty_Internal_Template instance');
                }
            } catch (\Exception $e) {
                // problem during compilation, log it and give up
                if ($verbose) {
                    $output->writeln("");
                }
                $output->writeln(sprintf("<error>! Failed to compile Smarty template \"%s\":</error>\n-> %s\n", (string) $template, $e->getMessage()));
                $count['failed']++;
            }
        }

        $output->write(sprintf("\n<comment>Summary:</comment>\n".
        "- Successfully compiled <info>%s</info> files.\n".
        "- Failed to compile <comment>%d</comment> files.\n".
        "- Total compilation time: <comment>%f secs</comment>.\n",
            $count['ok'], $count['failed'], $totalCtime));
    }
}
