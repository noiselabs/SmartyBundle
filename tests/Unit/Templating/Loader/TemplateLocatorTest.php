<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Templating\Loader;

use NoiseLabs\Bundle\SmartyBundle\Templating\Loader\TemplateLocator;
use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateReference;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Config\FileLocator;

/**
 * @group legacy
 */
class TemplateLocatorTest extends TestCase
{
    public function testLocateATemplate()
    {
        $template = new TemplateReference('bundle', 'controller', 'name', 'format', 'engine');

        $fileLocator = $this->getFileLocator();

        $fileLocator
            ->expects($this->once())
            ->method('locate')
            ->with($template->getPath())
            ->willReturn('/path/to/template')
        ;

        $locator = new TemplateLocator($fileLocator);

        $this->assertEquals('/path/to/template', $locator->locate($template));

        // Assert cache is used as $fileLocator->locate should be called only once
        $this->assertEquals('/path/to/template', $locator->locate($template));
    }

    public function testLocateATemplateFromCacheDir()
    {
        $template = new TemplateReference('bundle', 'controller', 'name', 'format', 'engine');

        $fileLocator = $this->getFileLocator();

        $locator = new TemplateLocator($fileLocator, __DIR__.'/../../Fixtures');

        $this->assertEquals(realpath(__DIR__.'/../../Fixtures/Resources/views/this.is.a.template.format.engine'), $locator->locate($template));
    }

    public function testThrowsExceptionWhenTemplateNotFound()
    {
        $template = new TemplateReference('bundle', 'controller', 'name', 'format', 'engine');

        $fileLocator = $this->getFileLocator();

        $errorMessage = 'FileLocator exception message';

        $fileLocator
            ->expects($this->once())
            ->method('locate')
            ->willThrowException(new \InvalidArgumentException($errorMessage))
        ;

        $locator = new TemplateLocator($fileLocator);

        try {
            $locator->locate($template);
            $this->fail('->locate() should throw an exception when the file is not found.');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString(
                $errorMessage,
                $e->getMessage(),
                'TemplateLocator exception should propagate the FileLocator exception message'
            );
        }
    }

    public function testThrowsAnExceptionWhenTemplateIsNotATemplateReferenceInterface()
    {
        $this->expectException(\InvalidArgumentException::class);
        $locator = new TemplateLocator($this->getFileLocator());
        $locator->locate('template');
    }

    protected function getFileLocator()
    {
        return $this
            ->getMockBuilder(FileLocator::class)
            ->setMethods(['locate'])
            ->setConstructorArgs(['/path/to/fallback'])
            ->getMock()
        ;
    }
}
