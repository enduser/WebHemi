<?php


namespace WebHemi\Application;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Zend\Stdlib\ArrayUtils;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;

class ApplicationMiddleware
{
    /**
     * @var ServiceManager
     */
    private $container;

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
        echo 'ApplicationMiddleware (wh)<br>';

        return $next($request, $response);
    }
}
