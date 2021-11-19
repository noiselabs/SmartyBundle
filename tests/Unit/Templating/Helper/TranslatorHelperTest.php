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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Templating\Helper;

use NoiseLabs\Bundle\SmartyBundle\Templating\Helper\TranslatorHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use TypeError;

class TranslatorHelperTest extends TestCase
{
    public function testPassingAnInvalidTranslatorInstanceRaisesAnException()
    {
        $this->expectException(TypeError::class);
        new TranslatorHelper(new NotATranslator());
    }

    public function testTransWithTranslator()
    {
        $message = 'Hello, World!';
        $translator = $this->getTranslator($message);
        $translatorHelper = new TranslatorHelper($translator);
        $this->assertSame($message, $translatorHelper->trans($message));
    }

    public function testTransWithoutTranslator()
    {
        $message = 'Hello, World!';
        $translatorHelper = new TranslatorHelper();
        $this->assertSame($message, $translatorHelper->trans($message));
    }

    public function testGetName()
    {
        $translatorHelper = new TranslatorHelper();
        $this->assertSame('translator', $translatorHelper->getName());
    }

    private function getTranslator(string $translation): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->method('trans')
            ->willReturn($translation)
        ;

        return $translator;
    }
}

class NotATranslator
{
}
