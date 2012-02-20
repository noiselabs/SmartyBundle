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
 * Copyright (C) 2011-2012 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2012 Vítor Brandão <noisebleed@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 * @since       0.1.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\TranslationExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;

/**
 * Test suite for the translation extension.
 *
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class TranslationExtensionTest extends TestCase
{
    /**
     * @dataProvider getTransTests
     */
    public function testTrans($name, $content, $expected, array $variables = array())
    {        
        $this->engine->setTemplate($name, $content);
        $this->engine->addExtension(new TranslationExtension(new Translator('en', new MessageSelector())));
        
        $this->assertEquals($expected, $this->engine->render($name, $variables));
    }

    /**
     * Returns translation tests (data provider).
     */
    public function getTransTests()
    {
        return array(
            // trans block
            array('tb1.tpl', '{trans}Hello{/trans}', 'Hello'),
            array('tb2.tpl', '{trans}{$name}{/trans}', 'SmartyBundle', array('name' => 'SmartyBundle')),
            array('tb3.tpl', '{trans}Hello {$name}{/trans}', 'Hello SmartyBundle', array('name' => 'SmartyBundle')),
            array('tb4.tpl', '{trans locale="pt"}Hello{/trans}', 'Hello'),

            // transchoice block
            /*array('{% transchoice count from "messages" %}{0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples{% endtranschoice %}',
                'There is no apples', array('count' => 0)),
            array('{% transchoice count %}{0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples{% endtranschoice %}',
                'There is 5 apples', array('count' => 5)),
            array('{% transchoice count %}{0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples (%name%){% endtranschoice %}',
                'There is 5 apples (Symfony2)', array('count' => 5, 'name' => 'Symfony2')),
            array('{% transchoice count with { \'%name%\': \'Symfony2\' } %}{0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples (%name%){% endtranschoice %}',
                'There is 5 apples (Symfony2)', array('count' => 5)),
            array('{% transchoice count into "fr"%}{0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples{% endtranschoice %}',
                'There is no apples', array('count' => 0)),*/
            
            // trans modifier
            array('tm1.tpl', '{"Hello"|trans}', 'Hello'),
            array('tm2.tpl', '{"$name"|trans}', 'SmartyBundle', array('name' => 'SmartyBundle')),
            array('tm3.tpl', '{"Hello $name"|trans}', 'Hello SmartyBundle', array('name' => 'SmartyBundle')),
            array('tm4.tpl', '{"Hello"|trans:array():null:"pt"}', 'Hello'),
            
           // transchoice modifier
            /*array('{{ "{0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples"|transchoice(count) }}', 'There is 5 apples', array('count' => 5)),
            array('{{ text|transchoice(5, {\'%name%\': \'Symfony2\'}) }}', 'There is 5 apples (Symfony2)', array('text' => '{0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples (%name%)')),
            array('{{ "{0} There is no apples|{1} There is one apple|]1,Inf] There is %count% apples"|transchoice(count, {}, "messages", "fr") }}', 'There is 5 apples', array('count' => 5)),*/

        );
    }
}
