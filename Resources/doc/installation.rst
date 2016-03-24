.. _ch_installation:

************
Installation
************

Download SmartyBundle
=====================

Tell composer to add the bundle to your ``composer.json``' by running the command:

.. code-block:: bash

    $ php composer.phar require noiselabs/smarty-bundle

Composer will install the bundle to your project's `vendor/noiselabs` directory.

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

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        # ...
        framework:
            templating:      { engines: ['twig', 'smarty'] }

