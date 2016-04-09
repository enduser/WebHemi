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

/**
 * @param array $applicationSettings
 */
function createDefinitions(array $applicationSettings)
{
    define('APPLICATION_MODULE_ADMIN', $applicationSettings['applicationModuleAdmin']);
    define('APPLICATION_MODULE_WEBSITE', $applicationSettings['applicationModuleWebsite']);
    define('APPLICATION_MODULE_TYPE_SUBDOMAIN', $applicationSettings['applicationModuleTypeSubdomain']);
    define('APPLICATION_MODULE_TYPE_SUBDIR', $applicationSettings['applicationModuleTypeSubdirectory']);
    define('AUTOLOGIN_COOKIE_PREFIX', $applicationSettings['autologinCookiePrefix']);
    define('SESSION_COOKIE_PREFIX', $applicationSettings['sessionCookiePrefix']);
    define('APPLICATION_PATH', $applicationSettings['applicationPath']);
    define('APPLICATION_MODULE', $applicationSettings['applicationModule']);
    define('APPLICATION_MODULE_LIST', $applicationSettings['applicationModuleList']);
    define('APPLICATION_MODULE_TYPE', $applicationSettings['applicationModuleType']);
    define('APPLICATION_MODULE_URI', $applicationSettings['applicationModuleUri']);
    define('MAIN_DOMAIN', $applicationSettings['mainDomain']);
    define('APPLICATION_DOMAIN', $applicationSettings['applicationDomain']);
    define('APPLICATION_THEME_NAME', $applicationSettings['applicationThemeName']);
    define('APPLICATION_THEME_SYSTEM_PATH', $applicationSettings['applicationThemeSystemPath']);
    define('APPLICATION_THEME_RESOURCE_PATH', $applicationSettings['applicationThemeResourcePath']);
    define('APPLICATION_THEME_ADMIN_LOGIN_STYLE', $applicationSettings['applicationThemeAdminLoginStyle']);
    define('APPLICATION_THEME_ADMIN_LOGIN_SCRIPT', $applicationSettings['applicationThemeAdminLoginScript']);
}

function dumpDefinitions()
{
    echo 'APPLICATION_MODULE_TYPE_SUBDOMAIN: ' . APPLICATION_MODULE_TYPE_SUBDOMAIN . '<br>';
    echo 'APPLICATION_MODULE_TYPE_SUBDIR: ' . APPLICATION_MODULE_TYPE_SUBDIR . '<br>';
    echo 'AUTOLOGIN_COOKIE_PREFIX: ' . AUTOLOGIN_COOKIE_PREFIX . '<br>';
    echo 'SESSION_COOKIE_PREFIX: ' . SESSION_COOKIE_PREFIX . '<br>';
    echo 'APPLICATION_PATH: ' . APPLICATION_PATH . '<br>';
    echo 'APPLICATION_MODULE: ' . APPLICATION_MODULE . '<br>';
    echo 'APPLICATION_MODULE_LIST: ' . APPLICATION_MODULE_LIST . '<br>';
    echo 'APPLICATION_MODULE_TYPE: ' . APPLICATION_MODULE_TYPE . '<br>';
    echo 'APPLICATION_MODULE_URI: ' . APPLICATION_MODULE_URI . '<br>';
    echo 'MAIN_DOMAIN: ' . MAIN_DOMAIN . '<br>';
    echo 'APPLICATION_DOMAIN: ' . APPLICATION_DOMAIN . '<br>';
    echo 'APPLICATION_THEME_NAME: ' . APPLICATION_THEME_NAME . '<br>';
    echo 'APPLICATION_THEME_SYSTEM_PATH: ' . APPLICATION_THEME_SYSTEM_PATH . '<br>';
    echo 'APPLICATION_THEME_RESOURCE_PATH: ' . APPLICATION_THEME_RESOURCE_PATH . '<br>';
    echo 'APPLICATION_THEME_ADMIN_LOGIN_STYLE: ' . APPLICATION_THEME_ADMIN_LOGIN_STYLE . '<br>';
    echo 'APPLICATION_THEME_ADMIN_LOGIN_SCRIPT: ' . APPLICATION_THEME_ADMIN_LOGIN_SCRIPT . '<br>';
}
