.. _ch_quickstart:

**********
Quickstart
**********

Requirements
------------

* `PHP <http://php.net>`_ 5.3.3 and up.
* `Symfony 2 <http://www.symfony.com>`_
* `Smarty 3 <http://www.smarty.net>`_

Installation
------------

SmartyBundle is composer-friendly.

Add SmartyBundle in your composer.json

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

Enable the bundle
-----------------

Enable the bundle in the kernel:

.. code-block:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new NoiseLabs\Bundle\SmartyBundle\SmartyBundle(),
        );
    }

Enable the Smarty template engine in the config

.. code-block:: yaml

    # app/config/config.yml
    framework:
        templating:      { engines: ['twig', 'smarty'] }
