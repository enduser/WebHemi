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

namespace WebHemiTest\Auth\Storage;

use WebHemi\Auth\Storage\Session as AuthStorageSession;
use WebHemiTest\Fixtures\PropertyClass;
use Zend\Session\Container as SessionContainer;
use WebHemi\User\Entity as UserEntity;
use WebHemi\User\Table as UserTable;
use Prophecy\Argument;
use PHPUnit_Framework_TestCase as TestCase;

define('SESSION_COOKIE_PREFIX', 'atsn');

/**
 * Class SessionTest
 * @package WebHemiTest\Auth\Storage
 */
class SessionTest extends TestCase
{
    /**
     * Test class constructor
     */
    public function testConstructor()
    {
        $sessionName = SESSION_COOKIE_PREFIX . '-' . bin2hex(AuthStorageSession::SESSION_SALT_DEFAULT);

        $this->assertNotEquals($sessionName, session_name());

        $storage = new AuthStorageSession();

        $this->assertAttributeInstanceOf(SessionContainer::class, 'session', $storage);
        $this->assertEquals($sessionName, session_name());
        $this->assertEquals('sha256', ini_get('session.hash_function'));
    }

    /**
     * Test session storage read when the identity is already resolved
     */
    public function testReadWithResolvedIdentity()
    {
        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => md5('just an hash we don\'t need now...'),
            'hash' => md5('just an hash we don\'t need now...'),
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        $storage = new AuthStorageSession();
        // we can use the dependency injector to manipulate private/protected properties too.
        $storage->injectDependency('resolvedIdentity', $entity);

        $result = $storage->read();

        $this->assertInstanceOf(UserEntity::class, $result);
        $this->assertAttributeInstanceOf(UserEntity::class, 'resolvedIdentity', $storage);
    }

    /**
     * Test session storage read when the session stores only an ID
     */
    public function testReadWhenIdentityIsAnId()
    {
        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => md5('just an hash we don\'t need now...'),
            'hash' => md5('just an hash we don\'t need now...'),
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        // use this test object to substitute the $this->session in the AuthStorageSession instance
        $fakeSessionClass = new PropertyClass();
        $fakeSessionClass->setAnyProperty(AuthStorageSession::MEMBER_DEFAULT, 1);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserById(Argument::type('int'))->willReturn($entity);

        $storage = new AuthStorageSession();
        $storage->injectDependency('userTable', $userTable->reveal());
        $storage->injectDependency('session', $fakeSessionClass);

        $result = $storage->read();

        $this->assertInstanceOf(UserEntity::class, $result);
        $this->assertAttributeInstanceOf(UserEntity::class, 'resolvedIdentity', $storage);
    }

    /**
     * Test session storage read when the session stores an UserEntity
     */
    public function testReadWhenIdentityIsInSession()
    {
        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => md5('just an hash we don\'t need now...'),
            'hash' => md5('just an hash we don\'t need now...'),
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        // use this test object to substitute the $this->session in the AuthStorageSession instance
        $fakeSessionClass = new PropertyClass();
        $fakeSessionClass->setAnyProperty(AuthStorageSession::MEMBER_DEFAULT, $entity);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserById(Argument::type('int'))->willReturn($entity);

        $storage = new AuthStorageSession();
        $storage->injectDependency('userTable', $userTable->reveal());
        $storage->injectDependency('session', $fakeSessionClass);

        $result = $storage->read();

        $this->assertInstanceOf(UserEntity::class, $result);
        $this->assertAttributeInstanceOf(UserEntity::class, 'resolvedIdentity', $storage);
    }

    /**
     * Test session storage read when the session stores an UserEntity
     */
    public function testReadWhenIdentityNotFound()
    {
        // use this test object to substitute the $this->session in the AuthStorageSession instance
        $fakeSessionClass = new PropertyClass();
        $fakeSessionClass->setAnyProperty(AuthStorageSession::MEMBER_DEFAULT, 101);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserById(Argument::type('int'))->willReturn(null);

        $storage = new AuthStorageSession();
        $storage->injectDependency('userTable', $userTable->reveal());
        $storage->injectDependency('session', $fakeSessionClass);

        $result = $storage->read();

        $this->assertNull($result);
        $this->assertAttributeEmpty('resolvedIdentity', $storage);
    }

    /**
     * Test save function
     */
    public function testWrite()
    {
        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => md5('just an hash we don\'t need now...'),
            'hash' => md5('just an hash we don\'t need now...'),
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        $fakeSessionClass = new PropertyClass();

        $storage = new AuthStorageSession();
        $storage->injectDependency('resolvedIdentity', $entity);
        $storage->injectDependency('session', $fakeSessionClass);

        $this->assertAttributeInstanceOf(UserEntity::class, 'resolvedIdentity', $storage);
        $this->assertFalse(isset($fakeSessionClass->{AuthStorageSession::MEMBER_DEFAULT}));
        $this->assertObjectNotHasAttribute(AuthStorageSession::MEMBER_DEFAULT, $storage);

        $storage->write($entity);

        $this->assertAttributeEmpty('resolvedIdentity', $storage);
        $this->assertTrue(isset($fakeSessionClass->{AuthStorageSession::MEMBER_DEFAULT}));
        $this->assertInstanceOf(UserEntity::class, $fakeSessionClass->{AuthStorageSession::MEMBER_DEFAULT});
    }

    /**
     * Test session clear
     */
    public function testClear()
    {
        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => md5('just an hash we don\'t need now...'),
            'hash' => md5('just an hash we don\'t need now...'),
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        $fakeSessionClass = new PropertyClass();
        $fakeSessionClass->setAnyProperty(AuthStorageSession::MEMBER_DEFAULT, $entity);

        $storage = new AuthStorageSession();
        $storage->injectDependency('resolvedIdentity', $entity);
        $storage->injectDependency('session', $fakeSessionClass);

        $this->assertAttributeInstanceOf(UserEntity::class, 'resolvedIdentity', $storage);
        $this->assertTrue(isset($fakeSessionClass->{AuthStorageSession::MEMBER_DEFAULT}));
        $this->assertInstanceOf(UserEntity::class, $fakeSessionClass->{AuthStorageSession::MEMBER_DEFAULT});

        $storage->clear();

        $this->assertAttributeEmpty('resolvedIdentity', $storage);
        $this->assertFalse(isset($fakeSessionClass->{AuthStorageSession::MEMBER_DEFAULT}));
    }
}
