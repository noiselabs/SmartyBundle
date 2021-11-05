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

use NoiseLabs\Bundle\SmartyBundle\Command\TemplatesCommand;
use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateNameParser;

class TemplatesCommandTest extends TestCase
{
    /**
     * @var string
     */
    private $templatesDir;

    protected function setUp(): void
    {
        $this->templatesDir = __DIR__.'/Fixtures/templates';
    }

    public function testItDisplaysAllSmartTemplates()
    {
        $tester = $this->createCommandTester();
        $tester->execute([]);
        $this->assertStringContainsString('Found 2 Smarty templates', $tester->getDisplay());
    }

    public function testItDisplaysNoTemplatesFound()
    {
        $tester = $this->createCommandTester(false);
        $tester->execute([]);
        $this->assertStringContainsString('No Smarty templates were found', $tester->getDisplay());
    }

    public function testItDisplaysAllSmartTemplatesInABundle()
    {
        $tester = $this->createCommandTester();
        $tester->execute(['bundle' => '@TestBundle']);
        $this->assertStringContainsString('Found 1 Smarty templates', $tester->getDisplay());
    }

    private function createCommandTester(bool $shouldReturnTemplates = true): CommandTester
    {
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

        $templatesCommand = new TemplatesCommand($templateFinder);

        $application = new Application($kernel);
        $application->add($templatesCommand);

        return new CommandTester($application->find('smarty:templates'));
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
