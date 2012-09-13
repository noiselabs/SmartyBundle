SmartyBundle
============

[@documentation]:   http://smartybundle.noiselabs.org/  "SmartyBundle Documentation"
[@php]:             http://php.net/                     "PHP: Hypertext Preprocessor"
[@smarty]:          http://www.smarty.net/              "The compiling PHP template engine"
[@symfony]:         http://www.symfony.com/             "High Performance PHP Framework for Web Development"

This [Symfony2](http://symfony.com/) bundle provides integration for the [Smarty3](http://www.smarty.net/) template engine.

[![Build Status](https://secure.travis-ci.org/noiselabs/SmartyBundle.png?branch=master)](http://travis-ci.org/noiselabs/SmartyBundle)

**Caution:** This bundle is developed in sync with [Symfony's repository](https://github.com/symfony/symfony).
For maximum compatibility with Symfony 2.0.x, you need to use releases 1.0.x of this bundle.

Requirements
------------

* [PHP][@php] 5.3.3 and up.
* [Smarty 3][@smarty]
* [Symfony 2][@symfony]

Installation
------------

SmartyBundle is composer-friendly.

### 1. Add SmartyBundle in your composer.json

```js
{
    "require": {
        "noiselabs/smarty-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update noiselabs/smarty-bundle
```

Composer will install the bundle to your project's `vendor/noiselabs` directory.

### 2. Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new NoiseLabs\Bundle\SmartyBundle\SmartyBundle(),
    );
}
```

### 3. Enable the Smarty template engine in the config

``` yml
    # app/config/config.yml
    framework:
        templating:      { engines: ['twig', 'smarty'] }
```

For other installation methods (Symfony-2.0 vendors script or git submodules) please refer to the documentation below.

Documentation
-------------

Complete documentation is available in the [SmartyBundle website][@documentation].

License
-------

This bundle is licensed under the LGPLv3 License. See the [LICENSE file](https://github.com/noiselabs/SmartyBundle/blob/master/Resources/meta/LICENSE) for details.

Authors
-------

Vítor Brandão - <noisebleed@noiselabs.org> ~ [twitter.com/noiselabs](http://twitter.com/noiselabs) ~ [blog.noiselabs.org](http://blog.noiselabs.org)

See also the list of [contributors](https://github.com/noiselabs/SmartyBundle/contributors) who participated in this project.

Submitting bugs and feature requests
------------------------------------

Bugs and feature requests are tracked on [GitHub](https://github.com/noiselabs/SmartyBundle/issues).
