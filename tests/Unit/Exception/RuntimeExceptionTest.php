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

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Exception;

use Exception;
use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
use Smarty;
use Smarty_Internal_Template;

class RuntimeExceptionTest extends TestCase
{
    public function testCreateFromPrevious()
    {
        $message = 'Original exception';
        $template = 'index.html.smarty';

        $originalException = new Exception($message);

        $exception = RuntimeException::createFromPrevious($originalException, $template);
        self::assertSame($template, $exception->getTemplateFile());
        self::assertSame($exception->getRawMessage(), $message);
        self::assertStringContainsString($message, $exception->getMessage());
        self::assertStringContainsString($template, $exception->getMessage());
    }

    public function testTemplateFilenameAndLineNumberCanBeSetAfterInstantiation()
    {
        $message = 'Runtime exception';
        $template = 'index.html.smarty';
        $lineNumber = 666;

        $exception = new RuntimeException($message);

        self::assertStringContainsString($message, $exception->getMessage());
        self::assertStringNotContainsString($template, $exception->getMessage());

        $exception->setTemplateFile($template);
        $exception->setTemplateLine($lineNumber);
        self::assertSame($template, $exception->getTemplateFile());
        self::assertSame($lineNumber, $exception->getTemplateLine());
        self::assertSame($exception->getRawMessage(), $message);

        self::assertStringContainsString($template, $exception->getMessage());
        self::assertStringContainsString('at line '.$lineNumber, $exception->getMessage());
    }

    public function testThatAFinalStopInTheExceptionMessageIsPushedToTheEnd()
    {
        $message = 'Runtime exception.';
        $template = 'index.html.smarty';

        $exception = new RuntimeException($message);

        $exception->setTemplateFile($template);

        self::assertStringContainsString(rtrim($message, '.').' in', $exception->getMessage());
    }

    public function testWithSmartyTemplate()
    {
        $message = 'Runtime exception';
        $templateFile = 'index.html.smarty';

        $smarty = new Smarty();
        $smartyTemplate = new Smarty_Internal_Template($templateFile, $smarty);

        $originalException = new Exception($message);
        $exception = RuntimeException::createFromPrevious($originalException, $smartyTemplate);

        self::assertFalse($exception->getTemplateFile());
    }
}
