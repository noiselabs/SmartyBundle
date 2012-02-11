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
 * Copyright (C) 2011 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @author      Vítor Brandão <noisebleed@noiselabs.org>
 * @copyright   (C) 2011 Vítor Brandão <noisebleed@noiselabs.org>
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
 * @since  0.1.0
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class TranslationExtensionTest extends TestCase
{
    /**
     * @dataProvider getTransTests
     *
     *
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function testTrans($name, $content, $expected, array $variables = array())
    {        
        $this->engine->setTemplate($name, $content);
        $this->engine->addExtension(new TranslationExtension(new Translator('en', new MessageSelector())));
        
        $this->assertEquals($expected, $this->engine->render($name, $variables));
    }

    /**
     * @since  0.1.0
     * @author Vítor Brandão <noisebleed@noiselabs.org>
     */
    public function getTransTests()
    {
        return array(
            // trans block
            array('b1.tpl', '{trans}Hello{/trans}', 'Hello'),
            array('b2.tpl', '{trans}{$name}{/trans}', 'SmartyBundle', array('name' => 'SmartyBundle')),
            array('b3.tpl', '{trans}Hello {$name}{/trans}', 'Hello SmartyBundle', array('name' => 'SmartyBundle')),
            array('b4.tpl', '{trans locale="pt"}Hello{/trans}', 'Hello'),

            // trans filter
            array('f1.tpl', '{"Hello"|trans}', 'Hello'),
            array('f2.tpl', '{"$name"|trans}', 'SmartyBundle', array('name' => 'SmartyBundle')),
            array('f3.tpl', '{"Hello $name"|trans}', 'Hello SmartyBundle', array('name' => 'SmartyBundle')),
            array('f4.tpl', '{"Hello"|trans:array():null:"pt"}', 'Hello'),
        );
    }
}
