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

use WebHemi\Action\Website\IndexAction;
use WebHemi\User\Entity as UserEntity;
use WebHemi\User\Meta\Entity as UserMetaEntity;
use WebHemi\Acl\Role\Entity as AclRoleEntity;
use WebHemiTest\Fixtures;
use Zend\Authentication\AuthenticationService;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Router\RouterInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\Config;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class HomePageActionTest
 * @package WebHemiTest\Action
 */
class IndexActionTest extends TestCase
{
    /** @var RouterInterface */
    protected $router;
    /** @var  AuthenticationService */
    protected $authService;
    /** @var  UserEntity */
    protected $userEntity;
    /** @var  Config */
    protected $config;

    use Fixtures\GetConfigTrait;

    /**
     * Set up unit test
     */
    protected function setUp()
    {
        $this->config = $this->getConfig();

        //$aclRoleEntity = new AclRoleEntity();
        //$aclRoleEntity->exchangeArray([
        //    'id_acl_role' => 1,
        //    'name' => 'admin',
        //    'is_read_only' => 1,
        //    'description' => ''
        //]);

        $this->userEntity = $this->prophesize(UserEntity::class);
        //$this->userEntity->getMetaList()->willReturn([]);
        //$this->userEntity->getCurrentUserRole()->willReturn($aclRoleEntity);

        $this->router = $this->prophesize(RouterInterface::class);
        $this->authService = $this->prophesize(AuthenticationService::class);
        //$this->authService->hasIdentity()->willReturn(false);
        //$this->authService->getIdentity()->willReturn($this->userEntity);
    }

    /**
     * Test response
     */
    public function testResponse()
    {
        $homePage = new IndexAction();

        $homePage->injectDependency('auth', $this->authService->reveal());
        $homePage->injectDependency('router', $this->router);
        $homePage->injectDependency('config', $this->config);

        $response = $homePage(new ServerRequest(['/']), new Response(), function () {
        });

        $this->assertTrue($response instanceof Response);
    }
}
