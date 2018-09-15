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
 * @author      Vítor Brandão <vitor@noiselabs.io>
 * @copyright   (C) 2011-2018 Vítor Brandão <vitor@noiselabs.io>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 * @link        https://www.noiselabs.io
 * @since       0.2.0
 */

namespace NoiseLabs\Bundle\SmartyBundle\Form;

use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Renders a Symfony2 form in a Smarty template.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 */

class SmartyRenderer extends FormRenderer implements SmartyRendererInterface
{
    /**
     * @var SmartyRendererEngineInterface
     */
    private $engine;

    /**
     * @param SmartyRendererEngineInterface $engine
     * @param CsrfTokenManagerInterface $csrfProvider
     */
    public function __construct(SmartyRendererEngineInterface $engine, CsrfTokenManagerInterface $csrfProvider = null)
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
