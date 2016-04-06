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

namespace WebHemi\Acl\Resource;

use WebHemi\Db\AbstractEntity;

/**
 * Class Entity
 * @package WebHemi\Acl\Resource
 *
 * @property int $aclResourceId
 * @property string $name
 * @property bool $isReadOnly
 * @property string $description
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
        $this->aclResourceId = isset($data['id_acl_resource']) ? (int)$data['id_acl_resource'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->isReadOnly = isset($data['is_read_only']) ? (bool)$data['is_read_only'] : false;
        $this->description = isset($data['description']) ? $data['description'] : null;

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
            'id_acl_resource' => $this->aclResourceId,
            'name' => $this->name,
            'is_read_only' => $this->isReadOnly ? 1 : 0,
            'description' => $this->description
        ];
    }
}
