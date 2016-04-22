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

namespace WebHemiTest\Auth;

use WebHemi\Auth\Adapter as AuthAdapter;
use Zend\Authentication\Result as AuthResult;
use WebHemi\Client\Lock\Table as ClientLockTable;
use WebHemi\User\Table as UserTable;
use WebHemi\User\Entity as UserEntity;
use Prophecy\Argument;
use PHPUnit_Framework_TestCase as TestCase;

require_once __DIR__ . '/../../../config/functions.php';

/**
 * Class AdapterTest
 * @package WebHemiTest\Auth
 */
class AdapterTest extends TestCase
{
    /**
     * Test simple setters
     */
    public function testSimpleAdapterMethods()
    {
        $authAdapter = new AuthAdapter();

        $this->assertInstanceOf(AuthAdapter::class, $authAdapter);
        $this->assertAttributeInternalType('array', 'serverData', $authAdapter);

        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => $this->encodePassword('testPassword'),
            'hash' => md5('just an md5 hash we don\'t need now...'),
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $this->assertNull($authAdapter->identity);

        $authAdapter->setIdentity($data['username']);
        $this->assertEquals($data['username'], $authAdapter->identity);

        $this->assertAttributeEmpty('credential', $authAdapter);
        $authAdapter->setCredential($data['password']);
        $this->assertAttributeSame($data['password'], 'credential', $authAdapter);

        $entity = new UserEntity();
        $entity->exchangeArray($data);
        $this->assertAttributeEmpty('verifiedUser', $authAdapter);
        $authAdapter->setVerifiedUser($entity);
        $this->assertAttributeInstanceOf(UserEntity::class, 'verifiedUser', $authAdapter);
    }

    /**
     * Test authentication when the a verified user is given
     */
    public function testAuthenticateWithVerifiedUser()
    {
        $authAdapter = new AuthAdapter();

        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => $this->encodePassword('testPassword'),
            'hash' => null,
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);
        $authAdapter->setVerifiedUser($entity);

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->setLock()->willReturn(1);
        $clientLockTable->releaseLock()->willReturn(1);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->update(Argument::type('array'))->willReturn(1);

        $authAdapter->injectDependency('clientLockTable', $clientLockTable->reveal());
        $authAdapter->injectDependency('userTable', $userTable->reveal());

        $authResult = $authAdapter->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::SUCCESS, $authResult->getCode());
        $this->assertInstanceOf(UserEntity::class, $authResult->getIdentity());
        $this->assertArraysAreSimilar([$authAdapter::RESULT_SUCCESS], $authResult->getMessages());

        /** @var UserEntity $resultEntity */
        $resultEntity = $authResult->getIdentity();
        $expectedHash = md5($data['username'] . '-' . $data['email']);

        $this->assertEquals($expectedHash, $resultEntity->hash);
    }

    /**
     * Test authentication when no identification is given
     */
    public function testAuthenticateWhenIdentityIsNotGiven()
    {
        $authAdapter = new AuthAdapter();

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->setLock()->willReturn(1);
        $clientLockTable->releaseLock()->willReturn(1);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserByName(Argument::type('null'))->willReturn(null);

        $authAdapter->injectDependency('clientLockTable', $clientLockTable->reveal());
        $authAdapter->injectDependency('userTable', $userTable->reveal());

        $authResult = $authAdapter->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::FAILURE_IDENTITY_NOT_FOUND, $authResult->getCode());
        $this->assertEmpty($authResult->getIdentity());
        $this->assertArraysAreSimilar([$authAdapter::RESULT_WRONG_IDENTITY], $authResult->getMessages());
    }

    /**
     * Test authentication when user is not found by name
     */
    public function testAuthenticateWhenIdentityByNameIsNotFound()
    {
        $authAdapter = new AuthAdapter();

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->setLock()->willReturn(1);
        $clientLockTable->releaseLock()->willReturn(1);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserByName(Argument::type('string'))->willReturn(null);

        $authAdapter->injectDependency('clientLockTable', $clientLockTable->reveal());
        $authAdapter->injectDependency('userTable', $userTable->reveal());

        $authAdapter->setIdentity('someIdentity');

        $authResult = $authAdapter->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::FAILURE_IDENTITY_NOT_FOUND, $authResult->getCode());
        $this->assertEquals('someIdentity', $authResult->getIdentity());
        $this->assertArraysAreSimilar([$authAdapter::RESULT_WRONG_IDENTITY], $authResult->getMessages());
    }

    /**
     * Test authentication when user is not found by email
     */
    public function testAuthenticateWhenIdentityByEmailIsNotFound()
    {
        $authAdapter = new AuthAdapter();

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->setLock()->willReturn(1);
        $clientLockTable->releaseLock()->willReturn(1);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserByEmail(Argument::type('string'))->willReturn(null);

        $authAdapter->injectDependency('clientLockTable', $clientLockTable->reveal());
        $authAdapter->injectDependency('userTable', $userTable->reveal());

        $authAdapter->setIdentity('some.email@foo.org');

        $authResult = $authAdapter->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::FAILURE_IDENTITY_NOT_FOUND, $authResult->getCode());
        $this->assertEquals('some.email@foo.org', $authResult->getIdentity());
        $this->assertArraysAreSimilar([$authAdapter::RESULT_WRONG_IDENTITY], $authResult->getMessages());
    }

    /**
     * Test authentication when user is not activated
     */
    public function testAuthenticateWhenUserIsNotActivated()
    {
        $authAdapter = new AuthAdapter();

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->setLock()->willReturn(1);
        $clientLockTable->releaseLock()->willReturn(1);

        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => $this->encodePassword('testPassword'),
            'hash' => null,
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => false,
            'is_enabled' => false,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserByName(Argument::type('string'))->willReturn($entity);

        $authAdapter->injectDependency('clientLockTable', $clientLockTable->reveal());
        $authAdapter->injectDependency('userTable', $userTable->reveal());

        $authAdapter->setIdentity($data['username']);

        $authResult = $authAdapter->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::FAILURE_UNCATEGORIZED, $authResult->getCode());
        $this->assertEquals($data['username'], $authResult->getIdentity());
        $this->assertArraysAreSimilar([$authAdapter::RESULT_NOT_AVAILABLE_IDENTITY], $authResult->getMessages());
    }

    /**
     * Test authentication when user is disabled
     */
    public function testAuthenticateWhenUserIsDisabled()
    {
        $authAdapter = new AuthAdapter();

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->setLock()->willReturn(1);
        $clientLockTable->releaseLock()->willReturn(1);

        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => $this->encodePassword('testPassword'),
            'hash' => null,
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => false,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserByEmail(Argument::type('string'))->willReturn($entity);

        $authAdapter->injectDependency('clientLockTable', $clientLockTable->reveal());
        $authAdapter->injectDependency('userTable', $userTable->reveal());

        $authAdapter->setIdentity($data['email']);

        $authResult = $authAdapter->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::FAILURE_UNCATEGORIZED, $authResult->getCode());
        $this->assertEquals($data['email'], $authResult->getIdentity());
        $this->assertArraysAreSimilar([$authAdapter::RESULT_NOT_AVAILABLE_IDENTITY], $authResult->getMessages());
    }

    /**
     * Test authentication when credential is wrong
     */
    public function testAuthenticateWhenCredentialIsWrong()
    {
        $authAdapter = new AuthAdapter();

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->setLock()->willReturn(1);
        $clientLockTable->releaseLock()->willReturn(1);

        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => $this->encodePassword('testPassword'),
            'hash' => null,
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->getUserByEmail(Argument::type('string'))->willReturn($entity);

        $authAdapter->injectDependency('clientLockTable', $clientLockTable->reveal());
        $authAdapter->injectDependency('userTable', $userTable->reveal());

        $authAdapter->setIdentity($data['email']);
        $authAdapter->setCredential('wrongPassword');

        $authResult = $authAdapter->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::FAILURE_CREDENTIAL_INVALID, $authResult->getCode());
        $this->assertEquals($data['email'], $authResult->getIdentity());
        $this->assertArraysAreSimilar([$authAdapter::RESULT_WRONG_CREDENTIAL], $authResult->getMessages());
    }

    /**
     * Test authentication when username and password is correct
     */
    public function testAuthenticateWhenUsernameAndPasswordIsCorrect()
    {
        $authAdapter = new AuthAdapter();

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->setLock()->willReturn(1);
        $clientLockTable->releaseLock()->willReturn(1);

        $data = [
            'id_user' => 1,
            'username' => 'testUser',
            'email' => 'test.address@foo.org',
            'password' => $this->encodePassword('testPassword'),
            'hash' => null,
            'last_ip' => '127.0.0.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new UserEntity();
        $entity->exchangeArray($data);

        $userTable = $this->prophesize(UserTable::class);
        $userTable->update(Argument::type('array'))->willReturn(1);
        $userTable->getUserByName(Argument::type('string'))->willReturn($entity);

        $authAdapter->injectDependency('clientLockTable', $clientLockTable->reveal());
        $authAdapter->injectDependency('userTable', $userTable->reveal());

        $authAdapter->setIdentity($data['username']);
        $authAdapter->setCredential('testPassword');

        $authResult = $authAdapter->authenticate();

        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertEquals(AuthResult::SUCCESS, $authResult->getCode());
        $this->assertInstanceOf(UserEntity::class, $authResult->getIdentity());
        $this->assertArraysAreSimilar([$authAdapter::RESULT_SUCCESS], $authResult->getMessages());

        /** @var UserEntity $resultEntity */
        $resultEntity = $authResult->getIdentity();
        $expectedHash = md5($data['username'] . '-' . $data['email']);

        $this->assertEquals($expectedHash, $resultEntity->hash);
    }

    /**
     * The same process as the WebHemi uses for password hash
     *
     * @param $password
     * @return mixed
     */
    protected function encodePassword($password)
    {
        return password_hash($password, AuthAdapter::PASSWORD_ALGORITHM, ['cost' => AuthAdapter::PASSWORD_COST]);
    }

    /**
     * @param array $a
     * @param array $b
     * @param bool  $typeIdentical
     * @return bool
     */
    protected function assertArraysAreSimilar(array $a, array $b, $typeIdentical = true)
    {
        $result = true;

        // if the second array misses indexes that the first has: false
        if (count(array_diff_assoc($a, $b))) {
            $result = false;
        }
        // we know that the indexes, but maybe not values, match.
        // compare the values between the two arrays
        foreach ($a as $k => $v) {
            if ($typeIdentical && $v !== $b[$k]) {
                $result = false;
            } elseif (!$typeIdentical && $v != $b[$k]) {
                $result = false;
            }
        }

        $this->assertTrue($result);
    }
}
