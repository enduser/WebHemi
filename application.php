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
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(__DIR__);
require 'wh_application/vendor/autoload.php';
require 'wh_application/src/WebHemi/Application.php';

$app = new WebHemi\Application();

echo 'APPLICATION_PATH : ' . APPLICATION_PATH . '<br>';
echo 'APPLICATION_MODULE : ' . APPLICATION_MODULE . '<br>';
echo 'APPLICATION_MODULE_TYPE : ' . APPLICATION_MODULE_TYPE . '<br>';
echo 'APPLICATION_MODULE_URI : ' . APPLICATION_MODULE_URI . '<br>';
echo 'MAIN_DOMAIN : ' . MAIN_DOMAIN . '<br>';
echo 'APPLICATION_DOMAIN : ' . APPLICATION_DOMAIN . '<br>';
echo 'APPLICATION_THEME_NAME : ' . APPLICATION_THEME_NAME . '<br>';
echo 'APPLICATION_THEME_SYSTEM_PATH : ' . APPLICATION_THEME_SYSTEM_PATH . '<br>';
echo 'APPLICATION_THEME_RESOURCE_PATH : ' . APPLICATION_THEME_RESOURCE_PATH . '<br>';

$app->run();
