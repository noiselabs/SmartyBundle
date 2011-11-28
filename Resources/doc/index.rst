SmartyBundle
============

This `Symfony2 <http://symfony.com/>`_ bundle provides integration for the `Smarty3 <http://www.smarty.net/>`_ template engine.

Introduction
------------

This bundle was created to support `Smarty <http://www.smarty.net/>`_ in Symfony2, providing an alternative to the `Twig <http://twig.sensiolabs.org/>`_ template engine natively supported.

An effort was made to provide, where possible, the same user configuration and extensions available for the Twig bundle. This will allow, I hope, an easy switch between the two bundles.

What is Smarty?
+++++++++++++++

Smarty is a template engine for PHP, facilitating the separation of presentation (HTML/CSS) from application logic. This implies that PHP code is application logic, and is separated from the presentation.

Some of Smarty's features:

* It is extremely fast.
* It is efficient since the PHP parser does the dirty work.
* No template parsing overhead, only compiles once.
* It is smart about recompiling only the template files that have changed.
* You can easily create your own custom functions and variable modifiers, so the template language is extremely extensible.
* Configurable template ``{delimiter}`` tag syntax, so you can use ``{$foo}, {{$foo}}, <!--{$foo}-->``, etc.
* The ``{if}..{elseif}..{else}..{/if}`` constructs are passed to the PHP parser, so the ``{if...}`` expression syntax can be as simple or as complex an evaluation as you like.
* Allows unlimited nesting of sections, if's etc.
* Built-in caching support.
* Arbitrary template sources.
* Template Inheritance for easy management of template content.
* Plugin architecture.

See the `Smarty3 Manual <http://www.smarty.net/docs/en/>`_ for other features and information on it's syntax, configuration and installation.

Requirements
------------

* PHP 5.3.2 and up.
* Symfony 2

Installation
------------

1. Download SmartyBundle
++++++++++++++++++++++++

This can be done in several ways, depending on your preference. The first method is the standard Symfony2 method.

**Using the vendors script**:

Add the following lines in your ``deps`` file:::

	[NoiseLabsSmartyBundle]
		git=git://github.com/noiselabs/SmartyBundle.git
		target=bundles/NoiseLabs/Bundle/SmartyBundle

Now, run the vendors script to download the bundle::

	$ php bin/vendors install


**Using submodules**:

If you prefer instead to use git submodules, then run the following::

	$ git submodule add git://github.com/noiselabs/SmartyBundle.git vendor/bundles/NoiseLabs/Bundle/SmartyBundle
	$ git submodule update --init

2. Configure the Autoloader
+++++++++++++++++++++++++++

Add the ``NoiseLabs`` namespace to your autoloader::

	<?php
	// app/autoload.php
	
	$loader->registerNamespaces(array(
		// ...
		'NoiseLabs' => __DIR__.'/../vendor/bundles',
	));


3. Enable the bundle
++++++++++++++++++++

Finally, enable the bundle in the kernel::

	<?php
	// app/AppKernel.php
	
	public function registerBundles()
	{
		$bundles = array(
			// ...
			new NoiseLabs\Bundle\SmartyBundle(),
		);
	}
