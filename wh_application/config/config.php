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
    'applicationModuleAdmin' => 'Admin',
    'applicationModuleWebsite' => 'Website',
    'applicationModuleTypeSubdomain' => 'subdomain',
    'applicationModuleTypeSubdirectory' => 'subdir',
    'autologinCookiePrefix' => 'atln',
    'sessionCookiePrefix' => 'atsn',
    'applicationModuleList' => [],
    'applicationPath' => realpath(__DIR__ . '/../'),
    'applicationModule' => 'Website',
    'applicationModuleType' => 'subdomain',
    'applicationModuleUri' => '/',
    'mainDomain' => null,
    'applicationDomain' => null,
    'applicationThemeName' => '',
    'applicationThemeSystemPath' => '',
    'applicationThemeResourcePath' => '',
    'applicationThemeResourceLoginPath' => '',
];

// Load configuration from autoload path
foreach (Glob::glob($applicationSettings['applicationPath'] . '/config/autoload/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge($config, include $file);
}

$modules = $config['applications'];

// Define Application list
if (!empty($modules)) {
    $applicationSettings['applicationModuleList'] = json_encode(array_keys($modules));
}

$domain     = $serverData['SERVER_NAME'];
// set a default module
$module     = $applicationSettings['applicationModule'];
$subDomain  = '';

// if no URL is present, then the current URL will be used
$url = 'http' . ((isset($serverData['HTTPS']) && $serverData['HTTPS']) ? 's' : '') . '://';
$url .= $serverData['HTTP_HOST'] . $serverData['REQUEST_URI'] . $serverData['QUERY_STRING'];

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
$applicationSettings['mainDomain']        = $domain;
$applicationSettings['applicationDomain'] = $subDomain . '.' . $applicationSettings['mainDomain'];

// we ignore the first (actually an empty string) and last (the rest of the URL)
list($tmp, $subDir) = explode('/', $urlParts['path'], 3);

// we run through the available application-modules
foreach ($modules as $moduleName => $moduleData) {
    // subdirectory-based modules
    if ($subDomain == 'www') {
        if (!empty($subDir)
            && $moduleData['type'] == $applicationSettings['applicationModuleTypeSubdirectory']
            && $moduleData['path'] == $subDir
        ) {
            $module = $moduleName;
            break;
        }
    } else {
        // subDomain-based modules
        if ($moduleData['type'] == $applicationSettings['applicationModuleTypeSubdomain']
            && $moduleData['path'] == $subDomain
        ) {
            $module = $moduleName;
            break;
        }
    }
}

$applicationSettings['applicationModule']     = $module;
$applicationSettings['applicationModuleType'] = isset($modules[$module])
    ? $modules[$module]['type']
    : ($module == $applicationSettings['applicationModuleWebsite'] ? 'subdomain' : 'subdir');
$applicationSettings['applicationModuleUri']  = isset($modules[$module])
    ? $modules[$module]['path']
    : ($module == $applicationSettings['applicationModuleWebsite'] ? 'www' : '/');

$defaultThemePath = $applicationSettings['applicationPath'] . '/templates/default_theme';
$themePath        = $defaultThemePath;
$theme            = isset($config['applications'][$applicationSettings['applicationModule']])
    ? $config['applications'][$applicationSettings['applicationModule']]['theme']
    : 'default';

// Update theme path or reset theme to default
if ('default' != $theme
    && file_exists($applicationSettings['applicationPath'] . '/templates/vendor_themes/' . $theme . '/theme.config.json')) {
    $themePath = $applicationSettings['applicationPath'] . '/templates/vendor_themes/' . $theme;
} else {
    $theme = 'default';
}

$applicationSettings['applicationThemeName'] = $theme;

// For Admin application we allow only the default theme. Login page can use custom CSS and JS only
if ($applicationSettings['applicationModuleAdmin'] == $applicationSettings['applicationModule']
    && $themePath !== $defaultThemePath
) {
    // Reset theme (except the name) to read default template
    $themePath = $defaultThemePath;
    $theme = 'default';
}

$applicationSettings['applicationThemeSystemPath'] = $themePath;

// Set resource path
if ('default' == $theme) {
    $applicationSettings['applicationThemeResourcePath'] = '/resources/theme/webhemi';
} else {
    $applicationSettings['applicationThemeResourcePath'] = '/resources/theme/' . $theme;
}

// Set resource path for the login
if ('default' == $applicationSettings['applicationThemeName']) {
    $applicationSettings['applicationThemeResourceLoginPath'] = '/resources/theme/webhemi';
} else {
    $applicationSettings['applicationThemeResourceLoginPath'] = '/resources/theme/'
        . $applicationSettings['applicationThemeName'];
}

// Read theme config
$themeConfig = json_decode(file_get_contents($themePath . '/theme.config.json'), true);
$config = ArrayUtils::merge($config, $themeConfig);

// Load specific application's config (Admin / Website routes)
$applicationConfigFile = $applicationSettings['applicationPath'] . '/config/application/' .
    ($applicationSettings['applicationModuleAdmin'] == $applicationSettings['applicationModule']
        ? $applicationSettings['applicationModuleAdmin']
        : $applicationSettings['applicationModuleWebsite']
    ) . '.php';
$config = ArrayUtils::merge($config, include $applicationConfigFile);

// fix template map paths
$themeTemplatePath = str_replace($applicationSettings['applicationPath'], 'wh_application', $themePath);
foreach ($config['templates']['map'] as $alias => $template) {
    $config['templates']['map'][$alias] = $themeTemplatePath . '/view/' . $template;
}

createDefinitions($applicationSettings);
// cleanup global variables
unset($serverData, $applicationSettings, $module, $modules, $domain, $subDomain, $url, $urlParts, $domainParts, $tld);
unset($tmp, $moduleName, $moduleData, $defaultThemePath, $themePath, $theme, $themeConfig, $applicationConfigFile);
unset($themeTemplatePath);

return new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
