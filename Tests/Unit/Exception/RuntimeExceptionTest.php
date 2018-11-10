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
 * Copyright (C) 2011-2018 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2018 Vítor Brandão <vitor@noiselabs.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Exception;

use Exception;
use NoiseLabs\Bundle\SmartyBundle\Exception\RuntimeException;
use PHPUnit_Framework_TestCase as TestCase;
use Smarty;
use Smarty_Internal_Template;

class RuntimeExceptionTest extends TestCase
{
    public function test_create_from_previous()
    {
        $message = 'Original exception';
        $template = 'index.html.smarty';

        $originalException = new Exception($message);

        $exception = RuntimeException::createFromPrevious($originalException, $template);
        self::assertSame($template, $exception->getTemplateFile());
        self::assertSame($exception->getRawMessage(), $message);
        self::assertContains($message, $exception->getMessage());
        self::assertContains($template, $exception->getMessage());
    }

    public function test_template_filename_and_line_number_can_be_set_after_instantiation()
    {
        $message = 'Runtime exception';
        $template = 'index.html.smarty';
        $lineNumber = 666;

        $exception = new RuntimeException($message);

        self::assertContains($message, $exception->getMessage());
        self::assertNotContains($template, $exception->getMessage());

        $exception->setTemplateFile($template);
        $exception->setTemplateLine($lineNumber);
        self::assertSame($template, $exception->getTemplateFile());
        self::assertSame($lineNumber, $exception->getTemplateLine());
        self::assertSame($exception->getRawMessage(), $message);

        self::assertContains($template, $exception->getMessage());
        self::assertContains('at line ' . $lineNumber, $exception->getMessage());
    }

    public function test_that_a_final_stop_in_the_exception_message_is_pushed_to_the_end()
    {
        $message = 'Runtime exception.';
        $template = 'index.html.smarty';

        $exception = new RuntimeException($message);

        $exception->setTemplateFile($template);

        self::assertContains(rtrim($message, '.') . ' in', $exception->getMessage());
    }

    public function test_with_smarty_template()
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
