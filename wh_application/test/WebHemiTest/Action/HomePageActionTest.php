<?php

namespace WebHemiTest\Action;

use WebHemi\Action\HomePageAction;
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

    protected function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);

        $config = include __DIR__ . '/../../../config/autoload/database.global.php';

        if (file_exists(__DIR__ . '/../../../config/autoload/database.local.php')) {
            $config = ArrayUtils::merge($config, include __DIR__ . '/../../../config/autoload/database.local.php');
        }

        $this->adapter = new Adapter($config['db']);
    }

    public function testResponse()
    {
        $homePage = new HomePageAction($this->router->reveal(), null, $this->adapter);
        $response = $homePage(new ServerRequest(['/']), new Response(), function () {
        });

        $this->assertTrue($response instanceof Response);
    }
}
