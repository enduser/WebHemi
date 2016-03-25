<?php

namespace WebHemiTest\Action;

use WebHemi\Action\HomePageAction;
use WebHemiTest\Fixtures;
use WebHemi\User\Table as UserTable;
use WebHemi\Auth\AuthenticationService;
use Zend\Diactoros\Response;
use Zend\Db\Adapter\Adapter;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Router\RouterInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageActionTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouterInterface */
    protected $router;
    /** @var  UserTable */
    protected $userTable;
    /** @var  ZendViewRenderer */
    protected $template;
    /** @var  AuthenticationService */
    protected $authService;

    use Fixtures\GetConfigTrait;

    protected function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);
        $this->authService = $this->prophesize(AuthenticationService::class);
        $config = $this->getConfig();

        $adapter = new Adapter((array)$config['db']);
        $this->userTable = new UserTable($adapter);
    }

    public function testResponse()
    {
        $homePage = new HomePageAction(
            $this->router->reveal(),
            $this->authService->reveal(),
            $this->userTable,
            null
        );
        $response = $homePage(new ServerRequest(['/']), new Response(), function () {
        });

        $this->assertTrue($response instanceof Response);
    }
}
