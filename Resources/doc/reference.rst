.. _ch_reference:

***********************
Configuration Reference
***********************

The example below uses YAML format. Please adapt the example if using XML or PHP.

``app/config/config.yml``::

    smarty:

        options:

            # See http://www.smarty.net/docs/en/api.variables.tpl
            allow_php_templates:
            allow_php_templates:
            auto_literal:
            autoload_filters:
            cache_dir:                           %kernel.cache_dir%/smarty/cache
            cache_id:
            cache_lifetime:
            cache_locking:
            cache_modified_check:
            caching:
            caching_type:
            compile_check:
            compile_dir:                         %kernel.cache_dir%/smarty/templates_c
            compile_id:
            compile_locking:
            compiler_class:
            config_booleanize:
            config_dir:                          %kernel.root_dir%/config/smarty
            config_overwrite:
            config_read_hidden:
            debug_tpl:
            debugging:
            debugging_ctrl:
            default_config_type:
            default_modifiers:
            default_resource_type:               file
            default_config_handler_func:
            default_template_handler_func:
            direct_access_security:
            error_reporting:
            escape_html:
            force_cache:
            force_compile:
            inheritance_merge_compiled_includes: true
            left_delimiter:
            locking_timeout:
            merge_compiled_includes:
            php_handling:
            plugins_dir:                         []
            right_delimiter:
            smarty_debug_id:
            template_dir:                        %kernel.root_dir%/Resources/views
            trim_whitespace :                    false
            trusted_dir:
            use_include_path:                    false
            use_sub_dirs:                        true

        globals:

            # Examples:
            foo:                 "@bar"
            pi:                  3.14

Available options
=================

allow_php_templates
    By default the PHP template file resource is disabled. Setting $allow_php_templates to TRUE will enable PHP template files.

auto_literal
    The Smarty delimiter tags ``{`` and ``}`` will be ignored so long as they are surrounded by white space. This behavior can be disabled by setting auto_literal to false.

autoload_filters
    If there are some filters that you wish to load on every template invocation, you can specify them using this variable and Smarty will automatically load them for you. The variable is an associative array where keys are filter types and values are arrays of the filter names.

cache_dir
    This is the name of the directory where template caches are stored. By default this is ``%kernel.cache_dir%/smarty/cache``. **This directory must be writeable by the web server**.

cache_id
    Persistent cache_id identifier. As an alternative to passing the same ``$cache_id`` to each and every function call, you can set this ``$cache_id`` and it will be used implicitly thereafter. With a ``$cache_id`` you can have multiple cache files for a single call to ``display()`` or ``fetch()`` depending for example from different content of the same template.

cache_lifetime
    This is the length of time in seconds that a template cache is valid. Once this time has expired, the cache will be regenerated. See the page `Smarty Class Variables - $cache_lifetime <http://www.smarty.net/docs/en/variable.cache.lifetime.tpl>`_ for more details.

cache_locking
    Cache locking avoids concurrent cache generation. This means resource intensive pages can be generated only once, even if they've been requested multiple times in the same moment. Cache locking is disabled by default.

cache_modified_check
    If set to ``TRUE``, Smarty will respect the If-Modified-Since header sent from the client. If the cached file timestamp has not changed since the last visit, then a '304: Not Modified' header will be sent instead of the content. This works only on cached content without ``{insert}`` tags.

caching
    This tells Smarty whether or not to cache the output of the templates to the ``$cache_dir``. By default this is set to the constant ``Smarty::CACHING_OFF``. If your templates consistently generate the same content, it is advisable to turn on ``$caching``, as this may result in significant performance gains.

caching_type
    This property specifies the name of the caching handler to use. It defaults to file, enabling the internal filesystem based cache handler.

compile_check
    Upon each invocation of the PHP application, Smarty tests to see if the current template has changed (different timestamp) since the last time it was compiled. If it has changed, it recompiles that template. If the template has yet not been compiled at all, it will compile regardless of this setting. By default this variable is set to ``TRUE``. Once an application is put into production (ie the templates won't be changing), the compile check step is no longer needed. Be sure to set $compile_check to ``FALSE`` for maximum performance. Note that if you change this to ``FALSE`` and a template file is changed, you will *not* see the change since the template will not get recompiled. If $caching is enabled and $compile_check is enabled, then the cache files will get regenerated if an involved template file or config file was updated. As of Smarty 3.1 ``$compile_check`` can be set to the value ``Smarty::COMPILECHECK_CACHEMISS``.
    This enables Smarty to revalidate the compiled template, once a cache file is regenerated. So if there was a cached template, but it's expired, Smarty will run a single compile_check before regenerating the cache.

compile_dir
    This is the name of the directory where compiled templates are located. By default this is ``%kernel.cache_dir%/smarty/templates_c``. **This directory must be writeable by the web server**.

compile_id
    Persistant compile identifier. As an alternative to passing the same ``$compile_id`` to each and every function call, you can set this $compile_id and it will be used implicitly thereafter. With a ``$compile_id`` you can work around the limitation that you cannot use the same ``$compile_dir`` for different ``$template_dirs``. If you set a distinct ``$compile_id`` for each ``$template_dir`` then Smarty can tell the compiled templates apart by their ``$compile_id``. If you have for example a prefilter that localizes your templates (that is: translates language dependend parts) at compile time, then you could use the current language as ``$compile_id`` and you will get a set of compiled templates for each language you use. Another application would be to use the same compile directory across multiple domains / multiple virtual hosts.

compile_locking
    Compile locking avoids concurrent compilation of the same template. Compile locking is enabled by default.

compiler_class
    Specifies the name of the compiler class that Smarty will use to compile the templates. The default is 'Smarty_Compiler'. For advanced users only.

config_booleanize
    If set to ``TRUE``, config files values of ``on/true/yes`` and ``off/false/no`` get converted to boolean values automatically. This way you can use the values in the template like so: ``{if #foobar#}...{/if}``. If ``foobar`` was ``on``, ``true`` or ``yes``, the ``{if}`` statement will execute. Defaults to ``TRUE``.

config_dir
    This is the directory used to store config files used in the templates. Default is ``%kernel.root_dir%/config/smarty``.

config_overwrite
    If set to ``TRUE``, the default then variables read in from config files will overwrite each other. Otherwise, the variables will be pushed onto an array. This is helpful if you want to store arrays of data in config files, just list each element multiple times.

config_read_hidden
    If set to ``TRUE``, hidden sections ie section names beginning with a period(.) in config files can be read from templates. Typically you would leave this ``FALSE``, that way you can store sensitive data in the config files such as database parameters and not worry about the template loading them. ``FALSE`` by default.

debug_tpl
    This is the name of the template file used for the debugging console. By default, it is named ``debug.tpl`` and is located in the ``SMARTY_DIR``.

debugging
    This enables the debugging console. The console is a javascript popup window that informs you of the included templates, variables assigned from php and config file variables for the current script. It does not show variables assigned within a template with the ``{assign}`` function.

debugging_ctrl
    This allows alternate ways to enable debugging. ``NONE`` means no alternate methods are allowed. ``URL`` means when the keyword ``SMARTY_DEBUG`` is found in the ``QUERY_STRING``, debugging is enabled for that invocation of the script. If ``$debugging`` is ``TRUE``, this value is ignored.

default_config_type
    This tells smarty what resource type to use for config files. The default value is ``file``, meaning that ``$smarty->configLoad('test.conf')`` and ``$smarty->configLoad('file:test.conf')`` are identical in meaning.

default_modifiers
    This is an array of modifiers to implicitly apply to every variable in a template. For example, to HTML-escape every variable by default, use ``array('escape:"htmlall"')``. To make a variable exempt from default modifiers, add the 'nofilter' attribute to the output tag such as ``{$var nofilter}``.

default_resource_type
    This tells smarty what resource type to use implicitly. The default value is file, meaning that ``{include 'index.tpl'}`` and ``{include 'file:index.tpl'}`` are identical in meaning.

default_config_handler_func
    This function is called when a config file cannot be obtained from its resource.

default_template_handler_func
    This function is called when a template cannot be obtained from its resource.

direct_access_security
    Direct access security inhibits direct browser access to compiled or cached template files. Direct access security is enabled by default.

error_reporting
    When this value is set to a non-null-value it's value is used as php's error_reporting level inside of ``display()`` and ``fetch()``.

escape_html
    Setting ``$escape_html`` to ``TRUE`` will escape all template variable output by wrapping it in ``htmlspecialchars({$output}``, ``ENT_QUOTES``, ``SMARTY_RESOURCE_CHAR_SET``);, which is the same as ``{$variable|escape:"html"}``. Template designers can choose to selectively disable this feature by adding the ``nofilter`` flag: ``{$variable nofilter}``. This is a compile time option. If you change the setting you must make sure that the templates get recompiled.

force_cache
    This forces Smarty to (re)cache templates on every invocation. It does not override the ``$caching`` level, but merely pretends the template has never been cached before.

force_compile
    This forces Smarty to (re)compile templates on every invocation. This setting overrides ``$compile_check``. By default this is ``FALSE``. This is handy for development and debugging. It should never be used in a production environment. If ``$caching`` is enabled, the cache file(s) will be regenerated every time.

inheritance_merge_compiled_includes
    In Smarty 3.1 template inheritance is a compile time process. All the extending of ``{block}`` tags is done at compile time and the parent and child templates are compiled in a single compiled template. ``{include}`` subtemplate could also ``{block}`` tags. Such subtemplate could not compiled by it's own because it could be used in other context where the ``{block}`` extended with a different result. For that reason the compiled code of ``{include}`` subtemplates gets also merged in compiled inheritance template.

    Merging the code into a single compile template has some drawbacks.
    1. You could not use variable file names in ``{include}`` Smarty would use the ``{include}`` of compilation time.
    2. You could not use individual compile_id in ``{include}``.
    3. Seperate caching of subtemplate was not possible.
    4. Any change of the template directory structure between calls was not necessarily seen.

    Starting with 3.1.15 some of the above conditions got checked and resulted in an exception. It turned out that a couple of users did use some of above and now got exceptions.

    To resolve this starting with 3.1.16 there is a new configuration parameter ``$inheritance_merge_compiled_includes``. For most backward compatibility its default setting is true. With this setting all ``{include}`` subtemplate will be merge into the compiled inheritance template, but the above cases could be rejected by exception.

    If ``$smarty->inheritance_merge_compiled_includes = false;`` ``{include}`` subtemplate will not be merged. You must now manually merge all ``{include}`` subtemplate which do contain ``{block}`` tags. This is done by setting the ``"inline"`` option. ``{include file='foo.bar' inline}``

    1. In case of a variable file name like {include file=$foo inline} you must you the variable in a compile_id  ``$smarty->compile_id = $foo;``
    2. If you use individual compile_id in {include file='foo.tpl' compile_id=$bar inline} it must be used in the global compile_id as well  ``$smarty->compile_id = $foo;``
    3. If call templates with different template_dir configurations and a parent could same named child template from different folders
    you must make the folder name part of the compile_id.

    In the upcomming major release Smarty 3.2 inheritance will no longer be a compile time process. All restrictions will be then removed.

left_delimiter
    This is the left delimiter used by the template language. Default is ``{``.

locking_timeout
    This is maximum time in seconds a cache lock is valid to avoid dead locks. The deafult value is 10 seconds.

merge_compiled_includes
    By setting ``$merge_compiled_includes`` to ``TRUE`` Smarty will merge the compiled template code of subtemplates into the compiled code of the main template. This increases rendering speed of templates using a many different sub-templates. Individual sub-templates can be merged by setting the inline option flag within the ``{include}`` tag. ``$merge_compiled_includes`` does not have to be enabled for the inline merge.

php_handling
    This tells Smarty how to handle PHP code embedded in the templates. There are four possible settings, the default being ``Smarty::PHP_PASSTHRU``. Note that this does NOT affect php code within ``{php}{/php}`` tags in the template. Settings: ``Smarty::PHP_PASSTHRU`` - Smarty echos tags as-is; ``Smarty::PHP_QUOTE`` - Smarty quotes the tags as html entities; ``Smarty::PHP_REMOVE`` - Smarty removes the tags from the templates; ``Smarty::PHP_ALLOW`` - Smarty will execute the tags as PHP code.

plugins_dir
    This is the directory or directories where Smarty will look for the plugins that it needs. Default is ``plugins/`` under the ``SMARTY_DIR``. If you supply a relative path, Smarty will first look under the ``SMARTY_DIR``, then relative to the current working directory, then relative to the PHP include_path. If ``$plugins_dir`` is an array of directories, Smarty will search for your plugin in each plugin directory in the order they are given. **While using the SmartyBundle you may add plugins by setting services tagged with smarty.extension. See section Extensions for more information.**

right_delimiter
    This is the right delimiter used by the template language. Default is ``}``.

smarty_debug_id
    The value of ``$smarty_debug_id`` defines the URL keyword to enable debugging at browser level. The default value is ``SMARTY_DEBUG``.

template_dir
    This is the name of the default template directory. If you do not supply a resource type when including files, they will be found here. By default this is ``%kernel.root_dir%/Resources/views``. ``$template_dir`` can also be an array of directory paths: Smarty will traverse the directories and stop on the first matching template found. **Note that the SmartyEngine included in this bundle already add the template directory of each registered Bundle**.

trim_whitespace
    Trim unnecessary whitespace from HTML markup.

trusted_dir
    ``$trusted_dir`` is only for use when security is enabled. This is an array of all directories that are considered trusted. Trusted directories are where you keep php scripts that are executed directly from the templates with ``{include_php}``.

use_include_path
    This tells smarty to respect the ``include_path`` within the ``File Template Resource`` handler and the plugin loader to resolve the directories known to $template_dir. The flag also makes the plugin loader check the ``include_path`` for ``$plugins_dir``.

use_sub_dirs
    Smarty will create subdirectories under the compiled templates and cache directories if $use_sub_dirs is set to ``TRUE``, default is ``FALSE``. In an environment where there are potentially tens of thousands of files created, this may help the filesystem speed. On the other hand, some environments do not allow PHP processes to create directories, so this must be disabled which is the default. Sub directories are more efficient, so use them if you can. Theoretically you get much better perfomance on a filesystem with 10 directories each having 100 files, than with 1 directory having 1000 files. This was certainly the case with Solaris 7 (UFS)... with newer filesystems such as ext3 and especially reiserfs, the difference is almost nothing.