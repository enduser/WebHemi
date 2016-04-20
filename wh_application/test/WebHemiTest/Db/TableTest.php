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

use WebHemi\Acl\Resource\Table as AclResourceTable;
use WebHemi\Acl\Resource\Entity as AclResourceEntity;
use WebHemi\Acl\Role\Table as AclRoleTable;
use WebHemi\Acl\Role\Entity as AclRoleEntity;
use WebHemi\Acl\Rule\Table as AclRuleTable;
use WebHemi\Application\Table as ApplicationTable;
use WebHemi\Client\Lock\Table as ClientLockTable;
use WebHemi\User\Table as UserTable;
use WebHemi\User\Meta\Table as UserMetaTable;
use WebHemi\User\Role\Table as UserRoleTable;
use DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Adapter\Adapter as DbAdapter;

/**
 * Class TableTest
 * @package WebHemiTest\Db
 */
class TableTest extends TestCase
{
    /**
     * Check requirements - also checks SQLite availability
     */
    protected function checkRequirements()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('No SQLite Available');
        }

        return parent::checkRequirements();
    }

    /**
     * @covers \WebHemi\Acl\Resource\Table
     */
    public function testResourceTable()
    {
        $adapter = new DbAdapter([
            'driver' => 'Pdo_Sqlite',
            'database' => realpath(__DIR__ . '/../Fixtures/database.sqlite3')
        ]);

        $entity = new AclResourceEntity();

        $table = new AclResourceTable($adapter, $entity);
        $this->assertInstanceOf(AclResourceTable::class, $table);

        $result = $table->getResourceById(-1);
        $this->assertNull($result);

        $result = $table->getResourceById(1);
        $this->assertInstanceOf(AclResourceEntity::class, $result);
        $this->assertEquals('admin:index', $result->name);

        $result = $table->getResources();
        $this->assertTrue(count($result) > 0);
        $this->assertInternalType('array', $result);
        foreach ($result as $key => $value) {
            $this->assertInstanceOf(AclResourceEntity::class, $value);
            $this->assertEquals($key, $value->aclResourceId);
        }
    }

    /**
     * @covers \WebHemi\Acl\Role\Table
     */
    public function testRoleTable()
    {
        $adapter = new DbAdapter([
            'driver' => 'Pdo_Sqlite',
            'database' => realpath(__DIR__ . '/../Fixtures/database.sqlite3')
        ]);

        $entity = new AclRoleEntity();

        $table = new AclRoleTable($adapter, $entity);
        $this->assertInstanceOf(AclRoleTable::class, $table);

        $result = $table->getRoleById(-1);
        $this->assertNull($result);

        $result = $table->getRoleById(1);
        $this->assertInstanceOf(AclRoleEntity::class, $result);
        $this->assertEquals('admin', $result->name);

        $result = $table->getRoles();
        $this->assertTrue(count($result) > 0);
        $this->assertInternalType('array', $result);
        foreach ($result as $key => $value) {
            $this->assertInstanceOf(AclRoleEntity::class, $value);
            $this->assertEquals($key, $value->aclRoleId);
        }
    }
}
