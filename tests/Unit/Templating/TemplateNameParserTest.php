<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Templating;

use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateNameParser;
use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateReference;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateReference as BaseTemplateReference;

/**
 * @group legacy
 */
class TemplateNameParserTest extends TestCase
{
    protected $parser;

    protected function setUp(): void
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel
            ->expects($this->any())
            ->method('getBundle')
            ->willReturnCallback(function ($bundle) {
                if (\in_array($bundle, ['SensioFooBundle', 'SensioCmsFooBundle', 'FooBundle'])) {
                    return true;
                }

                throw new \InvalidArgumentException();
            })
        ;
        $this->parser = new TemplateNameParser($kernel);
    }

    protected function tearDown(): void
    {
        $this->parser = null;
    }

    /**
     * @dataProvider parseProvider
     *
     * @param mixed $name
     * @param mixed $logicalName
     * @param mixed $path
     * @param mixed $ref
     */
    public function testParse($name, $logicalName, $path, $ref)
    {
        $template = $this->parser->parse($name);

        $this->assertSame($ref->getLogicalName(), $template->getLogicalName());
        $this->assertSame($logicalName, $template->getLogicalName());
        $this->assertSame($path, $template->getPath());
    }

    public function parseProvider()
    {
        return [
            ['FooBundle:Post:index.html.php', 'FooBundle:Post:index.html.php', '@FooBundle/Resources/views/Post/index.html.php', new TemplateReference('FooBundle', 'Post', 'index', 'html', 'php')],
            ['FooBundle:Post:index.html.twig', 'FooBundle:Post:index.html.twig', '@FooBundle/Resources/views/Post/index.html.twig', new TemplateReference('FooBundle', 'Post', 'index', 'html', 'twig')],
            ['FooBundle:Post:index.xml.php', 'FooBundle:Post:index.xml.php', '@FooBundle/Resources/views/Post/index.xml.php', new TemplateReference('FooBundle', 'Post', 'index', 'xml', 'php')],
            ['SensioFooBundle:Post:index.html.php', 'SensioFooBundle:Post:index.html.php', '@SensioFooBundle/Resources/views/Post/index.html.php', new TemplateReference('SensioFooBundle', 'Post', 'index', 'html', 'php')],
            ['SensioCmsFooBundle:Post:index.html.php', 'SensioCmsFooBundle:Post:index.html.php', '@SensioCmsFooBundle/Resources/views/Post/index.html.php', new TemplateReference('SensioCmsFooBundle', 'Post', 'index', 'html', 'php')],
            [':Post:index.html.php', ':Post:index.html.php', 'views/Post/index.html.php', new TemplateReference('', 'Post', 'index', 'html', 'php')],
            ['::index.html.php', '::index.html.php', 'views/index.html.php', new TemplateReference('', '', 'index', 'html', 'php')],
            ['index.html.php', '::index.html.php', 'views/index.html.php', new TemplateReference('', '', 'index', 'html', 'php')],
            ['FooBundle:Post:foo.bar.index.html.php', 'FooBundle:Post:foo.bar.index.html.php', '@FooBundle/Resources/views/Post/foo.bar.index.html.php', new TemplateReference('FooBundle', 'Post', 'foo.bar.index', 'html', 'php')],
            ['@FooBundle/Resources/views/layout.html.twig', '@FooBundle/Resources/views/layout.html.twig', '@FooBundle/Resources/views/layout.html.twig', new BaseTemplateReference('@FooBundle/Resources/views/layout.html.twig', 'twig')],
            ['@FooBundle/Foo/layout.html.twig', '@FooBundle/Foo/layout.html.twig', '@FooBundle/Foo/layout.html.twig', new BaseTemplateReference('@FooBundle/Foo/layout.html.twig', 'twig')],
            ['name.twig', 'name.twig', 'name.twig', new BaseTemplateReference('name.twig', 'twig')],
            ['name', 'name', 'name', new BaseTemplateReference('name')],
            ['default/index.html.php', '::default/index.html.php', 'views/default/index.html.php', new TemplateReference(null, null, 'default/index', 'html', 'php')],
        ];
    }

    public function testParseValidNameWithNotFoundBundle()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parser->parse('BarBundle:Post:index.html.php');
    }
}
