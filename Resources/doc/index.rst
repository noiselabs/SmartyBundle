.. SmartyBundle documentation master file, created by
   sphinx-quickstart on Sun Aug  5 02:30:06 2012.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

############
SmartyBundle
############

    This `Symfony2 <http://symfony.com/>`_ bundle provides integration for the `Smarty3 <http://www.smarty.net/>`_ template engine.

.. image:: _static/images/symfony.png
    :target: http://symfony.com
    :height: 50px

.. image:: _static/images/smarty.png
    :target: http://smarty.net
    :height: 50px


********
Summary
********

.. toctree::
    :maxdepth: 3
    :numbered:

    intro
    installation
    usage
    extensions
    assetic
    forms
    bootstrap
    reference
    contributing

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


*******************
Indices and tables
*******************

* :ref:`genindex`
* :ref:`search`

*******
License
*******

This bundle is licensed under the LGPL-3 License. See the `LICENSE file <https://github.com/noiselabs/SmartyBundle/blob/master/Resources/meta/LICENSE>`_ for details.

*******
Credits
*******

:Author: Vítor Brandão (noisebleed@noiselabs.org)
:Version: |release|
:Date: |today|

.. note::

    A lot of the content found in this documentation was "borrowed" from Smarty and Symfony2 documentation pages and websites. Credits goes to Smarty and Symfony authors and contributors.