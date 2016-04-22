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
    $requiredAttributes = [
        'applicationModuleAdmin',
        'applicationModuleWebsite',
        'applicationModuleTypeSubdomain',
        'applicationModuleTypeSubdirectory',
        'autologinCookiePrefix',
        'sessionCookiePrefix',
        'applicationPath',
        'applicationModule',
        'applicationModuleList',
        'applicationModuleType',
        'applicationModuleUri',
        'mainDomain',
        'applicationDomain',
        'applicationThemeName',
        'applicationThemeSystemPath',
        'applicationThemeResourcePath',
        'applicationThemeAdminLoginStyle',
        'applicationThemeAdminLoginScript'
    ];

    if (empty($applicationSettings) || count(array_diff($requiredAttributes, array_keys($applicationSettings))) > 0) {
        throw new \InvalidArgumentException('Cannot create constants. Some of the required attributes are missing.');
    }

    defined('APPLICATION_MODULE_ADMIN') || define('APPLICATION_MODULE_ADMIN', $applicationSettings['applicationModuleAdmin']);
    defined('APPLICATION_MODULE_WEBSITE') || define('APPLICATION_MODULE_WEBSITE', $applicationSettings['applicationModuleWebsite']);
    defined('APPLICATION_MODULE_TYPE_SUBDOMAIN') || define('APPLICATION_MODULE_TYPE_SUBDOMAIN', $applicationSettings['applicationModuleTypeSubdomain']);
    defined('APPLICATION_MODULE_TYPE_SUBDIR') || define('APPLICATION_MODULE_TYPE_SUBDIR', $applicationSettings['applicationModuleTypeSubdirectory']);
    defined('AUTOLOGIN_COOKIE_PREFIX') || define('AUTOLOGIN_COOKIE_PREFIX', $applicationSettings['autologinCookiePrefix']);
    defined('SESSION_COOKIE_PREFIX') || define('SESSION_COOKIE_PREFIX', $applicationSettings['sessionCookiePrefix']);
    defined('APPLICATION_PATH') || define('APPLICATION_PATH', $applicationSettings['applicationPath']);
    defined('APPLICATION_MODULE') || define('APPLICATION_MODULE', $applicationSettings['applicationModule']);
    defined('APPLICATION_MODULE_LIST') || define('APPLICATION_MODULE_LIST', $applicationSettings['applicationModuleList']);
    defined('APPLICATION_MODULE_TYPE') || define('APPLICATION_MODULE_TYPE', $applicationSettings['applicationModuleType']);
    defined('APPLICATION_MODULE_URI') || define('APPLICATION_MODULE_URI', $applicationSettings['applicationModuleUri']);
    defined('MAIN_DOMAIN') || define('MAIN_DOMAIN', $applicationSettings['mainDomain']);
    defined('APPLICATION_DOMAIN') || define('APPLICATION_DOMAIN', $applicationSettings['applicationDomain']);
    defined('APPLICATION_THEME_NAME') || define('APPLICATION_THEME_NAME', $applicationSettings['applicationThemeName']);
    defined('APPLICATION_THEME_SYSTEM_PATH') || define('APPLICATION_THEME_SYSTEM_PATH', $applicationSettings['applicationThemeSystemPath']);
    defined('APPLICATION_THEME_RESOURCE_PATH') || define('APPLICATION_THEME_RESOURCE_PATH', $applicationSettings['applicationThemeResourcePath']);
    defined('APPLICATION_THEME_ADMIN_LOGIN_STYLE') || define('APPLICATION_THEME_ADMIN_LOGIN_STYLE', $applicationSettings['applicationThemeAdminLoginStyle']);
    defined('APPLICATION_THEME_ADMIN_LOGIN_SCRIPT') || define('APPLICATION_THEME_ADMIN_LOGIN_SCRIPT', $applicationSettings['applicationThemeAdminLoginScript']);
}

/**
 * @return array|mixed
 */
function getServerVariables()
{
    $serverData = filter_input_array(INPUT_SERVER);

    // Fake server variables for unit test
    if (php_sapi_name() == 'cli') {
        $serverData = [
            'REDIRECT_APPLICATION_ENV' => 'dev',
            'REDIRECT_STATUS' => '200',
            'APPLICATION_ENV' => 'dev',
            'HTTP_HOST' => 'unittest.dev',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_CACHE_CONTROL' => 'max-age=0',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8,hu;q=0.6',
            'PATH' => '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin',
            'SERVER_SIGNATURE' => 'Apache/2.4.10 (Ubuntu) Server at admin.webhemi.dev Port 80',
            'SERVER_SOFTWARE' => 'Apache/2.4.10 (Ubuntu)',
            'SERVER_NAME' => 'unitest.dev',
            'SERVER_ADDR' => '127.0.0.1',
            'SERVER_PORT' => '80',
            'REMOTE_ADDR' => '192.168.100.1',
            'DOCUMENT_ROOT' => realpath(__DIR__ . '/../../'),
            'REQUEST_SCHEME' => 'http',
            'CONTEXT_PREFIX' => '',
            'CONTEXT_DOCUMENT_ROOT' => realpath(__DIR__ . '/../../'),
            'SERVER_ADMIN' => '[no address given]',
            'SCRIPT_FILENAME' => realpath(__DIR__ . '/../../') . '/application.php',
            'REMOTE_PORT' => '58453',
            'REDIRECT_URL' => '/',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => '',
            'REQUEST_URI' => '/',
            'SCRIPT_NAME' => '/application.php',
            'PHP_SELF' => '/application.php',
            'REQUEST_TIME_FLOAT' => microtime(true),
            'REQUEST_TIME' => time()
        ];
    }

    return $serverData;
}

/**
 * Developer usage only
 */
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
