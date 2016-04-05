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

namespace WebHemi\User\Meta;

use Zend\Db\Exception;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use WebHemi\User\Entity as UserEntity;

/**
 * Class Table
 * @package WebHemi\User\Meta
 */
class Table extends AbstractTableGateway
{
    /** @var string */
    protected $table = 'webhemi_user_meta';

    /**
     * Table constructor.
     * @param Adapter $adapter
     * @param Entity $entity
     */
    public function __construct(Adapter $adapter, Entity $entity)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype($entity);
        $this->initialize();
    }

    /**
     * @param string $metaKey
     * @param int $userId
     * @return Entity
     */
    public function getMetaByUserId($metaKey, $userId)
    {
        if ($userId instanceof UserEntity) {
            $userId = $userId->userId;
        }

        $rowSet = $this->select(['fk_user' => $userId, 'meta_key' => $metaKey]);
        return $rowSet->current();
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getAllByUserId($userId)
    {
        if ($userId instanceof UserEntity) {
            $userId = $userId->userId;
        }

        $rowSet     = $this->select(['fk_user' => $userId]);
        $entityList = [];

        /** @var Entity $entity */
        while ($entity = $rowSet->current()) {
            $entityList[$entity->metaKey] = $entity->metaData;
            $rowSet->next();
        }

        return $entityList;
    }
}
