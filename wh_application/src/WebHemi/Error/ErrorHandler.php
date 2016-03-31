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
     * Map of standard HTTP status code/reason phrases
     *
     * @var array
     */
    private $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /**
     * ErrorHandler constructor.
     * @param TemplateRendererInterface $templateRenderer
     */
    public function __construct(TemplateRendererInterface $templateRenderer)
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
