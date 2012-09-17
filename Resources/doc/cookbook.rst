.. index::
    single: Cookbook

.. _ch_cookbook:

********
Cookbook
********

Injecting variables into all templates (i.e. Global Variables)
==============================================================

.. index::
    single: Global Variables

As exemplified in the `Symfony Cookbook <http://symfony.com/doc/current/cookbook/templating/global_variables.html>`_ it is possible to make a variable to be accessible to all the templates you use by configuring your `app/config/config.yml` file:

.. code-block:: yaml

    # app/config/config.yml
    smarty:
        # ...
        globals:
            ga_tracking: UA-xxxxx-x

Now, the variable ga_tracking is available in all Smarty templates:

.. code-block:: html+smarty

    <p>Our google tracking code is: {$ga_tracking} </p>

Trim unnecessary whitespace from HTML markup
===================================================

.. index::
    single: Trimwhitespace

This technique can speed up your website by eliminating extra whitespace characters and thus reducing page size. It removes HTML comments (except ConditionalComments) and reduces multiple whitespace to a single space everywhere but ``<script>``, ``<pre>``, ``<textarea>`` [#]_.

To enable this feature add the ``trimwhitespace`` output filter in ``app/config/config.yml``:

.. code-block:: yaml
    :emphasize-lines: 7

    # app/config/config.yml
    
    # Smarty configuration
    smarty:
        options:
            autoload_filters:
                output: [trimwhitespace]

.. [#] http://stackoverflow.com/a/9207456/545442                