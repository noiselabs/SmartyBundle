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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Command;

use NoiseLabs\Bundle\SmartyBundle\Command\CompileCommand;
use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateFinder;
use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateNameParser;

class CompileCommandTest extends TestCase
{
    /**
     * @var string
     */
    private $templatesDir;

    protected function setUp(): void
    {
        $this->templatesDir = __DIR__.'/Fixtures/templates';
    }

    public function testItCompilesAllSmartTemplates()
    {
        $tester = $this->createCommandTester();
        $tester->execute([]);
        $this->assertStringContainsString(' Successfully compiled 2 files', $tester->getDisplay());
    }

    public function testItDisplaysNoTemplatesFound()
    {
        $tester = $this->createCommandTester(false);
        $tester->execute([]);
        $this->assertStringContainsString('Total compilation time', $tester->getDisplay());
    }

    public function testItCompilesAllSmartTemplatesInABundle()
    {
        $tester = $this->createCommandTester();
        $tester->execute(['bundle' => '@TestBundle']);
        $this->assertStringContainsString(' Successfully compiled 1 files', $tester->getDisplay());
    }

    private function createCommandTester(bool $shouldReturnTemplates = true): CommandTester
    {
        $smartyEngine = $this->createMock(SmartyEngine::class);

        $templateFinder = $this->createMock(TemplateFinder::class);
        $templateFinder
            ->expects($this->any())
            ->method('findAllTemplates')
            ->willReturn($shouldReturnTemplates ? [
                (new TemplateNameParser())->parse('index.smarty.hml'),
                (new TemplateNameParser())->parse('hello.smarty.hml'),
            ] : [])
        ;
        $templateFinder
            ->expects($this->any())
            ->method('findTemplatesInBundle')
            ->willReturn($shouldReturnTemplates ? [
                (new TemplateNameParser())->parse('bundle.smarty.hml'),
            ] : [])
        ;

        $returnValues = [
            ['TestBundle', $this->getBundle('TestBundle')],
        ];
        $kernel = $this->createMock(KernelInterface::class);
        $kernel
            ->expects($this->any())
            ->method('getBundle')
            ->willReturnMap($returnValues)
        ;
        $kernel
            ->expects($this->any())
            ->method('getBundles')
            ->willReturn([])
        ;

        $container = new Container();
        $container->setParameter('kernel.root_dir', $this->templatesDir);

        $kernel
            ->expects($this->any())
            ->method('getContainer')
            ->willReturn($container)
        ;

        $compileCommand = new CompileCommand($smartyEngine, $templateFinder);

        $application = new Application($kernel);
        $application->add($compileCommand);

        return new CommandTester($application->find('smarty:compile'));
    }

    private function getBundle(string $path)
    {
        $bundle = $this->createMock(BundleInterface::class);
        $bundle
            ->expects($this->any())
            ->method('getPath')
            ->willReturn($path)
        ;

        return $bundle;
    }
}
