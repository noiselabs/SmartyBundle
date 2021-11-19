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

use NoiseLabs\Bundle\SmartyBundle\Templating\TemplateReference;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;

/**
 * @group legacy
 */
class TemplateTest extends TestCase
{
    /**
     * @dataProvider getTemplateToPathProvider
     *
     * @param mixed $template
     * @param mixed $path
     */
    public function testGetPathForTemplate($template, $path)
    {
        $this->assertSame($template->getPath(), $path);
    }

    public function getTemplateToPathProvider()
    {
        return [
            [new TemplateReference('FooBundle', 'Post', 'index', 'html', 'php'), '@FooBundle/Resources/views/Post/index.html.php'],
            [new TemplateReference('FooBundle', '', 'index', 'html', 'twig'), '@FooBundle/Resources/views/index.html.twig'],
            [new TemplateReference('', 'Post', 'index', 'html', 'php'), 'views/Post/index.html.php'],
            [new TemplateReference('', '', 'index', 'html', 'php'), 'views/index.html.php'],
        ];
    }
}
