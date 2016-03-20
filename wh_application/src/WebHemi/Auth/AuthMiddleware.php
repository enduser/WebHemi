<?php


namespace WebHemi\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

class AuthMiddleware
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Template\TemplateRendererInterface
     */
    private $templateRenderer;

    /**
     * AuthMiddleware constructor.
     * @param RouterInterface $router
     * @param Template\TemplateRendererInterface $templateRenderer
     */
    public function __construct(RouterInterface $router, Template\TemplateRendererInterface $templateRenderer)
    {
        $this->router = $router;
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        echo 'Auth<br>';

        if (isset($_GET['auth'])) {
            return new HtmlResponse($this->templateRenderer->render('error::401'), 401);
        }

        return $next($request, $response);
    }
}
