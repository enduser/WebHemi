<?php


namespace WebHemi\Application;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use ArrayObject;
use Zend\Stdlib\ArrayUtils;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\ZendView\ZendViewRenderer;

class ApplicationMiddleware
{
    /**
     * @var ServiceManager
     */
    private $container;

    /**
     * @var string
     */
    private $themePath = 'default_theme';

    /**
     * ApplicationMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        echo 'Pre-route Config<br>';

        //$this->setThemeConfig();

        return $next($request, $response);
    }

    protected function setThemeConfig()
    {
        // get chosen theme config
        $configFile = __DIR__ . '/../../../templates/' . $this->themePath . '/theme.config.json';
        $templateConfig = json_decode(file_get_contents($configFile), true);

        // fix theme path
        foreach ($templateConfig['templates']['map'] as $alias => $path) {
            $templateConfig['templates']['map'][$alias] = 'wh_application/templates/' . $this->themePath . '/' . $path;
        }

        // Get already loaded config
        $applicationConfig = (array)$this->container->get('config');

        // Merge and set new config
        $applicationConfig = ArrayUtils::merge($applicationConfig, $templateConfig);
        $config = new ArrayObject($applicationConfig, ArrayObject::ARRAY_AS_PROPS);
        $this->container
            ->setAllowOverride(true)
            ->setService('config', $config)
            ->setAllowOverride(false);

        // get template resolver
        /** @var ZendViewRenderer $renderer */
//        $renderer = $this->container->get(TemplateRendererInterface::class);
    }
}
