<?php


namespace WebHemi\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

class AuthMiddleware
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * AuthMiddleware constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return mixed
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        echo 'Auth<br>';

        if (isset($_GET['auth'])) {
            throw new \Exception('Unauthorized', 401);
        }

        return $next($request, $response);
    }
}
