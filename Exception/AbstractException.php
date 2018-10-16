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

namespace NoiseLabs\Bundle\SmartyBundle\Exception;

use Exception;
use Smarty_Internal_Template;
use Smarty_Template_Source;

/**
 * SmartyBundle base exception.
 *
 * @note This class was heavily inspired in Twig_Error class. Credits goes to
 * Fabien Potencier (<fabien@symfony.com>) and the Twig community.
 *
 * @author  Vítor Brandão <vitor@noiselabs.com>
 */
class AbstractException extends Exception
{
    protected $lineno;
    protected $filename;
    protected $rawMessage;
    protected $previous;
    protected $template;

    /**
     * Constructor.
     *
     * @param string                   $message  The error message
     * @param integer                  $lineno   The compiled template line where the error occurred
     * @param string                   $filename The compiled template file name where the error
     * @param Smarty_Internal_Template $template Smarty template
     * @param Exception                $previous The previous exception
     */
    public function __construct(
        $message,
        $lineno = -1,
        $filename = null,
        Smarty_Internal_Template $template = null,
        Exception $previous = null
    ) {
        parent::__construct('', 0, $previous);

        $this->lineno = $lineno;
        $this->filename = $filename;
        $this->template = $template;

        if (-1 === $this->lineno || null === $this->filename) {
            $this->guessTemplateInfo();
        }

        $this->rawMessage = str_replace(
            array('&quot;', '&gt;', '&lt;'),
            array('"', '>', '<'),
            html_entity_decode($message)
        );

        $this->updateRepr();
    }

    /**
     * @param Exception $previous The previous exception
     * @param string    $resource An optional template resource (type and path)
     *
     * @return static A SmartyBundle Exception
     */
    public static function createFromPrevious(Exception $previous, $resource = null)
    {
        $filename = null != $resource ? $resource : null;

        // An exception has been thrown during the rendering of a template
        return new static($previous->getMessage(), -1, $resource, null, $previous);
    }

    /**
     * Gets the raw message.
     *
     * @return string The raw message
     */
    public function getRawMessage()
    {
        return $this->rawMessage;
    }

    /**
     * Gets the filename where the error occurred.
     *
     * @return string The filename
     */
    public function getTemplateFile()
    {
        return $this->filename;
    }

    /**
     * Sets the filename where the error occurred.
     *
     * @param string $filename The filename
     */
    public function setTemplateFile($filename)
    {
        $this->filename = $filename;

        $this->updateRepr();
    }

    /**
     * Gets the template line where the error occurred.
     *
     * @return integer The template line
     */
    public function getTemplateLine()
    {
        return $this->lineno;
    }

    /**
     * Sets the template line where the error occurred.
     *
     * @param integer $lineno The template line
     */
    public function setTemplateLine($lineno)
    {
        $this->lineno = $lineno;

        $this->updateRepr();
    }

    protected function updateRepr()
    {
        $this->message = $this->rawMessage;

        $dot = false;
        if ('.' === substr($this->message, -1)) {
            $this->message = substr($this->message, 0, -1);
            $dot = true;
        }

        if (null !== $this->filename) {
            if ($this->filename instanceof Smarty_Internal_Template) {
                $this->filename = ($this->filename->source instanceof Smarty_Template_Source) ?
                    $this->filename->source->filepath : $this->filename->template_resource;
            }
            $this->message .= sprintf(' in %s', is_string($this->filename) ? '"'.$this->filename.'"' : json_encode($this->filename));
        }

        if ($this->lineno >= 0) {
            $this->message .= sprintf(' at line %d', $this->lineno);
        }

        if ($dot) {
            $this->message .= '.';
        }
    }

    protected function guessTemplateInfo()
    {
        $template = null;

        foreach (debug_backtrace() as $trace) {
            if (!isset($trace['args'][2]) || !$trace['args'][2] instanceof Smarty_Internal_Template) {
                continue;
            }

            $template = $trace['args'][2];
            if (isset($trace['file']) &&
                $template->compiled instanceof \Smarty_Template_Compiled &&
                $template->compiled->filepath == $trace['file']) {
                break;
            }
        }

        // update template filename
        if (null !== $template && null === $this->filename) {
            $this->filename = ($template->source instanceof Smarty_Template_Source) ?
                $template->source->filepath : $template->template_resource;
        }
    }
}
