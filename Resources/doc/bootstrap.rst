.. _ch_bootstrap:

*****************
Twitter Bootstrap
*****************

.. code-block:: javascript

    {
        "require": {
            "php": ">=5.3.8",
            "symfony/framework-standard-edition": "dev-master",
            "noiselabs/smarty-bundle": "dev-master",
            "mopa/bootstrap-bundle": "dev-master",
            "twitter/bootstrap": "master",
            "knplabs/knp-paginator-bundle": "dev-master",
            "knplabs/knp-menu-bundle": "dev-master",
            "craue/formflow-bundle": "dev-master",
            "thomas-mcdonald/bootstrap-sass": "dev-master"
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



