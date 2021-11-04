.. _ch_contributing:

************************************
Contributing
************************************

Submitting bugs and feature requests
=====================================

Bugs and feature requests are tracked on `GitHub <https://github.com/noiselabs/SmartyBundle/issues>`_.

Coding Standards
================

When contributing to SmartyBundle you should follow the standards defined in the `PSR-0`_, `PSR-1`_ and `PSR-2`_. documents.

Here's a short example:

.. code-block:: php

    <?php
    /**
    * This file is part of NoiseLabs-SmartyBundle
    *
    * NoiseLabs-SmartyBundle is free software; you can redistribute it
    * and/or modify it under the terms of the GNU Lesser General Public
    * License as published by the Free Software Foundation; either
    * version 3 of the License, or (at your option) any later version.
    *
    * NoiseLabs-SmartyBundle is distributed in the hope that it will be
    * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
    * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    * Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public
    * License along with NoiseLabs-SmartyBundle; if not, see
    * <http://www.gnu.org/licenses/>.
    *
    * Copyright (C) 2011-2013 Vítor Brandão
    *
    * @category    NoiseLabs
    * @package     SmartyBundle
    * @author      Vítor Brandão <vitor@noiselabs.io>
    * @copyright   (C) 2011-2021 Vítor Brandão <vitor@noiselabs.io>
    * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
    * @link        https://noiselabs.io
    */

    namespace NoiseLabs\Bundle\SmartyBundle;

    /**
     * This class provides X.
     *
     * @author John Doe <john@example.com>
     */
    class FooBar
    {
        const SOME_CONST = 42;

        private $fooBar;

        /**
         * @param string $dummy Some argument description
         */
        public function __construct($dummy)
        {
            $this->fooBar = $this->transformText($dummy);
        }

        /**
         * @param string $dummy Some argument description
         * @return string|null Transformed input
         */
        private function transformText($dummy, $options = array())
        {
            $mergedOptions = array_merge($options, array(
                'some_default' => 'values',
            ));

            if (true === $dummy) {
                return;
            }
            if ('string' === $dummy) {
                if ('values' === $mergedOptions['some_default']) {
                    $dummy = substr($dummy, 0, 5);
                } else {
                    $dummy = ucwords($dummy);
                }
            }

            return $dummy;
        }
    }

Structure
---------

* Add a single space after each comma delimiter;

* Add a single space around operators (`==`, `&&`, ...);

* Add a blank line before `return` statements, unless the return is alone
  inside a statement-group (like an `if` statement);

* Use braces to indicate control structure body regardless of the number of
  statements it contains;

* Define one class per file - this does not apply to private helper classes
  that are not intended to be instantiated from the outside and thus are not
  concerned by the PSR-0 standard;

* Declare class properties before methods;

* Declare public methods first, then protected ones and finally private ones.

Naming Conventions
++++++++++++++++++

* Use camelCase, not underscores, for variable, function and method
  names, arguments;

* Use underscores for option, parameter names;

* Use namespaces for all classes;

* Suffix interfaces with `Interface`;

* Use alphanumeric characters and underscores for file names;

Documentation
+++++++++++++

* Add PHPDoc blocks for all classes, methods, and functions;

* Omit the `@return` tag if the method does not return anything;

License
+++++++

* SmartyBundle is released under the LGPL-3 license, and the license block has to be present at the top of every PHP file, before the namespace.

.. _`PSR-0`: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
.. _`PSR-1`: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
.. _`PSR-2`: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md


Authors
=======

Vítor Brandão - vitor@noiselabs.io ~ `twitter.com/noiselabs <http://twitter.com/noiselabs>`_ ~ `noiselabs.io <https://noiselabs.io>`_

See also the list of `contributors <https://github.com/noiselabs/SmartyBundle/contributors>`_ who participated in this project.

