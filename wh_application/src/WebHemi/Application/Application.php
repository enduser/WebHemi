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

namespace WebHemi\Application;

/**
 * Class Application
 * @package WebHemi\Application
 */
class Application
{
    const APPLICATION_MODULE_ADMIN = 'Admin';

    const APPLICATION_MODULE_WEBSITE = 'Website';

    const APPLICATION_MODULE_TYPE_SUBDOMAIN = 'subdomain';

    const APPLICATION_MODULE_TYPE_SUBDIR = 'subdir';

    const AUTOLOGIN_COOKIE_PREFIX = 'atln';

    const SESSION_COOKIE_PREFIX = 'atsn';

    /** @var array  */
    public static $APPLICATION_MODULE_LIST = [self::APPLICATION_MODULE_ADMIN, self::APPLICATION_MODULE_WEBSITE];

    /** @var string  */
    public static $APPLICATION_MODULE = self::APPLICATION_MODULE_WEBSITE;

    /** @var string  */
    public static $APPLICATION_MODULE_TYPE = self::APPLICATION_MODULE_TYPE_SUBDOMAIN;

    /** @var string  */
    public static $APPLICATION_MODULE_URI = '/';

    /** @var string */
    public static $MAIN_DOMAIN = null;

    /** @var string */
    public static $APPLICATION_DOMAIN = null;

    /**
     * Init application properties
     *
     * @param array $modules
     */
    public static function setApplicationProperties(array $modules = [])
    {
        // Define Application list
        if (!empty($modules)) {
            static::$APPLICATION_MODULE_LIST = json_encode(array_keys($modules));
        }

        $domain = $_SERVER['SERVER_NAME'];

        // set a default module
        $module     = static::$APPLICATION_MODULE;
        $subDomain  = '';

        // if no URL is present, then the current URL will be used
        $url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 's' : '') . '://';
        $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'];

        // parse the URL into
        $urlParts = parse_url($url);

        // if the host is not an IP address, then we can check the subdomain-based module names too
        if (!preg_match(
            '/^((\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.){3}(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/',
            $urlParts['host']
        )) {
            $domainParts = explode('.', $urlParts['host']);
            // @todo find out how to support complex TLDs like `.co.uk` or `.com.br`
            $tld = array_pop($domainParts);
            $domain = array_pop($domainParts) . '.' . $tld;
            // the rest is the sub-domain
            $subDomain = implode('.', $domainParts);
        }

        // if no sub-domain presents, then it should be handled as 'www'
        if (empty($subDomain)) {
            $subDomain = 'www';
        }

        // additionally we store the domains as well
        static::$MAIN_DOMAIN = $domain;
        static::$APPLICATION_DOMAIN = $subDomain . '.' . static::$MAIN_DOMAIN;

        // we ignore the first (actually an empty string) and last (the rest of the URL)
        list($tmp, $subDir) = explode('/', $urlParts['path'], 3);
        unset($tmp);

        // we run through the available application-modules
        foreach ($modules as $moduleName => $moduleData) {
            // subdirectory-based modules
            if ($subDomain == 'www') {
                if (!empty($subDir)
                    && $moduleData['type'] == static::APPLICATION_MODULE_TYPE_SUBDIR
                    && $moduleData['path'] == $subDir
                ) {
                    $module = $moduleName;
                    break;
                }
            } else {
                // subDomain-based modules
                if ($moduleData['type'] == static::APPLICATION_MODULE_TYPE_SUBDOMAIN
                    && $moduleData['path'] == $subDomain
                ) {
                    $module = $moduleName;
                    break;
                }
            }
        }

        static::$APPLICATION_MODULE = $module;

        static::$APPLICATION_MODULE_TYPE = isset($modules[$module])
            ? $modules[$module]['type']
            : ($module == static::APPLICATION_MODULE_WEBSITE ? 'subdomain' : 'subdir');

        static::$APPLICATION_MODULE_URI = isset($modules[$module])
            ? $modules[$module]['path']
            : ($module == static::APPLICATION_MODULE_WEBSITE ? 'www' : '/');
    }
}
