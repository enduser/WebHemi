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

namespace WebHemi\Acl\Rule;

use WebHemi\Db\AbstractEntity;

/**
 * Class Entity
 * @package WebHemi\Acl\Rule
 *
 * @property int $aclRuleId
 * @property int $aclRoleId
 * @property int $aclResourceId
 * @property bool $isAllowed
 */
class Entity extends AbstractEntity
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
        $this->aclRuleId = isset($data['id_alc_rule']) ? (int)$data['id_alc_rule'] : null;
        $this->aclRoleId = isset($data['fk_acl_role']) ? (int)$data['fk_acl_role'] : null;
        $this->aclResourceId = isset($data['fk_acl_resource']) ? (int)$data['fk_acl_resource'] : null;
        $this->isAllowed = isset($data['is_allowed']) ? (int)$data['is_allowed'] : false;

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
            'id_acl_rule' => $this->aclRuleId,
            'fk_acl_role' => $this->aclRoleId,
            'fk_acl_resource' => $this->aclResourceId,
            'is_allowed' => $this->isAllowed ? 1 : 0
        ];
    }
}
