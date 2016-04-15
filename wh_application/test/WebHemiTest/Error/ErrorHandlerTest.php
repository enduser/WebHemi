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

use WebHemi\Error\ErrorHandler;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Exception;
use Prophecy\Argument;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ErrorHandlerTest
 * @package WebHemiTest\Error
 */
class ErrorHandlerTest extends TestCase
{
    /**
     * Test error handler constructor
     */
    public function testConstructor()
    {
        $renderer = $this->prophesize(TemplateRendererInterface::class);

        $errorHandler = new ErrorHandler($renderer->reveal());

        $this->assertAttributeInstanceOf(TemplateRendererInterface::class, 'templateRenderer', $errorHandler);
    }

    /**
     * Test error handler for error 404
     *
     * By default when there's no error raised, but the response has no body, it means no middlewares left in
     * the pipeline, so it must result a HTML page with 404 status code
     */
    public function testErrorHandlerForNotFoundError()
    {
        $responseHtml = '<html><body>Hello World!</body></html>';

        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $renderer->render(Argument::type('string'), Argument::type('array'))->willReturn($responseHtml);

        $errorHandler = new ErrorHandler($renderer->reveal());

        $request = new Request('foo.org', 'GET');
        $response = new HtmlResponse('', 200);
        $error = null;

        /** @var HtmlResponse $result */
        $result = $errorHandler($request, $response, $error);

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals(404, $result->getStatusCode());
        $this->assertEquals('Not Found', $result->getReasonPhrase());
        $this->assertEquals($responseHtml, $result->getBody());
    }

    /**
     * Test error handler for error 500
     *
     * When an error raised during the middleware pipelines and has not been caught by the ErrorMiddleware
     * it gets here and must result in a HTML page with 500 status code
     */
    public function testErrorHandlerForInternalServerError()
    {
        $responseHtml = '<html><body>Hello World!</body></html>';

        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $renderer->render(Argument::type('string'), Argument::type('array'))->willReturn($responseHtml);

        $errorHandler = new ErrorHandler($renderer->reveal());

        $request = new Request('foo.org', 'GET');
        $response = new HtmlResponse('', 200);
        $error = new Exception('Some Error');

        /** @var HtmlResponse $result */
        $result = $errorHandler($request, $response, $error);

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
        $this->assertEquals('Internal Server Error', $result->getReasonPhrase());
        $this->assertEquals($responseHtml, $result->getBody());
    }

    /**
     * Test error handler for error 500
     *
     * In some special cases there is a response and there is no error and the call chain still end up here.
     * In this case the result should be also an error 500 page
     */
    public function testErrorHandlerForInternalServerErrorWithResponseAndNoError()
    {
        $responseHtml = '<html><body>Hello World!</body></html>';

        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $renderer->render(Argument::type('string'), Argument::type('array'))->willReturn($responseHtml);

        $errorHandler = new ErrorHandler($renderer->reveal());

        $request = new Request('foo.org', 'GET');
        $response = new HtmlResponse($responseHtml, 200);
        $error = null;

        /** @var HtmlResponse $result */
        $result = $errorHandler($request, $response, $error);

        $this->assertInstanceOf(HtmlResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());
        $this->assertEquals('Internal Server Error', $result->getReasonPhrase());
        $this->assertEquals($responseHtml, $result->getBody());
    }
}
