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

namespace WebHemi\Application;

use WebHemi\Db\AbstractEntity;

/**
 * Class Entity
 * @package WebHemi\Application
 *
 * @property int    $applicationId
 * @property string $name
 * @property bool   $isReadOnly
 * @property string $description
 * @property string $metaData
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
        $this->applicationId = (isset($data['id_application'])) ? (int)$data['id_application'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->isReadOnly = (isset($data['is_read_only'])) ? (bool)$data['is_read_only'] : true;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->metaData = (isset($data['meta_data'])) ? $data['meta_data'] : null;

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
            'id_application' => $this->applicationId,
            'name' => $this->name,
            'is_read_only' => $this->isReadOnly ? 1 : 0,
            'description' => $this->description,
            'meta_data' => $this->metaData
        ];
    }
}
