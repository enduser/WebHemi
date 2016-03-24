<?php
/**
 *
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
 *
 */

namespace WebHemi;

use ArrayObject;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;
use Zend\Expressive\Application as ZendApplication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Application
 * @package WebHemi\Application
 */
final class Application
{
    const APPLICATION_MODULE_ADMIN = 'Admin';

    const APPLICATION_MODULE_WEBSITE = 'Website';

    const APPLICATION_MODULE_TYPE_SUBDOMAIN = 'subdomain';

    const APPLICATION_MODULE_TYPE_SUBDIR = 'subdir';

    const AUTOLOGIN_COOKIE_PREFIX = 'atln';

    const SESSION_COOKIE_PREFIX = 'atsn';

    /** @var array  */
    private $serverData = [];

    /** @var ServiceManager */
    private $container;

    /** @var array  */
    private $applicationModuleList = [
        self::APPLICATION_MODULE_ADMIN,
        self::APPLICATION_MODULE_WEBSITE
    ];

    /** @var string  */
    public $applicationPath = '';

    /** @var string  */
    public $applicationModule = self::APPLICATION_MODULE_WEBSITE;

    /** @var string  */
    public $applicationModuleType = self::APPLICATION_MODULE_TYPE_SUBDOMAIN;

    /** @var string  */
    public $applicationModuleUri = '/';

    /** @var string */
    public $mainDomain = null;

    /** @var string */
    public $applicationDomain = null;

    /** @var array */
    public $applicationThemeName;

    /** @var array */
    public $applicationThemeSystemPath;

    /** @var array */
    public $applicationThemeResourcePath;

    /** @var  ArrayObject */
    private $config;

    /**
     * Application constructor.
     * @param ServiceManager|null $container
     */
    public function __construct(ServiceManager $container = null)
    {
        if (empty($container)) {
            $container = new ServiceManager();
        }

        // Avoid access to super global
        $this->serverData = filter_input_array(INPUT_SERVER);

        // Set application path
        $this->applicationPath = realpath(__DIR__ . '/../../');

        // Initial
        $this->initConfig();

        // Set global constants used by WebHemi
        $this->setDefinitions();

        // Build container
        $this->container = $container;
        (new Config($this->config['dependencies']))->configureServiceManager($this->container);

        // Inject config
        $container->setService('config', $this->config);

        // Inject WebHemi
        $container->setService('WebHemi', $this);
    }

    /**
     * Run the application
     *
     * If no request or response are provided, the method will use
     * ServerRequestFactory::fromGlobals to create a request instance, and
     * instantiate a default response instance.
     *
     * It then will invoke itself with the request and response, and emit
     * the returned response using the composed emitter.
     *
     * @param null|ServerRequestInterface $request
     * @param null|ResponseInterface $response
     */
    public function run(ServerRequestInterface $request = null, ResponseInterface $response = null)
    {
        /** @var ZendApplication $app */
        $app = $this->container->get('Zend\Expressive\Application');
        $app->run($request, $response);
    }

    /**
     * Get application container
     *
     * @return ServiceManager
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get module list
     *
     * @return array
     */
    public function getModuleList()
    {
        return $this->applicationModuleList;
    }

    /**
     * Initializes config and set application properties
     */
    private function initConfig()
    {
        $config = [];

        // Load configuration from autoload path
        foreach (Glob::glob($this->applicationPath . '/config/autoload/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE) as $file) {
            $config = ArrayUtils::merge($config, include $file);
        }

        // Let system-wide constants
        static::setApplicationProperties($config['applications']);

        // Load specific application's config (Admin / Website routes)
        $applicationConfigFile = $this->applicationPath . '/config/application/' .
            (static::APPLICATION_MODULE_ADMIN == $this->applicationModule
                ? static::APPLICATION_MODULE_ADMIN
                : static::APPLICATION_MODULE_WEBSITE
            ) . '.php';
        $config = ArrayUtils::merge($config, include $applicationConfigFile);

        // Load specific application's theme (templates)
        $theme = isset($config['applications'][$this->applicationModule])
            ? $config['applications'][$this->applicationModule]['theme']
            : 'default';
        $defaultThemePath = $this->applicationPath . '/templates/default_theme';
        $themePath = $defaultThemePath;

        if ('default' != $theme && file_exists($this->applicationPath . '/templates/vendor_themes/' . $theme . '/theme.config.json')) {
            $themePath = $this->applicationPath . '/templates/vendor_themes/' . $theme;
        } else {
            $theme = 'default';
        }

        $this->applicationThemeName = $theme;

        // For Admin application we allow only the default theme. Login page can use custom CSS and JS only
        if (static::APPLICATION_MODULE_ADMIN == $this->applicationModule
            && $themePath !== $defaultThemePath
        ) {
            // Reset theme (except the name) to read default template
            $themePath = $defaultThemePath;
            $theme = 'default';
        }

        $this->applicationThemeSystemPath = $themePath;
        if ('default' == $theme) {
            $resourcePath = '/resources/theme/webhemi';
        } else {
            $resourcePath = '/resources/theme/' . $theme;
        }
        $this->applicationThemeResourcePath = $resourcePath;

        // Read theme config
        $themeConfig = json_decode(file_get_contents($themePath . '/theme.config.json'), true);
        $config = ArrayUtils::merge($config, $themeConfig);

        // fix template map paths
        $themeTemplatePath = str_replace($this->applicationPath, 'wh_application', $themePath);
        foreach ($config['templates']['map'] as $alias => $template) {
            // perform corrections for Admin application
            if (static::APPLICATION_MODULE_ADMIN == $this->applicationModule
                && 'layout/layout' == $alias
            ) {
                $template = 'layout/admin.phtml';
            }

            $config['templates']['map'][$alias] = $themeTemplatePath . '/view/' . $template;
        }

        $this->config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
    }


    /**
     * Sets application properties
     *
     * @param array $modules
     */
    private function setApplicationProperties(array $modules = [])
    {
        // Define Application list
        if (!empty($modules)) {
            $this->applicationModuleList = json_encode(array_keys($modules));
        }

        $domain = $this->serverData['SERVER_NAME'];

        // set a default module
        $module     = $this->applicationModule;
        $subDomain  = '';

        // if no URL is present, then the current URL will be used
        $url = 'http' . ((isset($this->serverData['HTTPS']) && $this->serverData['HTTPS']) ? 's' : '') . '://';
        $url .= $this->serverData['HTTP_HOST'] . $this->serverData['REQUEST_URI'] . $this->serverData['QUERY_STRING'];

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
        $this->mainDomain = $domain;
        $this->applicationDomain = $subDomain . '.' . $this->mainDomain;

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

        $this->applicationModule = $module;

        $this->applicationModuleType = isset($modules[$module])
            ? $modules[$module]['type']
            : ($module == static::APPLICATION_MODULE_WEBSITE ? 'subdomain' : 'subdir');

        $this->applicationModuleUri = isset($modules[$module])
            ? $modules[$module]['path']
            : ($module == static::APPLICATION_MODULE_WEBSITE ? 'www' : '/');
    }

    /**
     * Create global constants from class properties
     */
    private function setDefinitions()
    {
        define('APPLICATION_MODULE_ADMIN', static::APPLICATION_MODULE_ADMIN);
        define('APPLICATION_MODULE_WEBSITE', static::APPLICATION_MODULE_WEBSITE);
        define('APPLICATION_MODULE_TYPE_SUBDOMAIN', static::APPLICATION_MODULE_TYPE_SUBDOMAIN);
        define('APPLICATION_MODULE_TYPE_SUBDIR', static::APPLICATION_MODULE_TYPE_SUBDIR);
        define('AUTOLOGIN_COOKIE_PREFIX', static::AUTOLOGIN_COOKIE_PREFIX);
        define('SESSION_COOKIE_PREFIX', static::SESSION_COOKIE_PREFIX);
        define('APPLICATION_PATH', $this->applicationPath);
        define('APPLICATION_MODULE', $this->applicationModule);
        define('APPLICATION_MODULE_TYPE', $this->applicationModuleType);
        define('APPLICATION_MODULE_URI', $this->applicationModuleUri);
        define('MAIN_DOMAIN', $this->mainDomain);
        define('APPLICATION_DOMAIN', $this->applicationDomain);
        define('APPLICATION_THEME_NAME', $this->applicationThemeName);
        define('APPLICATION_THEME_SYSTEM_PATH', $this->applicationThemeSystemPath);
        define('APPLICATION_THEME_RESOURCE_PATH', $this->applicationThemeResourcePath);
    }
}
