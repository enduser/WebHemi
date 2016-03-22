<?php


namespace WebHemi\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Exception;

class ErrorMiddleware
{
    /**
     * @var Template\TemplateRendererInterface
     */
    private $templateRenderer;

    /**
     * AuthMiddleware constructor.
     *
     * @param Template\TemplateRendererInterface $templateRenderer
     */
    public function __construct(Template\TemplateRendererInterface $templateRenderer)
    {
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
        echo 'ErrorMiddleware<br>';

        switch ($error->getCode()) {
            case 401:
                $code = 401;
                $reason = 'Authentication required';
                break;

            case 403:
                $code = 403;
                $reason = 'Permission denied';
                break;

            case 404:
                $code = 404;
                $reason = 'Page not found';
                break;

            default:
                $code = 500;
                $reason = 'Application error';
        }

        return new HtmlResponse($this->templateRenderer->render('error::' . $code, ['status' => $code, 'reason' => $reason, 'error' => $error]), $code);
    }
}
