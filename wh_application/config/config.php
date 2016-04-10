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

use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;

require_once 'definitions.php';

$config              = [];
$serverData          = filter_input_array(INPUT_SERVER);
$applicationSettings = [
    'mainDomain' => null,
    'sessionCookiePrefix' => 'atsn',
    'autologinCookiePrefix' => 'atln',
    'applicationPath' => realpath(__DIR__ . '/../'),
    'applicationModule' => 'Website',
    'applicationModuleUri' => 'www',
    'applicationModuleList' => [],
    'applicationModuleType' => 'subdomain',
    'applicationModuleAdmin' => 'Admin',
    'applicationModuleWebsite' => 'Website',
    'applicationModuleTypeSubdomain' => 'subdomain',
    'applicationModuleTypeSubdirectory' => 'subdir',
    'applicationDomain' => null,
    'applicationThemeName' => '',
    'applicationThemeSystemPath' => '',
    'applicationThemeResourcePath' => '',
    'applicationThemeAdminLoginStyle' => '/resources/theme/webhemi/css/login.css',
    'applicationThemeAdminLoginScript' => '/resources/theme/webhemi/js/login.js',
];

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
        'DOCUMENT_ROOT' => realpath(__DIR__ . '/../../../../'),
        'REQUEST_SCHEME' => 'http',
        'CONTEXT_PREFIX' => '',
        'CONTEXT_DOCUMENT_ROOT' => realpath(__DIR__ . '/../../../../'),
        'SERVER_ADMIN' => '[no address given]',
        'SCRIPT_FILENAME' => realpath(__DIR__ . '/../../../../') . '/application.php',
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

// Load configuration from autoload path
foreach (Glob::glob($applicationSettings['applicationPath'] . '/config/autoload/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge($config, include $file);
}

$modules               = $config['applications'];
$domain                = $serverData['SERVER_NAME'];
$module                = $applicationSettings['applicationModule'];
$subDomain             = '';
$applicationModuleList = [];
$url                   = 'http' . ((isset($serverData['HTTPS']) && $serverData['HTTPS']) ? 's' : '') . '://'
    . $serverData['HTTP_HOST'] . $serverData['REQUEST_URI'] . $serverData['QUERY_STRING'];
$urlParts              = parse_url($url);

// If the host is not an IP address, then check the subdomain-based module names too
if (!preg_match(
    '/^((\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.){3}(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/',
    $urlParts['host']
)) {
    $domainParts = explode('.', $urlParts['host']);
    // @todo find out how to support complex TLDs like `.co.uk` or `.com.br`
    $tld         = array_pop($domainParts);
    $domain      = array_pop($domainParts) . '.' . $tld;
    // the rest is the sub-domain
    $subDomain   = implode('.', $domainParts);
}

// If no sub-domain presents, then it should be handled as 'www'
if (empty($subDomain)) {
    $subDomain = 'www';
}

// Additionally store the domains as well
$applicationSettings['mainDomain']        = $domain;
$applicationSettings['applicationDomain'] = $subDomain . '.' . $applicationSettings['mainDomain'];

// Ignore the first (actually an empty string) and last (the rest of the URL)
list($tmp, $subDir) = explode('/', $urlParts['path'], 3);

// Run through the available application-modules to validate and find active module
foreach ($modules as $moduleName => $moduleData) {
    // Skip invalid application
    if (!isset($moduleData['type']) || !isset($moduleData['path'])) {
        continue;
    }

    $applicationModuleList[] = $moduleName;

    if ($subDomain == 'www') {
        // Sub directory-based modules
        if (!empty($subDir)
            && $moduleData['type'] == $applicationSettings['applicationModuleTypeSubdirectory']
            && $moduleData['path'] == $subDir
        ) {
            $module = $moduleName;
            break;
        }
    } else {
        // Sub domain-based modules
        if ($moduleData['type'] == $applicationSettings['applicationModuleTypeSubdomain']
            && $moduleData['path'] == $subDomain
        ) {
            $module = $moduleName;
            break;
        }
    }
}

// If no valid applications available: terminate
if (empty($applicationModuleList)) {
    throw new \Exception('No applications available!');
}

if (empty($config['applications'][$module]['theme'])) {
    $config['applications'][$module]['theme'] = 'default';
}

$applicationSettings['applicationModule']     = $module;
$applicationSettings['applicationModuleType'] = $modules[$module]['type'];
$applicationSettings['applicationModuleUri']  = $modules[$module]['path'];
$applicationSettings['applicationModuleList'] = json_encode($applicationModuleList);
// Preset variables for default theme
$defaultThemePath                             = $applicationSettings['applicationPath'] . '/templates/default_theme';
$themeName                                    = 'default';
$themePath                                    = $defaultThemePath;
$themeConfig                                  = null;


// Check theme config
if ('default' != $config['applications'][$module]['theme']
    && file_exists($applicationSettings['applicationPath'] . '/templates/vendor_themes/' . $config['applications'][$module]['theme'] . '/theme.config.json')
) {
    // Read the theme config and validate it
    $themeConfig = @json_decode(
        file_get_contents(
            $applicationSettings['applicationPath'] . '/templates/vendor_themes/'
            . $config['applications'][$module]['theme'] . '/theme.config.json'
        ),
        true
    );
    if ($themeConfig) {
        $themeName = $config['applications'][$module]['theme'];
        $themePath = $applicationSettings['applicationPath'] . '/templates/vendor_themes/' . $themeName;

        // Check if it supports admin login
        if (!empty($themeConfig['templates']['options']['admin_login_customized'])
            && !empty($themeConfig['templates']['options']['admin_login_stylesheet'])
            && !empty($themeConfig['templates']['options']['admin_login_javascript'])
        ) {
            $themeTemplatePath = str_replace($applicationSettings['applicationPath'], 'wh_application', $themePath);

            $applicationSettings['applicationThemeAdminLoginStyle']  = '/resources/theme/' . $themeName . '/'
                . $themeConfig['templates']['options']['admin_login_stylesheet'];
            $applicationSettings['applicationThemeAdminLoginScript'] = '/resources/theme/' . $themeName . '/'
                . $themeConfig['templates']['options']['admin_login_javascript'];
        }
    }
}

// If there is a valid custom theme, save it's name
$applicationSettings['applicationThemeName'] = $themeName;

// For Admin application we allow only the default theme.
// Only the login page can use custom CSS and JS and the variables are already saved or having the defaults
if (!$themeConfig || $applicationSettings['applicationModuleAdmin'] == $applicationSettings['applicationModule']) {
    // Reset theme to read default template
    $themePath = $defaultThemePath;
    $themeName = 'default';
    $themeConfig = json_decode(file_get_contents($defaultThemePath . '/theme.config.json'), true);
}
// Apply config
$config = ArrayUtils::merge($config, $themeConfig);

// Set theme paths
$applicationSettings['applicationThemeSystemPath'] = $themePath;
$applicationSettings['applicationThemeResourcePath'] = '/resources/theme/' . (('default' == $themeName) ? 'webhemi' : $themeName);

// Load specific application's config (Admin / Website routes and fixed template maps)
$applicationConfigFile = $applicationSettings['applicationPath'] . '/config/application/' .
    ($applicationSettings['applicationModuleAdmin'] == $applicationSettings['applicationModule']
        ? $applicationSettings['applicationModuleAdmin']
        : $applicationSettings['applicationModuleWebsite']
    ) . '.php';
$config = ArrayUtils::merge($config, include $applicationConfigFile);

// Fix template map paths
$themeTemplatePath = str_replace($applicationSettings['applicationPath'], 'wh_application', $themePath);

foreach ($config['templates']['paths'] as $alias => $template) {
    $config['templates']['paths'][$alias] = [$themeTemplatePath . '/' . $template];
}

foreach ($config['templates']['map'] as $alias => $template) {
    $config['templates']['map'][$alias] = $themeTemplatePath . '/' . $template;
}

// Create application-wide constants
createDefinitions($applicationSettings);

// fix route paths. Piping the applications are very buggy.
if (APPLICATION_MODULE_TYPE == APPLICATION_MODULE_TYPE_SUBDIR) {
    foreach ($config['routes'] as $index => $route) {
        $config['routes'][$index]['path'] = '/' . APPLICATION_MODULE_URI . $config['routes'][$index]['path'];
    }
}

// Cleanup global variables
unset($serverData, $applicationSettings, $module, $modules, $domain, $subDomain, $url, $urlParts, $domainParts, $tld);
unset($tmp, $moduleName, $moduleData, $defaultThemePath, $themePath, $themeName, $themeConfig, $applicationConfigFile);
unset($themeTemplatePath, $applicationModuleList, $alias, $template, $subDir, $file, $index, $route);
//var_dump($config);exit;
return new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
