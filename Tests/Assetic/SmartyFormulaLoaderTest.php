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
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests\Assetic;

use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetInterface;
use NoiseLabs\Bundle\SmartyBundle\Assetic\SmartyFormulaLoader;
use NoiseLabs\Bundle\SmartyBundle\Extension\AsseticExtension;
use NoiseLabs\Bundle\SmartyBundle\Tests\TestCase;

class SmartyFormulaLoaderTest extends TestCase
{
    protected $am;
    protected $fm;

    protected function setUp()
    {
        parent::setUp();

        $this->am = $this->getMock('Assetic\\AssetManager');
        $this->fm = $this->getMock('Assetic\\FilterManager');

        $factory = new AssetFactory(__DIR__.'/templates');
        $factory->setAssetManager($this->am);
        $factory->setFilterManager($this->fm);

        $this->engine->addExtension(new AsseticExtensionForTest($factory));

        $this->loader = new SmartyFormulaLoader($this->engine);
    }

    public function testMixture()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $expected = array(
            'mixture' => array(
                array('foo', 'foo/*', '@foo'),
                array(),
                array(
                    'output'    => 'packed/mixture',
                    'name'      => 'mixture',
                    'debug'     => false,
                    'combine'   => null,
                    'vars'      => array(),
                    'var_name'  => 'asset_url',
                ),
            ),
        );

        $resource = $this->getMock('Assetic\\Factory\\Resource\\ResourceInterface');
        $resource->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(file_get_contents(__DIR__.'/templates/mixture.smarty')));
        $this->am->expects($this->any())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        $formulae = $this->loader->load($resource);
        $this->assertEquals($expected, $formulae);
    }
}

class AsseticExtensionForTest extends AsseticExtension
{
    protected function getAssetUrl(AssetInterface $asset, array $options = array())
    {
        return $asset->getTargetPath();
    }
}
