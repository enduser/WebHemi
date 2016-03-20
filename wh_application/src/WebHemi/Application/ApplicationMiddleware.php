<?php


namespace WebHemi\Application;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use ArrayObject;

class ApplicationMiddleware
{
    /**
     * @var RouterInterface
     */
    private $config;

    /**
     * ApplicationMiddleware constructor.
     * @param ArrayObject $config
     */
    public function __construct(ArrayObject $config)
    {
        $this->config = $config;
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

        return $next($request, $response);
    }
}
