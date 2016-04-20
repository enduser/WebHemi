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
namespace WebHemiTest\Db;

use WebHemi\Acl\Resource\Entity as AclResourceEntity;
use WebHemi\Acl\Role\Entity as AclRoleEntity;
use WebHemi\Acl\Rule\Entity as AclRuleEntity;
use WebHemi\Application\Entity as ApplicationEntity;
use WebHemi\Client\Lock\Entity as ClientLockEntity;
use WebHemi\User\Entity as UserEntity;
use WebHemi\User\Meta\Entity as UserMetaEntity;
use WebHemi\User\Role\Entity as UserRoleEntity;
use DateTime;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class EntityTest
 * @package WebHemiTest\Db
 */
class EntityTest extends TestCase
{
    /**
     * @covers \WebHemi\Acl\Resource\Entity
     */
    public function testResourceEntity()
    {
        $data = [
            'id_acl_resource' => 10,
            'name' => 'test resource',
            'is_read_only' => 1,
            'description' => ''
        ];

        $this->assertClassNotHasAttribute('alcResourceId', AclResourceEntity::class);
        $this->assertClassNotHasAttribute('name', AclResourceEntity::class);
        $this->assertClassNotHasAttribute('isReadOnly', AclResourceEntity::class);
        $this->assertClassNotHasAttribute('description', AclResourceEntity::class);

        $entity = new AclResourceEntity();
        $result = $entity->exchangeArray($data);

        $this->assertInstanceOf(AclResourceEntity::class, $result);

        $this->assertEquals(10, $entity->aclResourceId);
        $this->assertEquals('test resource', $entity->name);
        $this->assertSame(true, $entity->isReadOnly);
        $this->assertAttributeEmpty('description', $entity);

        $arrayCopy = $entity->toArray();

        $this->assertArraysAreSimilar($data, $arrayCopy);
    }

    /**
     * @covers \WebHemi\Acl\Role\Entity
     */
    public function testRoleEntity()
    {
        $data = [
            'id_acl_role' => 11,
            'name' => 'test role',
            'is_read_only' => 0,
            'description' => ''
        ];

        $this->assertClassNotHasAttribute('alcRoleId', AclRoleEntity::class);
        $this->assertClassNotHasAttribute('name', AclRoleEntity::class);
        $this->assertClassNotHasAttribute('isReadOnly', AclRoleEntity::class);
        $this->assertClassNotHasAttribute('description', AclRoleEntity::class);

        $entity = new AclRoleEntity();
        $result = $entity->exchangeArray($data);

        $this->assertInstanceOf(AclRoleEntity::class, $result);

        $this->assertEquals(11, $entity->aclRoleId);
        $this->assertEquals('test role', $entity->name);
        $this->assertSame(false, $entity->isReadOnly);
        $this->assertAttributeEmpty('description', $entity);

        $arrayCopy = $entity->toArray();

        $this->assertArraysAreSimilar($data, $arrayCopy);
    }

    /**
     * @covers \WebHemi\Acl\Rule\Entity
     */
    public function testRuleEntity()
    {
        $data = [
            'id_acl_rule' => 5,
            'fk_acl_resource' => 10,
            'fk_acl_role' => 11,
            'is_allowed' => 1,
        ];

        $this->assertClassNotHasAttribute('alcRuleId', AclRuleEntity::class);
        $this->assertClassNotHasAttribute('alcResourceId', AclRuleEntity::class);
        $this->assertClassNotHasAttribute('alcRoleId', AclRuleEntity::class);
        $this->assertClassNotHasAttribute('isAllowed', AclRuleEntity::class);

        $entity = new AclRuleEntity();
        $result = $entity->exchangeArray($data);

        $this->assertInstanceOf(AclRuleEntity::class, $result);

        $this->assertEquals(5, $entity->aclRuleId);
        $this->assertEquals(10, $entity->aclResourceId);
        $this->assertEquals(11, $entity->aclRoleId);
        $this->assertSame(true, $entity->isAllowed);

        $arrayCopy = $entity->toArray();

        $this->assertArraysAreSimilar($data, $arrayCopy);
    }

    /**
     * @covers \WebHemi\Application\Entity
     */
    public function testApplicationEntity()
    {
        $data = [
            'id_application' => 12,
            'name' => 'test application',
            'is_read_only' => 0,
            'description' => '',
            'meta_data' => ''
        ];

        $this->assertClassNotHasAttribute('alcRoleId', ApplicationEntity::class);
        $this->assertClassNotHasAttribute('name', ApplicationEntity::class);
        $this->assertClassNotHasAttribute('isReadOnly', ApplicationEntity::class);
        $this->assertClassNotHasAttribute('description', ApplicationEntity::class);
        $this->assertClassNotHasAttribute('metaData', ApplicationEntity::class);

        $entity = new ApplicationEntity();
        $result = $entity->exchangeArray($data);

        $this->assertInstanceOf(ApplicationEntity::class, $result);

        $this->assertEquals(12, $entity->applicationId);
        $this->assertEquals('test application', $entity->name);
        $this->assertSame(false, $entity->isReadOnly);
        $this->assertAttributeEmpty('description', $entity);
        $this->assertAttributeEmpty('metaData', $entity);

        $arrayCopy = $entity->toArray();

        $this->assertArraysAreSimilar($data, $arrayCopy);
    }

    /**
     * @covers \WebHemi\Client\Lock\Entity
     */
    public function testClientLockEntity()
    {
        $time = time();

        $data = [
            'id_client_lock' => 13,
            'tryings' => '5',
            'time_lock' => date('Y-m-d H:i:s', $time),
        ];

        $this->assertClassNotHasAttribute('clientLockId', ClientLockEntity::class);
        $this->assertClassNotHasAttribute('clientIp', ClientLockEntity::class);
        $this->assertClassNotHasAttribute('tryings', ClientLockEntity::class);
        $this->assertClassNotHasAttribute('timeLock', ClientLockEntity::class);

        $entity = new ClientLockEntity();
        $result = $entity->exchangeArray($data);

        $this->assertInstanceOf(ClientLockEntity::class, $result);

        $this->assertEquals(13, $entity->clientLockId);
        $this->assertNull($entity->clientIp);
        $this->assertSame(5, $entity->tryings);
        $this->assertInstanceOf(DateTime::class, $entity->timeLock);
        $this->assertEquals($time, $entity->timeLock->getTimestamp());

        $arrayCopy = $entity->toArray();

        $this->assertArraysAreSimilar($data, $arrayCopy, false);
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
