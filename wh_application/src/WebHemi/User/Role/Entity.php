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

namespace WebHemi\User\Role;

use ArrayObject;

/**
 * Class Entity
 * @package WebHemi\User\Role
 *
 * @property int $userId
 * @property int $applicationId
 * @property int $aclRoleId
 */
class Entity extends ArrayObject
{
    /**
     * Exchange array values into object properties.
     *
     * @param array $data
     *
     * @return Entity
     */
    public function exchangeArray($data)
    {
        $this->userId = isset($data['fk_user']) ? (int)$data['fk_user'] : null;
        $this->applicationId = isset($data['fk_application']) ? (int)$data['fk_application'] : null;
        $this->aclRoleId = isset($data['fk_acl_role']) ? (int)$data['fk_acl_role'] : null;

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
            'fk_user' => $this->userId,
            'fk_application' => $this->applicationId,
            'fk_acl_role' => $this->aclRoleId
        ];
    }
}
