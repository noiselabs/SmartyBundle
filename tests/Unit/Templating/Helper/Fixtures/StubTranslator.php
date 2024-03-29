<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Unit\Templating\Helper\Fixtures;

use Symfony\Contracts\Translation\TranslatorInterface;

class StubTranslator implements TranslatorInterface
{
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        return '[trans]'.strtr($id, $parameters).'[/trans]';
    }
}
