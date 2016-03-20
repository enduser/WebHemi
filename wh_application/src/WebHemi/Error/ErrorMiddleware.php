<?php


namespace WebHemi\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Exception;

class ErrorMiddleware
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
     * @param Exception $error
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return HtmlResponse
     */
    public function __invoke(Exception $error, ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        echo 'Error<br>';

        var_dump($error);

        if (isset($_GET['auth'])) {
            return new HtmlResponse($this->templateRenderer->render('error::401'), 401);
        }

        return $next($request, $response);
    }
}
