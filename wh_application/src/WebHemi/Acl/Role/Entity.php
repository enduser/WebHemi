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

namespace WebHemi\Acl\Role;

use WebHemi\Db\AbstractEntity;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class Entity
 * @package WebHemi\Acl\Role
 *
 * @property int $aclRoleId
 * @property string $name
 * @property bool $isReadOnly
 * @property string $description
 */
class Entity extends AbstractEntity implements RoleInterface
{
    const DEFAULT_ROLE = 'guest';

    /**
     * Unique id of Role
     *
     * @var string
     */
    protected $roleId;

    /**
     * Defined by RoleInterface; returns the Role identifier
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Defined by RoleInterface; returns the Role identifier
     * Proxies to getRoleId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getRoleId();
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
        $this->aclRoleId = isset($data['id_acl_role']) ? (int)$data['id_acl_role'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->isReadOnly = isset($data['is_read_only']) ? (bool)$data['is_read_only'] : false;
        $this->description = isset($data['description']) ? $data['description'] : null;

        $this->roleId = $this->name;

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
            'id_acl_role' => $this->aclRoleId,
            'name' => $this->name,
            'is_read_only' => $this->isReadOnly ? 1 : 0,
            'description' => $this->description
        ];
    }
}
