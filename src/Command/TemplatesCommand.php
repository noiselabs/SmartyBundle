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

use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * List all available Smarty templates.
 *
 * @since 3.0.0
 */
class TemplatesCommand extends Command
{
    /**
     * @var TemplateFinder
     */
    private $finder;

    protected static $defaultName = 'smarty:templates';

    public function __construct(TemplateFinder $finder)
    {
        parent::__construct();

        $this->finder = $finder;
    }

    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('bundle', InputArgument::OPTIONAL, 'The bundle name where to load the templates'),
            ])
            ->setDescription('Lists all Smarty templates')
            ->setHelp(
                <<<'EOF'
                    The <info>%command.name%</info> command lists all Smarty templates found in the current project

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
        $io = new SymfonyStyle($input, $output);

        $bundleName = $input->getArgument('bundle');

        if ($bundleName && (0 === strpos($bundleName, '@'))) {
            $bundle = $this->finder->getBundle(trim($bundleName, '@'));
            $templates = $this->finder->findTemplatesInBundle($bundle);
        } else {
            $templates = $this->finder->findAllTemplates();
        }

        if (0 === count($templates)) {
            $io->writeln('No Smarty templates were found.');

            return 0;
        }

        $io->writeln(sprintf('Found <info>%s</info> Smarty templates:', count($templates)));
        foreach ($templates as $path => $template) {
            $io->writeln(sprintf('* <comment>%s</comment>', $path));
        }

        return 0;
    }
}
