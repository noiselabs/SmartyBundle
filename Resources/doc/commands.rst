.. _ch_commands:

********
Commands
********

SmartyBundle extends the default Symfony2 command line interface by providing the commands described below.

Compile Command
===============

``smarty:compile``

Compiles all known Smarty templates.

Usage
-----

The following command finds all known Smarty templates and compiles them:

.. code-block:: bash

    php app/console smarty:compile

Alternatively you may pass an optional ``@AcmeMyBundle`` argument to only search
for templates in a specific bundle:

.. code-block:: bash

    php app/console smarty:compile @AcmeMyBundle

Available Options
-----------------

* ``--force`` - Force the compilation of all templates even if they weren't modified.
* ``--verbose`` - Print information about each template being currently compiled.