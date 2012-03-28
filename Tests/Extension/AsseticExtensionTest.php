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
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Extension;

use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetInterface;
use NoiseLabs\Bundle\SmartyBundle\Extension\AsseticExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;

/**
 * Test suite for the assetic extension.
 *
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class AsseticExtensionTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Assetic\\AssetManager')) {
            $this->markTestSkipped('Assetic is not available.');
        }
    }

    public function testExtensionName()
    {
        $extension = new AsseticExtensionForTest(new AssetFactory('/foo'));

        $this->assertEquals('assetic', $extension->getName());
    }
}

class AsseticExtensionForTest extends AsseticExtension
{
    protected function getAssetUrl(AssetInterface $asset, array $options = array())
    {
        return $asset->getTargetPath();
    }
}
