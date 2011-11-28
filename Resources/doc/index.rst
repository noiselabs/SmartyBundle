SmartyBundle
============

This `Symfony2 <http://symfony.com/>`_ bundle provides integration for the `Smarty3 <http://www.smarty.net/>`_ template engine.

.. contents:: Contents

Introduction
------------

This bundle was created to support `Smarty <http://www.smarty.net/>`_ in Symfony2, providing an alternative to the `Twig <http://twig.sensiolabs.org/>`_ template engine natively supported.

	An effort was made to provide, where possible, the same user configuration and extensions available for the Twig bundle. This is to allow easy switching between the two bundles (at least I hope so!).

What is Smarty?
+++++++++++++++

Smarty is a template engine for PHP, facilitating the separation of presentation (HTML/CSS) from application logic. This implies that PHP code is application logic, and is separated from the presentation.

Some of Smarty's features: [#]_

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

.. [#] http://www.smarty.net/docs/en/what.is.smarty.tpl

Requirements
------------

* PHP 5.3.2 and up.
* Smarty 3
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

Enable the bundle in the kernel::

	<?php
	// app/AppKernel.php

	public function registerBundles()
	{
		$bundles = array(
			// ...
			new NoiseLabs\Bundle\SmartyBundle(),
		);
	}

4. Enable the Smarty template engine in the config
++++++++++++++++++++++++++++++++++++++++++++++++++

::

	# app/config/config.yml
	# ...
	templating:      { engines: ['twig', 'smarty'] }
	# ...

Usage
-----

Basic usage
+++++++++++

You can render a Smarty template instead of a Twig one simply by using the **.tpl** extension in the template name instead of .twig. The controller below renders the index.html.tpl template::

	// src/Acme/HelloBundle/Controller/HelloController.php

	public function indexAction($name)
	{
		return $this->render('AcmeHelloBundle:Hello:index.html.tpl', array('name' => $name));
	}

Template Inheritance
++++++++++++++++++++

Like Symfony2 PHP renderer or Twig, Smarty provides template inheritance.

	Template inheritance is an approach to managing templates that resembles object-oriented programming techniques. Instead of the traditional use of ``{include ...}`` tags to manage parts of templates, you can inherit the contents of one template to another (like extending a class) and change blocks of content therein (like overriding methods of a class.) This keeps template management minimal and efficient, since each template only contains the differences from the template it extends.

**Example:**

`layout.html.tpl`::

	<html>
	<head>
		<title>{block name=title}Default Page Title{/block}</title>
	</head>
	<body>
		{block name=body}{/block}
	</body>
	</html>

`mypage.html.tpl`::

	{extends file="file:AcmeHelloBundle:Default:layout.html.tpl"}
	{block name=title}My Page Title{/block}
	{block name=body}My HTML Page Body goes here{/block}

Output of mypage.html.tpl::

	<html>
	<head>
		<title>My Page Title</title>
	</head>
	<body>
		My HTML Page Body goes here
	</body>
	</html>

Instead of using the ``file:AcmeHelloBundle:Default:layout.html.tpl`` syntax you may use ``file:[WebkitBundle]/Default/layout.html.tpl`` which should be, performance wise, slightly better/faster.

Don't forget to use `file:` for 'logical' filenames
	Note the usage of the ``file:`` resource in the ``{extends}`` block. Even if the Smarty class variable ``$default_resource_type`` is set to `'file'` it is required to declare it, because we need to trigger a function to handle 'logical' file names (only if you are using the first syntax). Learn more about resources in the `Smarty Resources <http://www.smarty.net/docs/en/resources.tpl>`_ webpage.

`.html.tpl` or just `.tpl`
	The `.html.tpl` extension can simply be replaced by `.tpl`. We are prefixing with `.html` to stick with the Symfony convention of defining the format (`.html`) and engine (`.tpl`) for each template. See `Symfony's Template Formats section <http://symfony.com/doc/2.0/book/templating.html#template-formats>`_ for more information.

Injecting variables into all templates (i.e. Global Variables)
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

As exemplified in the `Symfony Cookbook <http://symfony.com/doc/current/cookbook/templating/global_variables.html>`_ it is possible to make a variable to be accessible to all the templates you use by configuring your `app/config/config.yml` file::

	# app/config/config.yml
	smarty:
		# ...
		globals:
			ga_tracking: UA-xxxxx-x

Now, the variable ga_tracking is available in all Smarty templates::

	<p>Our google tracking code is: {$ga_tracking} </p>
