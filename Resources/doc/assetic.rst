.. index::
    single: Assetic

.. _ch_assetic:

*******
Assetic
*******

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