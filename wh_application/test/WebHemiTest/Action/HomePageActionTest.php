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
namespace WebHemiTest\Action;

use WebHemi\Action\HomePageAction;
use WebHemiTest\Fixtures;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Router\RouterInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\Config;

/**
 * Class HomePageActionTest
 * @package WebHemiTest\Action
 */
class HomePageActionTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouterInterface */
    protected $router;
    /** @var  AuthenticationService */
    protected $authService;
    /** @var  Config */
    protected $config;

    use Fixtures\GetConfigTrait;

    /**
     * Set up unit test
     */
    protected function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);
        $this->authService = $this->prophesize(AuthenticationService::class);
        $this->config = $this->getConfig();
    }

    /**
     * Test response
     */
    public function testResponse()
    {
        $homePage = new HomePageAction();

        $homePage->injectDependency('auth', $this->authService);
        $homePage->injectDependency('router', $this->router);
        $homePage->injectDependency('config', $this->config);

        $response = $homePage(new ServerRequest(['/']), new Response(), function () {
        });

        $this->assertTrue($response instanceof Response);
    }
}
