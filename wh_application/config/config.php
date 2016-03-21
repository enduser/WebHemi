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
use WebHemi\Application\Application;

/**
 * Configuration files are loaded in a specific order. First ``global.php``, then ``*.global.php``.
 * then ``local.php`` and finally ``*.local.php``. This way local settings overwrite global settings.
 *
 * The configuration can be cached. This can be done by setting ``config_cache_enabled`` to ``true``.
 *
 * Obviously, if you use closures in your config you can't cache it.
 */

$cachedConfigFile = __DIR__ . '/../data/cache/app_config.php';

$config = [];

// Load cached config or read config from files and merge together.
if (is_file($cachedConfigFile)) {
    // Try to load the cached config
    $config = include $cachedConfigFile;
} else {
    // Load configuration from autoload path
    foreach (Glob::glob(__DIR__ . '/autoload/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE) as $file) {
        $config = ArrayUtils::merge($config, include $file);
    }

    // Cache config if enabled
    if (isset($config['config_cache_enabled']) && $config['config_cache_enabled'] === true) {
        file_put_contents($cachedConfigFile, '<?php return ' . var_export($config, true) . ';');
    }
}

// Init WebHemi Application to create system-wide constants
Application::setApplicationProperties($config['applications']);

// Load specific application's config (routes)
$applicationConfigFile = __DIR__ . '/application/' . Application::$APPLICATION_MODULE . '.php';

if (file_exists($applicationConfigFile)) {
    $config = ArrayUtils::merge($config, include $applicationConfigFile);
}

// Load specific application's theme (templates)
$theme = isset($config['applications'][Application::$APPLICATION_MODULE])
    ? $config['applications'][Application::$APPLICATION_MODULE]['theme']
    : 'default';
$themePath = __DIR__ . '/../templates/default_theme';

if ('default' != $theme && file_exists(__DIR__ . '/../templates/vendor_themes/' . $theme . '/theme.config.json')) {
    $themePath = __DIR__ . '/../templates/vendor_themes/' . $theme;
}

$themeTemplatePath = str_replace(__DIR__ . '/..', 'wh_application', $themePath);

$themeConfig = json_decode(file_get_contents($themePath . '/theme.config.json'), true);

// fix template map paths
foreach ($themeConfig['templates']['map'] as $alias => $template) {
    $themeConfig['templates']['map'][$alias] = $themeTemplatePath . '/view/' . $template;
}

$config = ArrayUtils::merge($config, $themeConfig);
var_dump(Application::$APPLICATION_MODULE);
// Return an ArrayObject so we can inject the config as a service in Aura.Di
// and still use array checks like ``is_array``.
return new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
