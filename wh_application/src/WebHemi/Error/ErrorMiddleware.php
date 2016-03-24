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

namespace WebHemi\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Template;
use Exception;

/**
 * Class ErrorMiddleware
 * @package WebHemi\Error
 */
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
     * @param int|Exception $error
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return HtmlResponse
     */
    public function __invoke($error, ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // If we don't get an Exception, then probably the Response holds the error information
        if (!$error instanceof Exception) {
            $error = new Exception($response->getReasonPhrase(), $response->getStatusCode());
        }

        switch ($error->getCode()) {
            case 401:
                $template = 401;
                break;

            case 403:
                $template = 403;
                break;

            case 404:
                $template = 404;
                break;

            default:
                $template = 500;
        }

        return new HtmlResponse($this->templateRenderer->render('error::' . $template, ['status' => $error->getCode(), 'reason' => $error->getMessage(), 'error' => $error]), $error->getCode());
    }
}
