# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## 2.0.2 _(2021-11-01)_

### Changed

* Fix symfony/form requirement by @Pchol in https://github.com/noiselabs/SmartyBundle/pull/73

## 2.0.1 _(2018-10-07)_

### Changed

* Update Smarty version in composer.json by @eufrost in https://github.com/noiselabs/SmartyBundle/pull/66
* Remove "target-dir" by @ok1br in https://github.com/noiselabs/SmartyBundle/pull/64
* Add url/path modifier example to documentation by @naucon in https://github.com/noiselabs/SmartyBundle/pull/67
* Can pass null argument in AssetsExtension constructor by @Pchol in https://github.com/noiselabs/SmartyBundle/pull/71
* Update symfony/framework-bundle and symfony/templating requirement by @Pchol in https://github.com/noiselabs/SmartyBundle/pull/69

## 2.0.0 _(2016-09-26)_

### Added
- Compatibility with Symfony 3.0

### Changed
- AssetsExtension now uses the new [Asset Component](http://symfony.com/doc/current/components/asset/introduction.html)
- SecurityExtension now uses `security.authorization_checker` instead of `security.context`
- Use `security.csrf.token_manager` instead of `form.csrf_provider`

### Removed
- Drop Support for Symfony < 2.8
  - `Symfony\Component\HttpKernel\Log\LoggerInterface` as been replaced by `Psr\Log\LoggerInterface`
- Drop Support for PHP < 5.5

## 1.3.1 _(2016-07-09)_

### Changed
- [#55](https://github.com/noiselabs/SmartyBundle/pull/55) Improve CSRF token compatibility with symfony 2.1
- [#56](https://github.com/noiselabs/SmartyBundle/pull/56) Improve security documentation

## 1.3.0 _(2016-03-24)_

### Added
- Compatibility with Symfony 2.8

### Removed
- [#50](https://github.com/noiselabs/SmartyBundle/pull/50) Removed support for unsupported versions of Symfony

### Fixed
- [#51](https://github.com/noiselabs/SmartyBundle/pull/51) fix problem with "plugin tag are already registered" debug messages

