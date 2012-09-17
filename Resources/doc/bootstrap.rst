.. index::
    single: Twitter Bootstrap

.. _ch_bootstrap:

*****************************
Twitter Bootstrap integration
*****************************

`Twitter Bootstrap <http://twitter.github.com/bootstrap/>`_ is an extensive front-end toolkit for developing web sites and applications released by Twitter developers.

`MopaBootstrapBundle <https://github.com/phiamo/MopaBootstrapBundle>`_ is a Symfony2 Bundle that integrates Bootstrap into Symfony2 project.

SmartyBundle builds upon these tools to give you a quick way to start a project using **Symfony2 + TwitterBootstrap + Smarty3**. Enjoy!

.. note::
    
    Examples presented here use only the most common/preferred tool for a given task. For complete reference please check `MopaBootstrapBundle documentation <https://github.com/phiamo/MopaBootstrapBundle/blob/master/Resources/doc/index.md>`_.

Installation
============

Composer (Symfony 2.1.x)
---------------------------

Add the following packages and scripts to ``composer.json``:

.. code-block:: javascript

    {
        "require": {
            "php": ">=5.3.8",
            
            "symfony/framework-standard-edition": "dev-master",
            
            "noiselabs/smarty-bundle": "dev-twitter-bootstrap",
            
            "mopa/bootstrap-bundle": "dev-master",
            "twitter/bootstrap": "master",
            "knplabs/knp-paginator-bundle": "dev-master",
            "knplabs/knp-menu-bundle": "dev-master",
            "craue/formflow-bundle": "dev-master",
            "thomas-mcdonald/bootstrap-sass": "dev-master",
            "mopa/bootstrap-sandbox-bundle": "dev-master",
            "liip/theme-bundle": "dev-master"
        },
        
        "scripts": {
            "post-install-cmd": [
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",
                "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrap",
                "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrapSass"
            ],
            "post-update-cmd": [
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
                "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",
                "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrap",
                "Mopa\\Bundle\\BootstrapBundle\\Composer\\ScriptHandler::postInstallSymlinkTwitterBootstrapSass"
            ]
        },
        
        "include-path": ["vendor/smarty/smarty/distribution/libs/"],
        
        "repositories": [
            {
                "type": "package",
                "package": {
                    "version": "master",
                    "name": "twitter/bootstrap",
                    "source": {
                        "url": "https://github.com/twitter/bootstrap.git",
                        "type": "git",
                        "reference": "master"
                    },
                    "dist": {
                        "url": "https://github.com/twitter/bootstrap/zipball/master",
                        "type": "zip"
                    }
                }
            },
            {
                "type":"package",
                "package": {
                    "version":"dev-master",
                    "name":"thomas-mcdonald/bootstrap-sass",
                    "source": {
                        "url":"https://github.com/thomas-mcdonald/bootstrap-sass.git",
                        "type":"git",
                        "reference":"master"
                    },
                    "dist": {
                        "url":"https://github.com/thomas-mcdonald/bootstrap-sass/zipball/master",
                        "type":"zip"
                    }
                }
            }
        ]
    }

Now tell composer to update vendors by running the command:
    
.. code-block:: bash    

    $ php composer.phar update

Enable the bundles
------------------

.. code-block:: php

    // app/AppKernel.php
    
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new NoiseLabs\Bundle\SmartyBundle\SmartyBundle(),
                
                new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
                new Mopa\Bundle\BootstrapSandboxBundle\MopaBootstrapSandboxBundle()
                new Knp\Bundle\MenuBundle\KnpMenuBundle(),
                new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
                new Liip\ThemeBundle\LiipThemeBundle()
            );

            // ...

            return $bundles;
        }
    }

Enable the Smarty template engine
---------------------------------

.. configuration-block::

    .. code-block:: yaml
    
        # app/config/config.yml
        
        framework:
            templating:      { engines: ['twig', 'smarty'] }
            
Configuration
=============

.. configuration-block::

    .. code-block:: yaml
    
        # app/config/config.yml
        
        # MopaBootstrap Configuration
        #
        mopa_bootstrap:
            # To load the navbar extensions (template helper, CompilerPass, etc.)
            navbar: ~