<?php


namespace WebHemi\Acl;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;

class AclMiddleware
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
     * AclMiddleware constructor.
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
        echo 'Acl<br>';
        
        if (isset($_GET['acl'])) {
            return new HtmlResponse($this->templateRenderer->render('error::403'), 403);
        }
        
        return $next($request, $response);
    }
}
