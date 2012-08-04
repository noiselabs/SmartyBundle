SmartyBundle
============

This [Symfony2](http://symfony.com/) bundle provides integration for the [Smarty3](http://www.smarty.net/) template engine.

[![Build Status](https://secure.travis-ci.org/noiselabs/SmartyBundle.png?branch=master)](http://travis-ci.org/noiselabs/SmartyBundle)

Requirements
------------

* PHP 5.3.2 and up.
* [Smarty 3](http://www.smarty.net)
* [Symfony 2](http://www.symfony.com)

Installation
------------

SmartyBundle is composer-friendly:

### 1. Add SmartyBundle in your composer.json:

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

### 2: Enable the bundle

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

For other installation methods (Symfony2.0 vendors script or submodules) please refer to the documentation below.

Documentation
-------------

The complete [documentation](https://github.com/noiselabs/SmartyBundle/blob/master/Resources/doc/index.rst) is available in the [`Resources/doc/index.rst`](https://github.com/noiselabs/SmartyBundle/blob/master/Resources/doc/index.rst) file in this bundle.

### Extensions under development

If you want to contribute to SmartyBundle please switch to the following branches when contributing to one of these extensions.

* **AsseticExtension** - https://github.com/noiselabs/SmartyBundle/tree/assetic-extension
* **FormExtension** - https://github.com/noiselabs/SmartyBundle/tree/form-extension
* **SecurityExtension** - *completed, merged into master*

Installation
------------

Follow the instructions described in the [documentation](https://github.com/noiselabs/SmartyBundle/blob/master/Resources/doc/index.rst).

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