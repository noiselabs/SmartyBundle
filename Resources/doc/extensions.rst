.. _ch_extensions:
    
**********
Extensions
**********

Smarty[Bundle] extensions are packages that add new features to Smarty. The extension architecture implemented in the SmartyBundle is an object-oriented approach to the `plugin system <http://www.smarty.net/docs/en/plugins.smarty>`_ available in Smarty. The implemented architecture was inspired by `Twig Extensions <http://twig.sensiolabs.org/doc/extensions.html>`_.

Each extension object share a common interest (translation, routing, etc.) and provide methods that will be registered as a Smarty plugin before rendering a template. To learn about the plugin ecosystem in Smarty take a look at the `Smarty documentation page <http://www.smarty.net/docs/en/plugins.smarty>`_ on that subject.

The SmartyBundle comes with a few extensions to help you right away. These are described in the next section.


Actions Extension
=================

This extension tries to provide the same funcionality described in `Symfony2 - Templating - Embedding Controllers <http://symfony.com/doc/2.0/book/templating.html#embedding-controllers>`_.

Following the example presented in the link above, the Smarty equivalents are:

*Using a block function:*

.. code-block:: html+smarty

    {render attributes=['min'=>1,'max'=>3]}AcmeArticleBundle:Article:recentArticles{/render}

*Using a modifier:*

.. code-block:: html+smarty

    {'AcmeArticleBundle:Article:recentArticles'|render:['min'=>1,'max'=>3]}


Assetic Extension
=================

`Assetic <https://github.com/kriswallsmith/assetic>`_ is an asset management framework for PHP. This extensions provides support for it's usage in Symfony2 when using Smarty templates.

Assetic combines two major ideas: assets and filters. The assets are files such as CSS, JavaScript and image files. The filters are things that can be applied to these files before they are served to the browser. This allows a separation between the asset files stored in the application and the files actually presented to the user.

Using Assetic provides many advantages over directly serving the files. The files do not need to be stored where they are served from and can be drawn from various sources such as from within a bundle:

.. code-block:: html+smarty

    {javascripts
        assets='@AcmeFooBundle/Resources/public/js/*'
    }
    <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}

To bring in CSS stylesheets, you can use the same methodologies seen in this entry, except with the stylesheets tag:

.. code-block:: html+smarty

    {stylesheets
        assets='@AcmeFooBundle/Resources/public/css/*'
    }
    <link rel="stylesheet" href="{$asset_url}" />
    {/stylesheets}

Combining Assets
----------------

You can also combine several files into one. This helps to reduce the number of HTTP requests, which is great for front end performance. It also allows you to maintain the files more easily by splitting them into manageable parts. This can help with re-usability as you can easily split project-specific files from those which can be used in other applications, but still serve them as a single file:

.. code-block:: html+smarty

    {javascripts
        assets='@AcmeFooBundle/Resources/public/js/*,
                @AcmeBarBundle/Resources/public/js/form.js,
                @AcmeBarBundle/Resources/public/js/calendar.js'
    }
    <script src="{$asset_url}"></script>
    {/javascripts}

In the dev environment, each file is still served individually, so that you can debug problems more easily. However, in the prod environment, this will be rendered as a single script tag.

Block attributes
----------------

Here is a list of the possible attributes to define in the block function.

* ``assets``: A comma-separated list of files to include in the build (CSS, JS or image files)
* ``debug``: If set to true, the plugin will not combine your assets to allow easier debug
* ``filter``: A coma-separated list of filters to apply. Currently, only LESS and YuiCompressor (both CSS and JS) are supported
* ``combine``: Combine all of your CSS and JS files (overrides `debug`)
* ``output``: Defines the URLs that Assetic produces
* ``var_name``: The variable name that will be used to pass the asset URL to the <link> tag
* ``as``: An alias to ``var_name``. Example: ``as='js_url'``
* ``vars``: Array of asset variables. For a description of this recently added feature please check out the `Johannes Schmitt blog post <http://jmsyst.com/blog/asset-variables-in-assetic>`_ about Asset Variables in Assetic.

    **Note:** Unlike the examples given in the `Asset Variables in Assetic <http://jmsyst.com/blog/asset-variables-in-assetic>`_, which uses curly brackets for the ``vars`` placeholder we are using **square brackets** due to Smarty usage of curly brackets as syntax delimiters. So ``js/messages.{locale}.js`` becomes ``js/messages.[locale].js``.

Full example
------------

Example using all available attributes:

.. code-block:: html+smarty

    {javascripts
        assets='@AcmeFooBundle/Resources/public/js/*,
                @AcmeBarBundle/Resources/public/js/form.js,
                @AcmeBarBundle/Resources/public/js/calendar.js',
                @AcmeBarBundle/Resources/public/js/messages.[locale].js
        filter='yui_js'
        output='js/compiled/main.js'
        var_name='js_url'
        vars=['locale']
    }
    <script src="{$js_url}"></script>
    {/javascripts}

Symfony/Assetic documentation
-----------------------------

For further details please refer to the Symfony documentation pages about Assetic:

* `How to Use Assetic for Asset Management <http://symfony.com/doc/current/cookbook/assetic/asset_management.html>`_
* `How to Minify JavaScripts and Stylesheets with YUI Compressor <http://symfony.com/doc/current/cookbook/assetic/yuicompressor.html>`_

Assets Extension
================

Templates commonly refer to images, Javascript and stylesheets as assets. You could hard-code the path to these assets (e.g. ``/images/logo.png``), but the SmartyBundle provides a more dynamic option via the ``assets`` function:

.. code-block:: html+smarty

    <img src="{asset}images/logo.png{/asset}" />

    <link href="{asset}css/blog.css{/asset}" rel="stylesheet" type="text/css" />

This bundle also provides the ``assets_version`` function to return the version of the assets in a package. To set the version see the `assets_version configuration option in Symfony's Framework Bundle <http://symfony.com/doc/2.0/reference/configuration/framework.html#ref-framework-assets-version>`_.

Usage in template context:

.. code-block:: html+smarty

    {assets_version}


Form Extension
==============

*Coming soon*.


Routing Extension
=================

To generate URLs from a Smarty template you may use two block functions (``path`` and ``url``) provided by the `RoutingExtension <https://github.com/noiselabs/SmartyBundle/tree/master/Extension/RoutingExtension.php>`_.

.. code-block:: html+smarty

    <a href="{path slug='my-blog-post'}blog_show{/path}">
        Read this blog post.
    </a>

Absolute URLs can also be generated.

.. code-block:: html+smarty

    <a href="{url slug='my-blog-post'}blog_show{/url}">
        Read this blog post.
    </a>

Please see the `Symfony2 - Routing <http://symfony.com/doc/2.0/book/routing.html>`_ for full information about routing features and options in Symfony2.

Translation Extension
=====================

To help with message translation of static blocks of text in template context, the SmartyBundle, provides a translation extension. This extension is implemented in the class `TranslationExtension <https://github.com/noiselabs/SmartyBundle/tree/master/Extension/TranslationExtension.php>`_.

You may translate a message, in a template, using a block or modifier. Both methods support the following arguments:
    - **count**: In pluralization context, used to determine which translation to use and also to populate the %count% placeholder *(only available in transchoice)*;
    - **vars**: `Message placeholders <http://symfony.com/doc/2.0/book/translation.html#message-placeholders>`_;
    - **domain**: Message domain, an optional way to organize messages into groups;
    - **locale**: The locale that the translations are for (e.g. en_GB, en, etc);

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


`Message pluralization <http://symfony.com/doc/2.0/book/translation.html#pluralization>`_ can be achieved using ``transchoice``:

.. warning::
    
    Unlike the examples given in the `Symfony documentation <http://symfony.com/doc/2.0/book/translation.html#explicit-interval-pluralization>`_, which uses curly brackets for explicit interval pluralization we are using **square brackets** due to Smarty usage of curly brackets as syntax delimiters. So ``{0} There is no apples`` becomes ``[0] There is no apples``.

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

This extension provides access control inside a Smarty template. This part of the security process is called authorization, and it means that the system is checking to see if you have privileges to perform a certain action. For full details about the `Symfony2 security system <http://symfony.com/doc/2.0/book/security.html>`_ check it's `documentation page <http://symfony.com/doc/2.0/book/security.html>`_.

  If you want to check if the current user has a role inside a template, use the built-in ``is_granted`` modifier.

Usage:
    
.. code-block:: html+smarty

    {if 'IS_AUTHENTICATED_FULLY'|is_granted:$object:$field}
        access granted
    {else}
        access denied
    {/if}

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
    