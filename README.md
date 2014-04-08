SmartyBundle
============

[@documentation]:   https://smartybundle.readthedocs.org/   "SmartyBundle Documentation"
[@php]:             http://php.net/                         "PHP: Hypertext Preprocessor"
[@smarty]:          http://www.smarty.net/                  "The compiling PHP template engine"
[@symfony]:         http://www.symfony.com/                 "High Performance PHP Framework for Web Development"

This [Symfony2](http://symfony.com/) bundle provides integration for the [Smarty3](http://www.smarty.net/) template engine.

[![Total Downloads](https://poser.pugx.org/noiselabs/smarty-bundle/downloads.png)](https://packagist.org/packages/noiselabs/smarty-bundle)
[![Latest Stable Version](https://poser.pugx.org/noiselabs/smarty-bundle/v/stable.png)](https://packagist.org/packages/noiselabs/smarty-bundle)
[![Latest Unstable Version](https://poser.pugx.org/noiselabs/smarty-bundle/v/unstable.png)](https://packagist.org/packages/noiselabs/smarty-bundle)
[![Build Status](https://secure.travis-ci.org/noiselabs/SmartyBundle.png)](http://travis-ci.org/noiselabs/SmartyBundle)
[![License](https://poser.pugx.org/noiselabs/smarty-bundle/license.png)](https://packagist.org/packages/noiselabs/smarty-bundle)

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

Complete documentation is available on [Read the Docs][@documentation].

### Extensions under development

If you want to contribute to SmartyBundle please switch to the following branches when contributing to one of these extensions.

* **Assetic** - [assetic-extension](https://github.com/noiselabs/SmartyBundle/tree/assetic-extension)
* **Twitter Bootstrap** - [twitter-bootstrap](https://github.com/noiselabs/SmartyBundle/tree/twitter-bootstrap)
* **Forms** - [form-extension](https://github.com/noiselabs/SmartyBundle/tree/form-extension)
* **Security** - *completed, merged into master*

License
-------

This bundle is licensed under the LGPLv3 License. See the [LICENSE file](https://github.com/noiselabs/SmartyBundle/blob/master/Resources/meta/LICENSE) for details.

Authors
-------

Vítor Brandão - <vitor@noiselabs.org> ~ [twitter.com/noiselabs](http://twitter.com/noiselabs) ~ [blog.noiselabs.org](http://blog.noiselabs.org)

See also the list of [contributors](https://github.com/noiselabs/SmartyBundle/contributors) who participated in this project.

Submitting bugs and feature requests
------------------------------------

Bugs and feature requests are tracked on [GitHub](https://github.com/noiselabs/SmartyBundle/issues).
