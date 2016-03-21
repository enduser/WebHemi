<?php

namespace WebHemi\Acl;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;

class AclMiddleware
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * AclMiddleware constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return mixed
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        echo 'Acl<br>';

        if (isset($_GET['acl'])) {
            throw new \Exception('Forbidden', 403);
        }

        return $next($request, $response);
    }
}
