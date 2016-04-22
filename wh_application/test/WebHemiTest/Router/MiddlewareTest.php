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

namespace WebHemiTest\Router;

use WebHemi\Router\Middleware as RouterMiddleware;
use WebHemi\Router\Exception as RouterException;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\ZendRouter;
use Zend\Expressive\Router\RouteResult;
use Prophecy\Argument;
use PHPUnit_Framework_TestCase as TestCase;
use WebHemiTest\Fixtures\PropertyClass;

/**
 * Class MiddlewareTest
 * @package WebHemiTest\Router
 */
class MiddlewareTest extends TestCase
{
    /** @var  ZendRouter */
    protected $router;

    /**
     * Error code data provider
     *
     * @return array
     */
    public function routeResultProvider()
    {
        return [
            ['/ok', true],
            ['/bad', false],
            ['/good/path', true],
            ['/wrong/route', false],
            ['/this/is/right', true],
            ['/not/proper', false],
            ['/access/granted', true],
            ['/access/denied', false],
        ];
    }

    /**
     * @param $url
     * @param $expectedResult
     *
     * @dataProvider routeResultProvider
     */
    public function testRouter($url, $expectedResult)
    {
        if (!$expectedResult) {
            $this->setExpectedException(RouterException::class);
        }

        $callable = new PropertyClass();
        $request = new ServerRequest([$url]);
        $response = new HtmlResponse('All good');

        $routeResult = $this->prophesize(RouteResult::class);
        $routeResult->isFailure()->willReturn(!$expectedResult);

        $router = $this->prophesize(ZendRouter::class);
        $router->match($request)->willReturn($routeResult->reveal());


        $middleware = new RouterMiddleware();
        $middleware->injectDependency('router', $router->reveal());

        $result = $middleware($request, $response, $callable);

        $this->assertEquals(':)', $result);
    }
}
