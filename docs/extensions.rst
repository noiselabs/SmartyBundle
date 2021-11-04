.. _ch_extensions:

**********
Extensions
**********

SmartyBundle extensions are packages that add new features to Smarty. The extension architecture implemented in the SmartyBundle is an object-oriented approach to the `plugin system <http://www.smarty.net/docs/en/plugins.smarty>`_ available in Smarty. The implemented architecture was inspired by `Twig Extensions <http://twig.sensiolabs.org/doc/extensions.html>`_.

Each extension object share a common interest (translation, routing, etc.) and provide methods that will be registered as a Smarty plugin before rendering a template. To learn about the plugin ecosystem in Smarty take a look at the `Smarty documentation page <http://www.smarty.net/docs/en/plugins.smarty>`_ on that subject.

The SmartyBundle comes with a few extensions to help you right away. These are described in the next section.


Actions Extension
=================

This extension tries to provide the same funcionality described in `Symfony - Templating - Embedding Controllers <http://symfony.com/doc/current/book/templating.html#embedding-controllers>`_.

Following the example presented in the link above, the Smarty equivalents are:

*Using a block function:*

.. code-block:: html+smarty

    {render attributes=['min'=>1,'max'=>3]}AcmeArticleBundle:Article:recentArticles{/render}

*Using a modifier:*

.. code-block:: html+smarty

    {'AcmeArticleBundle:Article:recentArticles'|render:['min'=>1,'max'=>3]}

Assetic Extension
=================

.. warning::

    Removed in SmartyBundle 3.0

See chapter :ref:`ch_assetic` for complete documentation about Assetic support in SmartyBundle.

Assets Extension
================

Templates commonly refer to images, JavaScript, stylesheets and other
assets. You could hard-code the path to these assets (e.g. ``/images/logo.png``), but SmartyBundle provides a more dynamic option via the ``asset`` modifier:

.. code-block:: html+smarty

    <img src="{'images/logo.png'|asset}" />

or ``asset`` block:

.. code-block:: html+smarty

    <link href="{asset}css/blog.css{/asset}" rel="stylesheet" type="text/css" />

This bundle also provides the ``assets_version`` function to return the version of the assets in a package. To set the version see the `assets_version configuration option in Symfony's Framework Bundle <http://symfony.com/doc/current/reference/configuration/framework.html#ref-framework-assets-version>`_.

Usage in template context:

.. code-block:: html+smarty

    {assets_version}

Form Extension
==============

.. warning::

    Removed in SmartyBundle 3.0

Form extension provides support for `Symfony Forms <http://symfony.com/doc/current/book/forms.html>`_ and it is described in its own chapter. :ref:`Go there now <ch_forms>`.

Routing Extension
=================

To generate URLs from a Smarty template you may use two block functions (``path`` and ``url``) provided by the `RoutingExtension <https://github.com/noiselabs/SmartyBundle/tree/master/Extension/RoutingExtension.php>`_.

``path`` block:

.. code-block:: html+smarty

    <a href="{path slug='my-blog-post'}blog_show{/path}">
        Read this blog post.
    </a>

``path`` modifier:

.. code-block:: html+smarty

    <a href="{'blog_show|path:['slug' => 'my-blog-post']}">
        Read this blog post.
    </a>

Absolute URLs can also be generated.

``url`` block:

.. code-block:: html+smarty

    <a href="{url slug='my-blog-post'}blog_show{/url}">
        Read this blog post.
    </a>

``url`` modifier:

.. code-block:: html+smarty

    <a href="{'blog_show'|url ['slug' => 'my-blog-post']}">
        Read this blog post.
    </a>

Please see the `Symfony - Routing <http://symfony.com/doc/current/book/routing.html>`_ for full information about routing features and options in Symfony.

Translation Extension
=====================

To help with message translation of static blocks of text in template context, the SmartyBundle, provides a translation extension. This extension is implemented in the class `TranslationExtension <https://github.com/noiselabs/SmartyBundle/tree/master/Extension/TranslationExtension.php>`_.

You may translate a message, in a template, using a block or modifier. Both methods support the following arguments:


count
    In pluralization context, used to determine which translation to use and also to populate the %count% placeholder *(only available in transchoice)*;

vars
    `Message placeholders <http://symfony.com/doc/current/book/translation.html#message-placeholders>`_;

domain
    Message domain, an optional way to organize messages into groups;

locale
    The locale that the translations are for (e.g. en_GB, en, etc);

``trans`` block:

.. code-block:: html+smarty

    {trans}Hello World!{/trans}

    {trans vars=['%name%' => 'World']}Hello %name%{/trans}

    {trans domain="messages" locale="pt_PT"}Hello World!{/trans}

    <!-- In case you're curious, the latter returns "Olá Mundo!" :) -->

``trans`` modifier:

.. code-block:: html+smarty

    {"Hello World!"|trans}

    {"Hello %name%"|trans:['%name%' => 'World']}

    {"Hello World!"|trans:[]:"messages":"pt_PT"}


`Message pluralization <http://symfony.com/doc/current/book/translation.html#pluralization>`_ can be achieved using ``transchoice``:

.. warning::

    Unlike the examples given in the `Symfony documentation <http://symfony.com/doc/current/book/translation.html#explicit-interval-pluralization>`_, which uses curly brackets for explicit interval pluralization we are using **square brackets** due to Smarty usage of curly brackets as syntax delimiters. So ``{0} There is no apples`` becomes ``[0] There is no apples``.

``transchoice`` block:

.. code-block:: html+smarty

    {transchoice count=$count}[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples{/transchoice}

``transchoice`` modifier:

.. code-block:: html+smarty

    {'[0] There is no apples|[1] There is one apple|]1,Inf] There is %count% apples'|transchoice:$count}
    <!-- Should write: "There is 5 apples" -->

The transchoice block/modifier automatically gets the %count% variable from the current context and passes it to the translator. This mechanism only works when you use a placeholder following the %var% pattern.


Security Extension
==================

This extension provides access control inside a Smarty template. This part of the security process is called authorization, and it means that the system is checking to see if you have privileges to perform a certain action. For full details about the `Symfony security system <http://symfony.com/doc/current/book/security.html>`_ check it's `documentation page <http://symfony.com/doc/current/book/security.html>`_.

If you want to check if the current user has a role inside a template, use the built-in ``is_granted`` modifier.

Usage:

.. code-block:: html+smarty

    {if 'IS_AUTHENTICATED_FULLY'|is_granted:$object:$field}
        <a href="...">Delete</a>
    {else}
        <!-- no delete for you -->
    {/if}

.. note::

    If you use this function and are *not* at a URL behind a firewall
    active, an exception will be thrown. Again, it's almost always a good
    idea to have a main firewall that covers all URLs.

Complex Access Controls with Expressions
----------------------------------------

.. note::

    The ``expression`` functionality was introduced in Symfony 2.4.

In addition to a role like ``ROLE_ADMIN``, the ``isGranted`` method also
accepts an `Expression <https://github.com/symfony/symfony/blob/master/src/Symfony/Component/ExpressionLanguage/Expression.php>`_ object.

You can use expressions inside your templates like this:

.. code-block:: html+smarty

    {if '"ROLE_ADMIN" in roles or (user and user.isSuperAdmin())'|expression|is_granted}
        <a href="...">Delete</a>
    {/if}

In this example, if the current user has ``ROLE_ADMIN`` or if the current
user object's ``isSuperAdmin()`` method returns ``true``, then access will
be granted (note: your User object may not have an ``isSuperAdmin`` method,
that method is invented for this example).

For more details on expressions and security, see the section `Complex Access Controls with Expressions <http://symfony.com/doc/current/book/security.html#book-security-expressions>`_ in the Symfony book.

Using CSRF Protection in the Login Form
---------------------------------------

The security extension also adds a modifer to support CSRF Protection in login forms. Please read `Using CSRF Protection in the Login Form <http://symfony.com/doc/current/cookbook/security/csrf_in_login_form.html>`_ from the Symfony Documentation for general CSRF Protection setup. The template for rendering should look like this:

.. code-block:: html+smarty

    <input type="hidden" name="_csrf_token" value="{'authenticate'|csrf_token}">

Enabling custom Extensions
==========================

To enable a Smarty extension, add it as a regular service in one of your configuration, and tag it with ``smarty.extension``. The creation of the extension itself is described in the next section.

.. configuration-block::

    .. code-block:: yaml

        services:
            smarty.extension.your_extension_name:
                class: Fully\Qualified\Extension\Class\Name
                arguments: [@service]
                tags:
                    - { name: smarty.extension }

Creating a SmartyBundle Extension
=================================

.. note::

    In version 0.1.0 class AbstractExtension was simply named Extension. Please
    update your code when migrating to 0.2.0.

An extension is a class that implements the `ExtensionInterface <https://github.com/noiselabs/SmartyBundle/tree/master/Extension/ExtensionInterface.php>`_. To make your life easier an abstract `AbstractExtension <https://github.com/noiselabs/SmartyBundle/tree/master/Extension/AbstractExtension.php>`_ class is provided, so you can inherit from it instead of implementing the interface. That way, you just need to implement the getName() method as the ``Extension`` class provides empty implementations for all other methods.

The ``getName()`` method must return a unique identifier for your extension:

.. code-block:: php

    namespace NoiseLabs\Bundle\SmartyBundle\Extension;

    class TranslationExtension extends AbstractExtension
    {
        public function getName()
        {
            return 'translator';
        }
    }

**Plugins**

Plugins can be registered in an extension via the ``getPlugins()`` method. Each element in the array returned by ``getPlugins()`` must implement `PluginInterface <https://github.com/noiselabs/SmartyBundle/tree/master/Extension/Plugin/PluginInterface.php>`_.

For each Plugin object three parameters are required. The plugin name comes in the first parameter and should be unique for each plugin type. Second parameter is an object of type ``ExtensionInterface`` and third parameter is the name of the method in the extension object used to perform the plugin action.

Please check available method parameters and plugin types in the `Extending Smarty With Plugins <http://www.smarty.net/docs/en/plugins.smarty>`_ webpage.

.. code-block:: php

    namespace NoiseLabs\Bundle\SmartyBundle\Extension;

    use NoiseLabs\Bundle\SmartyBundle\Extension\Plugin\BlockPlugin;

    class TranslationExtension extends Extension
    {
        public function getPlugins()
        {
            return array(
                new BlockPlugin('trans', $this, 'blockTrans'),
            );
        }

        public function blockTrans(array $params = array(), $message = null, $template, &$repeat)
        {
            $params = array_merge(array(
                'arguments' => array(),
                'domain'    => 'messages',
                'locale'    => null,
            ), $params);

            return $this->translator->trans($message, $params['arguments'], $params['domain'], $params['locale']);
        }
    }

**Filters**

Filters can be registered in an extension via the ``getFilters()`` method.

Each element in the array returned by ``getFilters()`` must implement `FilterInterface <https://github.com/noiselabs/SmartyBundle/tree/master/Extension/Filter/FilterInterface.php>`_.

.. code-block:: php

    namespace NoiseLabs\Bundle\SmartyBundle\Extension;

    use NoiseLabs\Bundle\SmartyBundle\Extension\Filter\PreFilter;

    class BeautifyExtension extends Extension
    {
        public function getFilters()
        {
            return array(
                new PreFilter($this, 'htmlTagsTolower'),
            );
        }

        // Convert html tags to be lowercase
        public function htmlTagsTolower($source, \Smarty_Internal_Template $template)
        {
            return preg_replace('!<(\w+)[^>]+>!e', 'strtolower("$1")', $source);
        }
    }

**Globals**

Global variables can be registered in an extension via the ``getGlobals()`` method.

There are no restrictions about the type of the array elements returned by ``getGlobals()``.

.. code-block:: php

    namespace NoiseLabs\Bundle\SmartyBundle\Extension;

    class GoogleExtension extends Extension
    {
        public function getGlobals()
        {
            return array(
                'ga_tracking' => 'UA-xxxxx-x'
            );
        }
    }
