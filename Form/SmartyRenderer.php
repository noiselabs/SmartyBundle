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
 * @author      Vítor Brandão <vitor@noiselabs.org>
 * @copyright   (C) 2011-2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        http://www.noiselabs.org
 * @since       0.2.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Form;

use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;

/**
 * Renders a Symfony2 form in a Smarty template.
 *
 * @author Vítor Brandão <vitor@noiselabs.org>
 */

class SmartyRenderer extends FormRenderer implements SmartyRendererInterface
{
    /**
     * @var SmartyRendererEngineInterface
     */
    private $engine;

    /**
     * @param \NoiseLabs\Bundle\SmartyBundle\Form\SmartyRendererEngineInterface $engine
     * @param \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface|null $csrfProvider
     */
    public function __construct(SmartyRendererEngineInterface $engine, CsrfProviderInterface $csrfProvider = null)
    {
        parent::__construct($engine, $csrfProvider);

        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     *
     * @see
     */
    public function searchAndRenderBlock2(FormView $view, $blockNameSuffix, array $variables = array())
    {
        var_dump($blockNameSuffix);
        die('not found');
    }

}