# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.0] Unreleased

### Added
- Compatibility with Symfony 3.0

### Changed
- AssetsExtension now uses the new [Asset Component](http://symfony.com/doc/current/components/asset/introduction.html)
- SecurityExtension now uses `security.authorization_checker` instead of `security.context`

### Removed
- Drop Support for Symfony < 2.8
- Drop Support for PHP < 5.5

## [1.3.0] - 2016-03-24

### Added
- Compatibility with Symfony 2.8

### Removed
- [#50](https://github.com/noiselabs/SmartyBundle/pull/50) Removed support for unsupported versions of Symfony

### Fixed
- [#51](https://github.com/noiselabs/SmartyBundle/pull/51) fix problem with "plugin tag are already registered" debug messages

