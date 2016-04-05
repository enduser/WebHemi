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

use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class Role
 * @package WebHemi\Acl\Role
 */
class Role implements RoleInterface
{
    /** @var null|string  */
    protected $roleId;

    /** @var null|RoleInterface  */
    protected $parentRole;

    /**
     * Role constructor.
     * @param null|string $roleId
     * @param null|string|RoleInterface $parentRole
     */
    public function __construct($roleId = null, $parentRole = null)
    {
        if (isset($parentRole) && !$parentRole instanceof RoleInterface) {
            $parentRole = new Role($parentRole);
        }

        $this->roleId = $roleId;
        $this->parentRole = $parentRole;
    }

    /**
     * @return null|string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @param string $roleId
     * @return $this
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
        return $this;
    }

    /**
     * @return null|string|RoleInterface
     */
    public function getParentRole()
    {
        return $this->parentRole;
    }

    /**
     * @param RoleInterface $parentRole
     * @return $this
     */
    public function setParentRole(RoleInterface $parentRole)
    {
        $this->parentRole = $parentRole;
        return $this;
    }
}
