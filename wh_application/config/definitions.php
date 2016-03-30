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
    define('APPLICATION_MODULE_TYPE', $applicationSettings['applicationModuleType']);
    define('APPLICATION_MODULE_URI', $applicationSettings['applicationModuleUri']);
    define('MAIN_DOMAIN', $applicationSettings['mainDomain']);
    define('APPLICATION_DOMAIN', $applicationSettings['applicationDomain']);
    define('APPLICATION_THEME_NAME', $applicationSettings['applicationThemeName']);
    define('APPLICATION_THEME_SYSTEM_PATH', $applicationSettings['applicationThemeSystemPath']);
    define('APPLICATION_THEME_RESOURCE_PATH', $applicationSettings['applicationThemeResourcePath']);
    define('APPLICATION_THEME_RESOURCE_LOGIN_PATH', $applicationSettings['applicationThemeResourceLoginPath']);
}
