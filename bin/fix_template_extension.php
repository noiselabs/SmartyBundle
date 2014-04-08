#!/usr/bin/env php
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
 * Copyright (C) 2011-2014 Vítor Brandão
 *
 * @category    NoiseLabs
 * @package     SmartyBundle
 * @copyright   (C) 2011-2014 Vítor Brandão <vitor@noiselabs.org>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
 */

$rootDir = __DIR__.'/..';
require_once $rootDir.'/autoload.php.dist';

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

// check for Symfony existance
if (!is_dir($rootDir.'/vendor/symfony')) {
    printf('Fatal Error:'.PHP_EOL.
    '  The Symfony library is required to run this script and could not'.PHP_EOL.
    '  be found in "vendor/symfony". Please run bin/vendors.php or manually'.PHP_EOL.
    '  place a copy of the Symfony git repo in "vendor/".'.PHP_EOL,
        $rootDir
    );
}

function usage($exitcode = 0, $msg = false)
{
    if ($msg) {
        echo $msg.PHP_EOL.PHP_EOL;
    }

    echo 'Usage: '.basename(__FILE__).' --src=DIRECTORY [--from=OLD_EXTENSION] [--to=NEW_EXTENSION] [--git] [--fix]'.PHP_EOL.PHP_EOL;

    echo 'Options:'.PHP_EOL.
    '  --src=DIRECTORY     Project directory to look for source files.'.PHP_EOL.
    '  --from=EXTENSION    Original template extension (defaults to "tpl").'.PHP_EOL.
    '  --to=EXTENSION      New template extension (defaults to "smarty").'.PHP_EOL.
    '  --git               When updating file extensions use "git mv" command'.PHP_EOL.
    '                      instead of just renaming the file,'.PHP_EOL.
    '  --fix               Really update files instead of just doing a dry-run,'.PHP_EOL
    ;

    exit($exitcode);
}

$bullet = '*';

// script options
$shortopts = 'h';
$longopts = array(
    'fix',
    'from::',
    'help',
    'git',
    'to::',
    'src:'
);

$options = getopt($shortopts, $longopts);

if (isset($options['h']) || isset($options['help'])) {
    usage(0);
}

// source dir argument is required
if (!isset($options['src'])) {
    usage(-1, 'Fatal Error: The source directory argument is missing.');
} else {
    $srcDir = $options['src'];
}

// set defaults
$oldExtension = isset($options['from']) ? $options['from'] : 'tpl';
$newExtension = isset($options['to']) ? $options['to'] : 'smarty';
$fix = isset($options['fix']);
$git = isset($options['git']);

// check if source dir exists
if (!is_dir($srcDir)) {
    usage(-1, sprintf('Fatal Error: The source directory "%s" does not exist.', $srcDir));
} else {
    $srcDir = realpath($srcDir);
}

$finder = new Finder();
$finder
    ->files()
    ->name('*.php')
    ->name('*.'.$oldExtension)
    ->in($srcDir)
    ->exclude('.git')
    ->exclude('app/cache')
    ->exclude('vendor')
;

$count = 0;
$renamed = 0;

if ($git) {
    $exeFinder = new ExecutableFinder();
    $gitBin = $exeFinder->find('git', '/usr/bin/git');
    echo $bullet.' Using git ;)'.PHP_EOL;
}

if ($fix) {
    printf('%s FIXING *.php and *.%s files in "%s"...',
        $bullet,
        $oldExtension,
        $srcDir
    );
} else {
    printf('%s Analyzing *.php and *.%s files in "%s"...',
        $bullet,
        $oldExtension,
        $srcDir
    );
}
echo PHP_EOL.PHP_EOL;

// give 1 second to the user before all hell breaks loose
sleep(1);

foreach ($finder as $file) {
    /* @var $file Symfony\Component\Finder\SplFileInfo */

    $oldFilename = $file->getRealpath();
    $old = file_get_contents($oldFilename);
    $new = $old;

    $rename = ($file->getExtension() == $oldExtension);

    // start regex!
    $new = preg_replace_callback('/(\'|")([^\.]+\.\w+\.)('.$oldExtension.')\1/', function ($matches) use ($new, $oldExtension, $newExtension) {
        return $matches[1].$matches[2].$newExtension.$matches[1];
    }, $new);

    $changed = ($new != $old);

    if ($changed || $rename) {
        $count++;
    }

    if ($changed && $fix) {
        file_put_contents($file->getRealpath(), $new);
    }

    // calculate the new filename
    if ($rename) {
        $oldRelativePathname = $file->getRelativePathname();
        $newRelativePathname = dirname($oldRelativePathname).DIRECTORY_SEPARATOR.basename($oldRelativePathname, '.'.$oldExtension).'.'.$newExtension;
        $newFilename = $srcDir.DIRECTORY_SEPARATOR.$newRelativePathname;

        if ($fix) {
            if ($git) {
                $process = new Process(sprintf('%s mv %s %s',
                    $gitBin,
                    $oldFilename,
                    $newFilename
                ), $srcDir);
                $process->run();
                if (!$process->isSuccessful()) {
                    printf(PHP_EOL.'FATAL ERROR: git mv: %s', $process->getErrorOutput());
                    exit(-1);
                }
            } else {
            rename($oldFilename, $newFilename);
            }
        }
    }

    if ($rename) {
        printf('%4d) %s => %s'.PHP_EOL, $count, $file->getRelativePathname(), $newRelativePathname);
    } elseif ($changed) {
        printf('%4d) %s'.PHP_EOL, $count, $file->getRelativePathname());
    }
}

if (!$fix) {
    printf(PHP_EOL."%s No changes were made to the filesystem. Let's try again with --fix to actually fix something?".PHP_EOL,
        $bullet
    );
}

if (!$git) {
    printf(PHP_EOL."%s Do you know a --git option is available? When enabled, files are renamed using 'git mv'.".PHP_EOL,
        $bullet
    );
}

exit($count ? 1 : 0);
