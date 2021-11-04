.. _ch_usage:
    
*****
Usage
*****

Basic usage
===========

You can render a Smarty template instead of a Twig one simply by using the
``.smarty`` extension in the template name instead of ``.twig``. The controller
below renders the :file:`index.html.smarty` template:

.. code-block:: php

    // src/AppBundle/Controller/DefaultController.php

    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.smarty');
    }

Template Inheritance
====================

Like Symfony2 PHP renderer or Twig, Smarty provides template inheritance.

.. note::

    Template inheritance is an approach to managing templates that resembles object-oriented programming techniques. Instead of the traditional use of ``{include ...}`` tags to manage parts of templates, you can inherit the contents of one template to another (like extending a class) and change blocks of content therein (like overriding methods of a class.) This keeps template management minimal and efficient, since each template only contains the differences from the template it extends.

Let's assume you have a :file:`app/Resources/views/base.html.smarty` as layout

.. code-block:: html+smarty

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8" />
        <title>{block name=title}Welcome!{/block}</title>
        {block name=stylesheets}{/block}
    </head>
    <body>
    {block name=body}{/block}
    {block name=javascripts}{/block}
    </body>
    </html>

and :file:`app/Resources/views/default/index.html.smarty` for the content
    
.. code-block:: html+smarty

    {extends 'file:base.html.smarty'}
    {block name=title}Welcome to the SmartyBundle{/block}
    {block name=body}Welcome to the SmartyBundle{/block}

Then the output of :file:`index.html.smarty` will be:
    
.. code-block:: html+smarty    

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8" />
        <title>Welcome to the SmartyBundle</title>
    </head>
    <body>
    Welcome to the SmartyBundle
    </body>
    </html>

Referencing Templates
=====================

There are several possibilites to reference templates:

#.  ``file:base.html.smarty``: To load a template that lives in the ``app/Resources/views`` directory of the project you should use the following syntax:
    
    .. code-block:: html+smarty

        {extends 'file:base.html.smarty'}

#.  ``file:AppBundle::index.html.smarty``: This syntax is the same as with twig.

    .. code-block:: html+smarty

        {extends 'file:AppBundle::base.html.smarty'}

#.  ``file:[AppBundle]/base.html.smarty``: Instead of the colon (``:``) separated syntax you can use smarty native syntax which should be, performance wise, slightly better/faster. But it works only within templates (not in the Controller), so for consistency's sake you might want to prefer the colon separated syntax one.

    .. code-block:: html+smarty

        {extends 'file:[AppBundle]/base.html.smarty'}

Please see `Symfony2 - Template Naming and Locations
<http://symfony.com/doc/3.0/book/templating.html#template-naming-locations>`_
to learn more about the naming scheme and template locations supported in
Symfony2.

``{include}`` functions work the same way as the examples above.:
    
.. code-block:: html+smarty    

    {include 'file:AppBundle::base.html.smarty'}
    {include 'file:[AppBundle]/base.html.smarty'}
    {include 'file:base.html.smarty'}

.. warning::
    
    Note the usage of the ``file:`` resource in the ``{extends}`` function. We need to declare the resource even if the Smarty class variable ``$default_resource_type`` is set to ``'file'``. This is required because we need to trigger a function to handle 'logical' file names (only mandatory if you are using the first syntax). Learn more about resources in the `Smarty Resources <http://www.smarty.net/docs/en/resources.tpl>`_ webpage.

.. note::
    
    The ``.html.smarty`` extension can simply be replaced by ``.smarty``. We are prefixing with ``.html`` to stick with the Symfony convention of defining the format (``.html``) and engine (``.smarty``) for each template.
