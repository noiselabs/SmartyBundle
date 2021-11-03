.. _ch_intro:

************
Introduction
************

This bundle was created to support `Smarty <http://www.smarty.net/>`_ in Symfony2, providing an alternative to the `Twig <http://twig.sensiolabs.org/>`_ template engine natively supported.

.. note::

    An effort was made to provide, where possible, the same user configuration and extensions available for the Twig bundle. This is to allow easy switching between the two bundles (at least I hope so!).

What is Smarty?
===============

Smarty is a template engine for PHP, facilitating the separation of presentation (HTML/CSS) from application logic. This implies that PHP code is application logic, and is separated from the presentation.

Some of Smarty's features: [#]_

* It is extremely fast.
* It is efficient since the PHP parser does the dirty work.
* No template parsing overhead, only compiles once.
* It is smart about recompiling only the template files that have changed.
* You can easily create your own custom functions and variable modifiers, so the template language is extremely extensible.
* Configurable template ``{delimiter}`` tag syntax, so you can use ``{$foo}, {{$foo}}, <!--{$foo}-->``, etc.
* The ``{if}..{elseif}..{else}..{/if}`` constructs are passed to the PHP parser, so the ``{if...}`` expression syntax can be as simple or as complex an evaluation as you like.
* Allows unlimited nesting of sections, if's etc.
* Built-in caching support.
* Arbitrary template sources.
* Template Inheritance for easy management of template content.
* Plugin architecture.

See the `Smarty3 Manual <http://www.smarty.net/docs/en/>`_ for other features and information on it's syntax, configuration and installation.

.. [#] http://www.smarty.net/docs/en/what.is.smarty.tpl

Requirements
============

* `PHP <http://php.net>`_ 7.1 and up
* `Symfony <http://www.symfony.com>`_ 4
* `Smarty <http://www.smarty.net>`_ 3

License
=======

This bundle is licensed under the LGPL-3 License. See the `LICENSE file <https://github.com/noiselabs/SmartyBundle/blob/master/Resources/meta/LICENSE>`_ for details.
