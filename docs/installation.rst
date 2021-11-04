.. _ch_installation:

************
Installation
************

Download SmartyBundle
=====================

Tell composer to add the bundle to your :file:`composer.json` by running the command:

.. code-block:: bash

    $ php composer.phar require noiselabs/smarty-bundle:4.0.x-dev

Composer will install the bundle to your project's :file:`vendor/noiselabs` directory.

Enable the bundle
=================

Enable the bundle in the kernel:

.. code-block:: php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new NoiseLabs\Bundle\SmartyBundle\SmartyBundle(),
        );
    }

Enable the Smarty template engine in the config
===============================================

.. code-block:: yaml

    # app/config/config.yml
    # ...
    framework:
        templating:
            engines: ['twig', 'php', 'smarty']

.. warning::

    You need to enable the ``php`` engine as well. Otherwise some services will not work as expected. See `https://github.com/symfony/symfony/issues/14719`_
