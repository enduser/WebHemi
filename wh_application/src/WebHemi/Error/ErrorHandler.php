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

use Zend\Expressive\Template;
use Zend\Expressive\Template\TemplateRendererInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Zend\Stratigility\Http\Response as StratigilityResponse;
use Zend\Stratigility\Utils;

/**
 * Class ErrorHandler
 * @package WebHemi\Error
 */
class ErrorHandler
{
    /** @var TemplateRendererInterface */
    private $templateRenderer;

    /** @var string  */
    private $template404 = 'error/404';
    /** @var string  */
    private $template500 = 'error/500';

    /**
     * ErrorHandler constructor.
     * @param TemplateRendererInterface $templateRenderer
     */
    public function __construct(TemplateRendererInterface $templateRenderer = null)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * Final handler for an application.
     *
     * @param Request $request
     * @param Response $response
     * @param null|mixed $error
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $error = null)
    {
        if (!$error) {
            if ($response->getStatusCode() === 200
                && $response->getBody()->getSize() === 0
            ) {
                if ($this->templateRenderer) {
                    $error = new Exception('Not Found', 404);

                    $response->getBody()->write(
                        $this->templateRenderer->render(
                            $this->template404,
                            [
                                'layout' => 'layout::error',
                                'status' => 404,
                                'reason' => 'Not Found',
                                'error' => $error,
                                'uri' => $request->getUri()
                            ]
                        )
                    );
                }
                return $response->withStatus(404);
            }
        }

        if ($this->templateRenderer) {
            $error = new Exception('Internal Server Error', 500);

            $response->getBody()->write(
                $this->templateRenderer->render(
                    $this->template500,
                    [
                        'layout' => 'layout::error',
                        'status' => 500,
                        'reason' => 'Internal Server Error',
                        'error' => $error,
                        'uri' => $request->getUri(),
                        'request'  => $request,
                        'response' => $response,
                        'z' => 1
                    ]
                )
            );
        }

        return $response->withStatus(500);
    }
}
