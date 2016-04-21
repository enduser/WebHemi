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

namespace WebHemiTest\Error;

use WebHemi\Error\Middleware;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Prophecy\Argument;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Exception;

/**
 * Class MiddlewareTest
 * @package WebHemiTest\Error
 */
class MiddlewareTest extends TestCase
{
    /** @var  string */
    protected $responseHtml;
    /** @var  ObjectProphecy  */
    protected $renderer;
    /** @var array */
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
     * set up unit test
     */
    public function setUp()
    {
        $this->responseHtml = '<html><body>Hello World!</body></html>';

        $this->renderer = $this->prophesize(TemplateRendererInterface::class);
        $this->renderer->render(Argument::type('string'), Argument::type('array'))->willReturn($this->responseHtml);
    }

    /**
     * Error code data provider
     *
     * @return array
     */
    public function errorCodeProvider()
    {
        return [
            [400, '500'],
            [401, '401'],
            [403, '403'],
            [404, '404'],
            [405, '500'],
            [410, '500'],
            [500, '500'],
            [502, '500'],
        ];
    }

    /**
     * @param int    $code
     * @param string $template
     *
     * @dataProvider errorCodeProvider
     */
    public function testMiddlewareWithErrorResponse($code, $template)
    {
        $middleware = new Middleware();
        $middleware->injectDependency('templateRenderer', $this->renderer->reveal());

        $this->assertAttributeInstanceOf(TemplateRendererInterface::class, 'templateRenderer', $middleware);

        $request = new ServerRequest();
        $response = new HtmlResponse($this->responseHtml, $code);
        $error = $code;
        $callable = function() {
            return true;
        };

        /** @var HtmlResponse $result */
        $result = $middleware($error, $request, $response, $callable);

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertAttributeEquals($code, 'code', $middleware);
        $this->assertAttributeEquals($template, 'template', $middleware);
        $this->assertEquals($code, $result->getStatusCode());
        $this->assertEquals($this->phrases[$code], $result->getReasonPhrase());
        $this->assertEquals($this->responseHtml, $result->getBody());
    }

    /**
     * @param int $code
     *
     * @dataProvider errorCodeProvider
     */
    public function testMiddlewareWithException($code, $template)
    {
        $middleware = new Middleware();
        $middleware->injectDependency('templateRenderer', $this->renderer->reveal());

        $this->assertAttributeInstanceOf(TemplateRendererInterface::class, 'templateRenderer', $middleware);

        $request = new ServerRequest();
        $response = new HtmlResponse($this->responseHtml, $code);
        $error = new Exception($this->phrases[$code], $code);
        $callable = function() {
            return true;
        };

        /** @var HtmlResponse $result */
        $result = $middleware($error, $request, $response, $callable);

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertAttributeEquals($code, 'code', $middleware);
        $this->assertAttributeEquals($template, 'template', $middleware);
        $this->assertEquals($code, $result->getStatusCode());
        $this->assertEquals($this->phrases[$code], $result->getReasonPhrase());
        $this->assertEquals($this->responseHtml, $result->getBody());
    }
}
