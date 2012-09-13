.. _ch_installation:

************
Installation
************

Download SmartyBundle
=====================

This can be done in several ways, depending on your preference. The first method is the standard Symfony2.1 method.

Symfony 2.1.x --- Composer
---------------------------

Add SmartyBundle in your ``composer.json``':

.. code-block:: json

    {
        "require": {
            "noiselabs/smarty-bundle": "dev-master"
        }
    }


Now tell composer to download the bundle by running the command:

.. code-block:: bash

    $ php composer.phar update noiselabs/smarty-bundle

Composer will install the bundle to your project's `vendor/noiselabs` directory.

Symfony 2.0.x --- Using the vendors script
-------------------------------------------

Add the following lines in your ``deps`` file:

.. code-block:: ini

    [SmartyBundle]
        git=git://github.com/noiselabs/SmartyBundle.git
        target=bundles/NoiseLabs/Bundle/SmartyBundle

Now, run the vendors script to download the bundle:

.. code-block:: bash

    $ php bin/vendors install


Using submodules
----------------

If you prefer instead to use git submodules, then run the following:

.. code-block:: bash

    $ git submodule add git://github.com/noiselabs/SmartyBundle.git vendor/bundles/NoiseLabs/Bundle/SmartyBundle
    $ git submodule update --init


Configure the Autoloader (only if not using composer!)
======================================================

Add the ``NoiseLabs`` namespace to your autoloader:

.. code-block:: php

    // app/autoload.php

    $loader->registerNamespaces(array(
        // ...
        'NoiseLabs\\Bundle' => __DIR__.'/../vendor/bundles',
    ));


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
