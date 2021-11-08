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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\AssetsExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;

/**
 * Test suite for the assets extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class AssetsExtensionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $templatesDir = __DIR__.'/templates/assets';
        $this->engine->setTemplateDir($templatesDir);
    }

    public function testExtensionName()
    {
        $extension = $this->createAssetsExtension();
        $this->assertEquals('assets', $extension->getName());
    }

    public function testGetVersion()
    {
        $extension = $this->createAssetsExtension(null, [], 'foo');
        $this->engine->addExtension($extension);
        $xml = $this->renderXml('version.smarty');
        $this->assertEquals('foo', (string) $xml->asset);
    }

    public function testGetUrl()
    {
        $extension = $this->createAssetsExtension(null, 'http://assets.example.com/');
        $this->engine->addExtension($extension);
        $xml = $this->renderXml('base_url.smarty');
        $this->assertEquals('http://assets.example.com/foo.js', (string) $xml->asset[0]);
        $this->assertEquals('http://assets.example.com/foo.js', (string) $xml->asset[1]);
    }

    protected function createAssetsExtension($basePath = null, $baseUrls = [], $version = null, $format = null, $namedPackages = [])
    {
        $context = $this->createMock(ContextInterface::class);

        $versionStrategy = new EmptyVersionStrategy();
        if (null !== $version) {
            $versionStrategy = new StaticVersionStrategy($version);
        }

        if ($baseUrls) {
            $package = new UrlPackage($baseUrls, $versionStrategy, $context);
        } else {
            $package = new Package($versionStrategy, $context);
        }
        $packages = new Packages($package, $namedPackages);

        return new AssetsExtension($packages);
    }
}
