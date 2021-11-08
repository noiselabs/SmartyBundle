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

use NoiseLabs\Bundle\SmartyBundle\Extension\TranslationExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Translator;

/**
 * Test suite for the translation extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class TranslationExtensionTest extends TestCase
{
    public function testEscaping()
    {
        $template = 'trans_escaping.html.tpl';
        $this->engine->setTemplate($template, '{trans vars=["%value%" => $value]}Percent: %value%% ({$msg}){/trans}');
        $this->engine->addExtension(new TranslationExtension(new Translator('en', new MessageFormatter())));

        $this->assertEquals('Percent: 12% (approx.)', $this->engine->render($template, ['value' => 12, 'msg' => 'approx.']));
    }

    /**
     * @dataProvider getTranslationTests
     *
     * @param mixed $content
     * @param mixed $expected
     */
    public function testTrans($content, $expected, array $variables = [])
    {
        static $test = 0;
        $template = 'translation_test_'.$test++.'.html.tpl';

        $this->engine->setTemplate($template, $content);
        $this->engine->addExtension(new TranslationExtension(new Translator('en', new MessageFormatter())));

        $this->assertEquals($expected, $this->engine->render($template, $variables));
    }

    /**
     * Returns translation tests (data provider).
     */
    public function getTranslationTests()
    {
        return [
            // trans block
            ['{trans}Hello{/trans}', 'Hello'],
            ['{trans}{$name}{/trans}', 'Symfony2', ['name' => 'Symfony2']],

            ['{trans domain="elsewhere"}Hello{/trans}', 'Hello'],

            ['{trans}Hello {$name}{/trans}', 'Hello Symfony2', ['name' => 'Symfony2']],
            ['{trans vars=["%name%" => "Symfony2"]}Hello %name%{/trans}', 'Hello Symfony2'],
            ['{$vars=["%name%" => "Symfony2"]}{trans vars=$vars}Hello %name%{/trans}', 'Hello Symfony2'],

            ['{trans locale="pt"}Hello{/trans}', 'Hello'],

            // transchoice block
            ['{transchoice count=$count domain="messages"}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples{/transchoice}',
                'There is no apples', ['count' => 0], ],
            ['{transchoice count=$count}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples{/transchoice}',
                'There is 5 apples', ['count' => 5], ],
            ['{transchoice count=$count vars=["%name%" => $name]}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples (%name%){/transchoice}',
                'There is 5 apples (Symfony2)', ['count' => 5, 'name' => 'Symfony2'], ],
            ['{transchoice count=$count vars=["%name%" => "Symfony2"]}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples (%name%){/transchoice}',
                'There is 5 apples (Symfony2)', ['count' => 5], ],
            ['{transchoice count=$count locale="fr"}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples{/transchoice}',
                'There is no apples', ['count' => 0], ],

            // trans modifier
            ['{"Hello"|trans}', 'Hello'],
            ['{$name|trans}', 'Symfony2', ['name' => 'Symfony2']],
            ['{$hello|trans:$vars}', 'Hello Symfony2', ['hello' => 'Hello %name%', 'vars' => ['%name%' => 'Symfony2']]],
            ['{"Hello"|trans:array():"messages":"pt"}', 'Hello'],

            // transchoice modifier
            ['{"[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples"|transchoice:$count}', 'There is 5 apples', ['count' => 5]],
            ['{$text|transchoice:5:["%name%" => "Symfony2"]}', 'There is 5 apples (Symfony2)', ['text' => '[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples (%name%)']],
            ['{"[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples"|transchoice:$count:[]:"messages":"pt"}', 'There is 5 apples', ['count' => 5]],
        ];
    }
}
