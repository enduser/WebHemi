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

namespace WebHemi\User;

use DateTime;
use WebHemi\Db\AbstractEntity;
use WebHemi\User\Meta\Table as UserMetaTable;
use WebHemi\User\Role\Entity as UserRoleEntity;
use WebHemi\User\Role\Table as UserRoleTable;
use WebHemi\Acl\Role\Entity as AclRoleEntity;
use WebHemi\Acl\Role\Table as AclRoleTable;
use WebHemi\Application\Entity as ApplicationEntity;
use WebHemi\Application\Table as ApplicationTable;

/**
 * Class Entity
 * @package WebHemi\User
 *
 * @property int $userId
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $hash
 * @property string $lastIp
 * @property string $registerIp
 * @property bool $isActive
 * @property bool $isEnabled
 * @property DateTime $timeLogin
 * @property DateTime $timeRegister
 */
class Entity extends AbstractEntity
{
    /** @var  UserMetaTable */
    protected $userMetaTable;
    /** @var  UserRoleTable */
    protected $userRoleTable;
    /** @var  ApplicationTable */
    protected $applicationTable;
    /** @var  AclRoleTable */
    protected $aclRoleTable;

    /**
     * @return array
     */
    public function getMetaList()
    {
        return $this->userMetaTable->getAllByUserId($this->userId);
    }

    /**
     * @return AclRoleEntity
     */
    public function getCurrentUserRole()
    {
        /** @var ApplicationEntity $application */
        $application = $this->applicationTable->getCurrentApplication();
        /** @var UserRoleEntity $userRole */
        $userRole = $this->userRoleTable->getRole($this->userId, $application->applicationId);
        return $this->aclRoleTable->getRoleById($userRole->aclRoleId);
    }

    /**
     * @param string $applicationName
     * @return AclRoleEntity
     */
    public function getUserRoleForApplication($applicationName)
    {
        /** @var ApplicationEntity $application */
        $application = $this->applicationTable->getApplicationByName($applicationName);
        /** @var UserRoleEntity $userRole */
        $userRole = $this->userRoleTable->getRole($this->userId, $application->applicationId);
        return $this->aclRoleTable->getRoleById($userRole->aclRoleId);
    }

    /**
     * Override default debug information to hide injected elements
     */
    public function __debugInfo()
    {
        return [
            'userId' => $this->userId,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'hash' => $this->hash,
            'lastIp' => $this->lastIp,
            'registerIp' => $this->registerIp,
            'isActive' => $this->isActive,
            'isEnabled' => $this->isEnabled,
            'timeLogin' => $this->timeLogin,
            'timeRegister' => $this->timeRegister,
        ];
    }

    /**
     * Exchange array values into object properties.
     *
     * @param array $data
     *
     * @return Entity
     */
    public function exchangeArray($data)
    {
        $this->userId = (isset($data['id_user'])) ? (int)$data['id_user'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->hash = (isset($data['hash'])) ? $data['hash'] : null;
        $this->lastIp = (isset($data['last_ip'])) ? $data['last_ip'] : null;
        $this->registerIp = (isset($data['register_ip'])) ? $data['register_ip'] : null;
        $this->isActive = (isset($data['is_active'])) ? (bool)$data['is_active'] : null;
        $this->isEnabled = (isset($data['is_enabled'])) ? (bool)$data['is_enabled'] : null;
        $this->timeLogin = (isset($data['time_login'])) ? new DateTime($data['time_login']) : null;
        $this->timeRegister = (isset($data['time_register'])) ? new DateTime($data['time_register']) : null;

        return $this;
    }

    /**
     * Exchange object properties into array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id_user' => $this->userId,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'hash' => $this->hash,
            'last_ip' => $this->lastIp,
            'register_ip' => $this->registerIp,
            'is_active' => $this->isActive ? 1 : 0,
            'is_enabled' => $this->isEnabled ? 1 : 0,
            'time_login' => $this->timeLogin ? $this->timeLogin->format('Y-m-d H:i:s') : null,
            'time_register' => $this->timeRegister ? $this->timeRegister->format('Y-m-d H:i:s') : null
        ];
    }
}
