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

namespace NoiseLabs\Bundle\SmartyBundle\Extension;

use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;
use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\ModifierPlugin;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper;

/**
 * SmartyBundle extension for Symfony actions helper.
 *
 * This extension tries to provide the same functionality described in
 * {@link http://symfony.com/doc/current/book/templating.html#embedding-controllers}.
 *
 * @author Vítor Brandão <vitor@noiselabs.io>
 * @author Igor Vovk (igorynia)
 */
class ActionsExtension extends AbstractExtension
{
    /**
     * @var ActionsHelper
     */
    protected $actionsHelper;

    /**
     * Constructor.
     */
    public function __construct(ActionsHelper $actionsHelper)
    {
        $this->actionsHelper = $actionsHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return [
            new BlockPlugin('render', $this, 'renderBlockAction'),
            new ModifierPlugin('render', $this, 'renderModifierAction'),
        ];
    }

    /**
     * Returns the Response content for a given controller or URI.
     *
     * @param string     $controller A controller name to execute (a string like BlogBundle:Post:index), or a relative URI
     * @param null|mixed $template
     * @param null|mixed $repeat
     *
     * @see Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper::render()
     * @see Symfony\Bundle\TwigBundle\Extension\ActionsExtension::renderAction()
     *
     * @return mixed
     */
    public function renderBlockAction(array $parameters = [], $controller = null, $template = null, &$repeat = null)
    {
        // only output on the closing tag
        if (!$repeat) {
            $parameters = array_merge([
                'attributes' => [],
                'options' => [],
            ], $parameters);

            return $this->render($controller, $parameters['attributes'], $parameters['options']);
        }
    }

    /**
     * Returns the Response content for a given controller or URI.
     *
     * @param string $controller A controller name to execute (a string like BlogBundle:Post:index), or a relative URI
     *
     * @see Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper::render()
     * @see Symfony\Bundle\TwigBundle\Extension\ActionsExtension::renderAction()
     */
    public function renderModifierAction($controller, array $attributes = [], array $options = [])
    {
        return $this->render($controller, $attributes, $options);
    }

    /**
     * @param $controller
     *
     * @return mixed
     *
     * @since Symfony-2.2
     */
    protected function render($controller, array $attributes = [], array $options = [])
    {
        $renderOptions = [];
        if (isset($options['standalone']) && true === $options['standalone']) {
            if (isset($options['strategy'])) {
                $renderOptions['strategy'] = $options['strategy'];
                unset($options['strategy']);
            } else {
                $renderOptions['strategy'] = 'esi';
            }
            unset($options['standalone']);
        }

        $isControllerReference = false !== strpos($controller, ':');

        return $this->getActionsHelper()->render(
            $isControllerReference ? $this->getActionsHelper()->controller($controller, $attributes, $options) : $controller,
            $isControllerReference ? $renderOptions : array_merge($renderOptions, $attributes)
        );
    }

    /**
     * @return ActionsHelper
     */
    protected function getActionsHelper()
    {
        return $this->actionsHelper;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     *
     * @since  0.1.0
     */
    public function getName()
    {
        return 'actions';
    }
}
