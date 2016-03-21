<?php

namespace WebHemiTest\Action;

use WebHemi\Action\HomePageAction;
use WebHemiTest\Fixtures;
use Zend\Diactoros\Response;
use Zend\Db\Adapter\Adapter;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Router\RouterInterface;
use Zend\Stdlib\ArrayUtils;

class HomePageActionTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouterInterface */
    protected $router;
    /** @var  Adapter */
    protected $adapter;

    use Fixtures\GetConfigTrait;

    protected function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);

        $config = $this->getConfig();

        $this->adapter = new Adapter((array)$config['db']);
    }

    public function testResponse()
    {
        $homePage = new HomePageAction($this->router->reveal(), null, $this->adapter);
        $response = $homePage(new ServerRequest(['/']), new Response(), function () {
        });

        $this->assertTrue($response instanceof Response);
    }
}
