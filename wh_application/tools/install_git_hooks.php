#!/usr/bin/env php
<?php

/**
 * WebHemi
 *
 * PHP version 5.6
 *
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webhemi.gixx-web.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@gixx-web.com so we can send you a copy immediately.
 *
 * @category  WebHemi
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

define('SCRIPT_VERSION', 'v.1.0');

chdir(__DIR__);
define('DOC_ROOT', realpath(dirname(__DIR__) . '/..'));

echo 'WebHemi git hooks installer (' . SCRIPT_VERSION . ')' . PHP_EOL . PHP_EOL;

// Create symlink for all git hook scripts
echo 'Create symlink for all git hook scripts: ' . PHP_EOL;
$handle = opendir(DOC_ROOT . '/wh_application/tools/git_hooks/');
if ($handle) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != '.' && $entry != '..') {
            if (file_exists(DOC_ROOT . '/wh_application/tools/git_hooks/' . $entry)
                && is_readable(DOC_ROOT . '/wh_application/tools/git_hooks/' . $entry)
            ) {
                echo "\t-> " . $entry . ': ';

                if (!file_exists(DOC_ROOT . '/.git/hooks/' . $entry)) {
                    if (symlink(
                        DOC_ROOT . '/wh_application/tools/git_hooks/' . $entry,
                        DOC_ROOT . '/.git/hooks/' . $entry
                    )) {
                        echo 'Done' . PHP_EOL;
                    }
                } else {
                    echo 'already exists...' . PHP_EOL;
                }
            }
        }
    }
}
