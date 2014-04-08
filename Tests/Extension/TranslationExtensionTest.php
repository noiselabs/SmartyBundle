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
 * Copyright (C) 2011-2014 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\TranslationExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

/**
 * Test suite for the translation extension.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */
class TranslationExtensionTest extends TestCase
{
    public function testEscaping()
    {
        $template = 'trans_escaping.html.tpl';
        $this->engine->setTemplate($template, '{trans vars=["%value%" => $value]}Percent: %value%% ({$msg}){/trans}');
        $this->engine->addExtension(new TranslationExtension(new Translator('en', new MessageSelector())));

        $this->assertEquals('Percent: 12% (approx.)', $this->engine->render($template, array('value' => 12, 'msg' => 'approx.')));
    }

    /**
     * @dataProvider getTransTests
     */
    public function testTrans($content, $expected, array $variables = array())
    {
        static $test = 0;
        $template = 'translation_test_'.$test++.'.html.tpl';

        $this->engine->setTemplate($template, $content);
        $this->engine->addExtension(new TranslationExtension(new Translator('en', new MessageSelector())));

        $this->assertEquals($expected, $this->engine->render($template, $variables));
    }

    /**
     * Returns translation tests (data provider).
     */
    public function getTransTests()
    {
        return array(
            // trans block
            array('{trans}Hello{/trans}', 'Hello'),
            array('{trans}{$name}{/trans}', 'Symfony2', array('name' => 'Symfony2')),

            array('{trans domain="elsewhere"}Hello{/trans}', 'Hello'),

            array('{trans}Hello {$name}{/trans}', 'Hello Symfony2', array('name' => 'Symfony2')),
            array('{trans vars=["%name%" => "Symfony2"]}Hello %name%{/trans}', 'Hello Symfony2'),
            array('{$vars=["%name%" => "Symfony2"]}{trans vars=$vars}Hello %name%{/trans}', 'Hello Symfony2'),

            array('{trans locale="pt"}Hello{/trans}', 'Hello'),

            // transchoice block
            array('{transchoice count=$count domain="messages"}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples{/transchoice}',
                'There is no apples', array('count' => 0)),
            array('{transchoice count=$count}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples{/transchoice}',
                'There is 5 apples', array('count' => 5)),
            array('{transchoice count=$count vars=["%name%" => $name]}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples (%name%){/transchoice}',
                'There is 5 apples (Symfony2)', array('count' => 5, 'name' => 'Symfony2')),
            array('{transchoice count=$count vars=["%name%" => "Symfony2"]}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples (%name%){/transchoice}',
                'There is 5 apples (Symfony2)', array('count' => 5)),
            array('{transchoice count=$count locale="fr"}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples{/transchoice}',
                'There is no apples', array('count' => 0)),

            // trans modifier
            array('{"Hello"|trans}', 'Hello'),
            array('{$name|trans}', 'Symfony2', array('name' => 'Symfony2')),
            array('{$hello|trans:$vars}', 'Hello Symfony2', array('hello' => 'Hello %name%', 'vars' => array('%name%' => 'Symfony2'))),
            array('{"Hello"|trans:array():"messages":"pt"}', 'Hello'),

            // transchoice modifier
            array('{"[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples"|transchoice:$count}', 'There is 5 apples', array('count' => 5)),
            array('{$text|transchoice:5:["%name%" => "Symfony2"]}', 'There is 5 apples (Symfony2)', array('text' => '[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples (%name%)')),
            array('{"[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples"|transchoice:$count:[]:"messages":"pt"}', 'There is 5 apples', array('count' => 5)),
        );
    }
}
