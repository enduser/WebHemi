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
                $code = 401;
                $template = 401;
                break;

            case 403:
                $code = 403;
                $template = 403;
                break;

            case 404:
                $code = 404;
                $template = 404;
                break;

            default:
                $code = array_key_exists($error->getCode(), $this->phrases) ? $error->getCode() : 500;
                $template = 500;
        }

        return new HtmlResponse(
            $this->templateRenderer->render(
                'error::' . $template,
                ['layout' => 'layout::error', 'status' => $code, 'reason' => $this->phrases[$code], 'error' => $error]
            ),
            $code
        );
    }
}
