.. _ch_usage:
    
*****
Usage
*****

Basic usage
===========

You can render a Smarty template instead of a Twig one simply by using the **.smarty** extension in the template name instead of .twig. The controller below renders the index.html.smarty template:

.. code-block:: php

    // src/Acme/HelloBundle/Controller/HelloController.php

    public function indexAction($name)
    {
        return $this->render('AcmeHelloBundle:Hello:index.html.smarty', array('name' => $name));
    }

Template Inheritance
====================

Like Symfony2 PHP renderer or Twig, Smarty provides template inheritance.

    Template inheritance is an approach to managing templates that resembles object-oriented programming techniques. Instead of the traditional use of ``{include ...}`` tags to manage parts of templates, you can inherit the contents of one template to another (like extending a class) and change blocks of content therein (like overriding methods of a class.) This keeps template management minimal and efficient, since each template only contains the differences from the template it extends.

**Example:**

`layout.html.smarty`:

.. code-block:: html+smarty

    <html>
    <head>
        <title>{block name=title}Default Page Title{/block}</title>
    </head>
    <body>
        {block name=body}{/block}
    </body>
    </html>

`mypage.html.smarty`:
    
.. code-block:: html+smarty    

    {extends 'file:AcmeHelloBundle:Default:layout.html.smarty'}
    {block name=title}My Page Title{/block}
    {block name=body}My HTML Page Body goes here{/block}

Output of mypage.html.smarty:
    
.. code-block:: html+smarty    

    <html>
    <head>
        <title>My Page Title</title>
    </head>
    <body>
        My HTML Page Body goes here
    </body>
    </html>

Instead of using the ``file:AcmeHelloBundle:Default:layout.html.smarty`` syntax you may use ``file:[WebkitBundle]/Default/layout.html.smarty`` which should be, performance wise, slightly better/faster (since this is a native Smarty syntax).:

.. code-block:: html+smarty

    {extends 'file:[WebkitBundle]/Default/layout.html.smarty'}

To load a template that lives in the ``app/Resources/views`` directory of the project you should use the following syntax:
    
.. code-block:: html+smarty    

    {extends 'file:base.html.smarty'}

Please see `Symfony2 - Template Naming and Locations <http://symfony.com/doc/2.0/book/templating.html#template-naming-locations>`_ to learn more about the naming scheme and template locations supported in Symfony2.

**{include} functions** work the same way as the examples above.:
    
.. code-block:: html+smarty    

    {include 'file:WebkitBundle:Default:layout.html.smarty'}
    {include 'file:[WebkitBundle]/Default/layout.html.smarty'}
    {include 'file:base.html.smarty'}

.. warning::
    
    Note the usage of the ``file:`` resource in the ``{extends}`` function. We need to declare the resource even if the Smarty class variable ``$default_resource_type`` is set to ``'file'``. This is required because we need to trigger a function to handle 'logical' file names (only mandatory if you are using the first syntax). Learn more about resources in the `Smarty Resources <http://www.smarty.net/docs/en/resources.smarty>`_ webpage.

.. note::
    
    The `.html.smarty` extension can simply be replaced by `.smarty`. We are prefixing with `.html` to stick with the Symfony convention of defining the format (`.html`) and engine (`.smarty`) for each template.
   