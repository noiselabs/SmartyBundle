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

use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateFilenameParser;
use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateReference;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;

/**
 * @group legacy
 */
class TemplateFilenameParserTest extends TestCase
{
    protected $parser;

    protected function setUp(): void
    {
        $this->parser = new TemplateFilenameParser();
    }

    protected function tearDown(): void
    {
        $this->parser = null;
    }

    /**
     * @dataProvider getFilenameToTemplateProvider
     *
     * @param mixed $file
     * @param mixed $ref
     */
    public function testParseFromFilename($file, $ref)
    {
        $template = $this->parser->parse($file);

        if (false === $ref) {
            $this->assertFalse($template);
        } else {
            $this->assertEquals($template->getLogicalName(), $ref->getLogicalName());
        }
    }

    public function getFilenameToTemplateProvider()
    {
        return [
            ['/path/to/section/name.format.engine', new TemplateReference('', '/path/to/section', 'name', 'format', 'engine')],
            ['\\path\\to\\section\\name.format.engine', new TemplateReference('', '/path/to/section', 'name', 'format', 'engine')],
            ['name.format.engine', new TemplateReference('', '', 'name', 'format', 'engine')],
            ['name.format', false],
            ['name', false],
        ];
    }
}
