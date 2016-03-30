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
use Zend\Expressive\AppFactory;
use Zend\Expressive\Application as ZendApplication;
use Zend\Expressive\Router\RouterInterface;
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
    /** @var string  */
    private $applicationModuleAdmin = 'Admin';

    /** @var string  */
    private $applicationModuleWebsite = 'Website';

    /** @var string  */
    private $applicationModuleTypeSubdomain = 'subdomain';

    /** @var string  */
    private $applicationModuleTypeSubdirectory = 'subdir';

    /** @var string  */
    private $autologinCookiePrefix = 'atln';

    /** @var string  */
    private $sessionCookiePrefix = 'atsn';

    /** @var array  */
    private $serverData = [];

    /** @var ServiceManager */
    private $container;

    /** @var array  */
    private $applicationModuleList = [];

    /** @var string  */
    private $applicationPath = '';

    /** @var string  */
    private $applicationModule = 'Website';

    /** @var string  */
    private $applicationModuleType = 'subdomain';

    /** @var string  */
    private $applicationModuleUri = '/';

    /** @var string */
    private $mainDomain = null;

    /** @var string */
    private $applicationDomain = null;

    /** @var string */
    private $applicationThemeName;

    /** @var string */
    private $applicationThemeSystemPath;

    /** @var string */
    private $applicationThemeResourcePath;

    /** @var string */
    private $applicationThemeResourceLoginPath;

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
        try {
            /** @var ZendApplication $app */
            $app = $this->container->get('Zend\Expressive\Application');

            // When the application is in a sub-directory we add it's URL in the beginning of the middleware pipeline.
            if ($this->applicationModuleType == $this->applicationModuleTypeSubdirectory) {
                $subApp = $app;
                $app = AppFactory::create($this->container, $this->container->get(RouterInterface::class));
                $app->pipe('/' . $this->applicationModuleUri, $subApp);
            }

            $app->run($request, $response);
        } catch (\Exception $exp) {
//            // todo: render error page
            echo 'asd';
            var_dump($exp);
        }
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
        $this->setApplicationProperties($config['applications']);

        $defaultThemePath = $this->applicationPath . '/templates/default_theme';
        $themePath        = $defaultThemePath;
        $theme            = isset($config['applications'][$this->applicationModule])
            ? $config['applications'][$this->applicationModule]['theme']
            : 'default';

        // Update theme path or reset theme to default
        if ('default' != $theme && file_exists($this->applicationPath . '/templates/vendor_themes/' . $theme . '/theme.config.json')) {
            $themePath = $this->applicationPath . '/templates/vendor_themes/' . $theme;
        } else {
            $theme = 'default';
        }
        // Save the theme name for later use
        $this->applicationThemeName = $theme;

        // For Admin application we allow only the default theme. Login page can use custom CSS and JS only
        if ($this->applicationModuleAdmin == $this->applicationModule
            && $themePath !== $defaultThemePath
        ) {
            // Reset theme (except the name) to read default template
            $themePath = $defaultThemePath;
            $theme = 'default';
        }

        $this->applicationThemeSystemPath = $themePath;

        // Set resource path
        if ('default' == $theme) {
            $this->applicationThemeResourcePath = '/resources/theme/webhemi';
        } else {
            $this->applicationThemeResourcePath = '/resources/theme/' . $theme;
        }

        // Set resource path for the login
        if ('default' == $this->applicationThemeName) {
            $this->applicationThemeResourceLoginPath = '/resources/theme/webhemi';
        } else {
            $this->applicationThemeResourceLoginPath = '/resources/theme/' . $this->applicationThemeName;
        }

        // Read theme config
        $themeConfig = @json_decode(file_get_contents($themePath . '/theme.config.json'), true);
        $config = ArrayUtils::merge($config, $themeConfig);

        // Load specific application's config (Admin / Website routes)
        $applicationConfigFile = $this->applicationPath . '/config/application/' .
            ($this->applicationModuleAdmin == $this->applicationModule
                ? $this->applicationModuleAdmin
                : $this->applicationModuleWebsite
            ) . '.php';
        $config = ArrayUtils::merge($config, include $applicationConfigFile);

        // fix template map paths
        $themeTemplatePath = str_replace($this->applicationPath, 'wh_application', $themePath);
        foreach ($config['templates']['map'] as $alias => $template) {
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
                    && $moduleData['type'] == $this->applicationModuleTypeSubdirectory
                    && $moduleData['path'] == $subDir
                ) {
                    $module = $moduleName;
                    break;
                }
            } else {
                // subDomain-based modules
                if ($moduleData['type'] == $this->applicationModuleTypeSubdomain
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
            : ($module == $this->applicationModuleWebsite ? 'subdomain' : 'subdir');

        $this->applicationModuleUri = isset($modules[$module])
            ? $modules[$module]['path']
            : ($module == $this->applicationModuleWebsite ? 'www' : '/');
    }

    /**
     * Create global constants from class properties
     */
    private function setDefinitions()
    {
        define('APPLICATION_MODULE_ADMIN', $this->applicationModuleAdmin);
        define('APPLICATION_MODULE_WEBSITE', $this->applicationModuleWebsite);
        define('APPLICATION_MODULE_TYPE_SUBDOMAIN', $this->applicationModuleTypeSubdomain);
        define('APPLICATION_MODULE_TYPE_SUBDIR', $this->applicationModuleTypeSubdirectory);
        define('AUTOLOGIN_COOKIE_PREFIX', $this->autologinCookiePrefix);
        define('SESSION_COOKIE_PREFIX', $this->sessionCookiePrefix);
        define('APPLICATION_PATH', $this->applicationPath);
        define('APPLICATION_MODULE', $this->applicationModule);
        define('APPLICATION_MODULE_TYPE', $this->applicationModuleType);
        define('APPLICATION_MODULE_URI', $this->applicationModuleUri);
        define('MAIN_DOMAIN', $this->mainDomain);
        define('APPLICATION_DOMAIN', $this->applicationDomain);
        define('APPLICATION_THEME_NAME', $this->applicationThemeName);
        define('APPLICATION_THEME_SYSTEM_PATH', $this->applicationThemeSystemPath);
        define('APPLICATION_THEME_RESOURCE_PATH', $this->applicationThemeResourcePath);
        define('APPLICATION_THEME_RESOURCE_LOGIN_PATH', $this->applicationThemeResourceLoginPath);
    }
}
