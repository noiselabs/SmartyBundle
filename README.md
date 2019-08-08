SmartyBundle
============

[![Join the chat at https://gitter.im/noiselabs/SmartyBundle](https://badges.gitter.im/noiselabs/SmartyBundle.svg)](https://gitter.im/noiselabs/SmartyBundle?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[@documentation]:   http://smartybundle.readthedocs.io/   "SmartyBundle Documentation"
[@php]:             http://php.net/                         "PHP: Hypertext Preprocessor"
[@smarty]:          http://www.smarty.net/                  "The compiling PHP template engine"
[@symfony]:         http://www.symfony.com/                 "High Performance PHP Framework for Web Development"

This [Symfony2+](http://symfony.com/) bundle provides integration for the [Smarty3](http://www.smarty.net/) template engine.

[![Total Downloads](https://poser.pugx.org/noiselabs/smarty-bundle/downloads.png)](https://packagist.org/packages/noiselabs/smarty-bundle)
[![Latest Stable Version](https://poser.pugx.org/noiselabs/smarty-bundle/v/stable.png)](https://packagist.org/packages/noiselabs/smarty-bundle)
[![Latest Unstable Version](https://poser.pugx.org/noiselabs/smarty-bundle/v/unstable.png)](https://packagist.org/packages/noiselabs/smarty-bundle)
[![Build Status](https://secure.travis-ci.org/noiselabs/SmartyBundle.png)](http://travis-ci.org/noiselabs/SmartyBundle)
[![License](https://poser.pugx.org/noiselabs/smarty-bundle/license.png)](https://packagist.org/packages/noiselabs/smarty-bundle)

See this compatibility chart to find out which version of the SmartyBundle you need depending on your version of Symfony.

| Symfony | SmartyBundle |
|---|---|
| `^4.0` | `^3.0` |
| `^3.0` | `^3.0` |
| `^2.8` | `^2.0` |
| `^2.1.0` | `~1.3` |
| `2.0.*` | `~1.0.0` |

### Update: Symfony 4 support (October 2018)

Symfony 4 support is being developed in the [3.0 branch](https://github.com/noiselabs/SmartyBundle/tree/3.0). Please checkout that branch if you wish to test it and/or contribute.

Requirements
------------

* [PHP][@php] 5.5.0 and up.
* [Smarty 3][@smarty]
* [Symfony][@symfony] 2.8 and up.

Installation
------------

Please see the [Documentation][@documentation] for installation instructions.

Documentation
-------------

Complete documentation is available on [Read the Docs][@documentation].

### Extensions under development

If you want to contribute to SmartyBundle please switch to the following branches when contributing to one of these extensions.

* **Assetic** - [assetic-extension](https://github.com/noiselabs/SmartyBundle/tree/assetic-extension)
* **Twitter Bootstrap** - [twitter-bootstrap](https://github.com/noiselabs/SmartyBundle/tree/twitter-bootstrap)
* **Forms** - [form-extension](https://github.com/noiselabs/SmartyBundle/tree/form-extension)
* **Security** - *completed, merged into master*

Development
-----------

If you want to contribute to SmartyBundle please ensure all tests are passing for the supported PHP versions. SmartyBundle currently supports PHP 7.0 till 7.3.

A `Makefile` is provided with some convenience commands. Use `make help` to list all available commands.

`make build` and `make build-parallel` will build 4 Docker containers: php70, php71, php72 and php73.

`make test` will run tests for each Docker container above.

You can target individual PHP versions using `make build-php71` and `make test-php73` for example.

License
-------

This bundle is licensed under the LGPLv3 License. See the [LICENSE file](https://github.com/noiselabs/SmartyBundle/blob/master/Resources/meta/LICENSE) for details.

Authors
-------

Vítor Brandão - <vitor@noiselabs.org> ~ [twitter.com/noiselabs](http://twitter.com/noiselabs) ~ [blog.noiselabs.org](http://blog.noiselabs.org)

See also the list of [contributors](https://github.com/noiselabs/SmartyBundle/contributors) who participated in this project.

Credits
-------

Kudos to [https://www.jetbrains.com/](JetBrains) for kindly supporting SmartyBundle through its [PhpStorm](https://www.jetbrains.com/phpstorm/) OpenSource licence.

[![phpstorm logo](Resources/assets/phpstorm-text.png)](Resources/assets/phpstorm-text.png)

Submitting bugs and feature requests
------------------------------------

Bugs and feature requests are tracked on [GitHub](https://github.com/noiselabs/SmartyBundle/issues).
