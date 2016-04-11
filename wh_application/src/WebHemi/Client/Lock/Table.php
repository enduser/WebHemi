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

namespace WebHemi\Client\Lock;

use Zend\Db\Exception;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Validator\Ip as IpValidator;

/**
 * Class Table
 * @package WebHemi\Client\Lock
 */
class Table extends AbstractTableGateway
{
    /** The maximum number of access attempts */
    const MAX_TRYINGS = 5;
    /** The number of minutes the login is locked upon reaching the maximum number of access attempts */
    const LOCK_TIME = 15;

    /** @var string */
    protected $table = 'webhemi_client_lock';
    /** @var Entity */
    protected static $entity;

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
     * @return Entity
     */
    public function getLock()
    {
        if (!isset(self::$entity)) {
            $remoteAddress = new RemoteAddress();
            $ipValidator = new IpValidator();
            $serverData = getServerVariables();

            $ipAddress = $remoteAddress->setUseProxy()->getIpAddress();

            if (!$ipValidator->isValid($ipAddress)) {
                $ipAddress = $serverData['REMOTE_ADDR'];
            }

            $rowset = $this->select(['client_ip' => $ipAddress]);
            $entity = $rowset->current();

            // if no record, we create one
            if (!$entity) {
                // instantiate the return object and save the new record
                $entity = new Entity();

                $entity->clientLockId = null;
                $entity->clientIp = $ipAddress;
                $entity->timeLock = null;
                $entity->tryings = 0;

                // if we can't save
                if (!$this->insert($entity->toArray())) {
                    $entity = false;
                }
            }

            self::$entity = $entity;
        }

        return self::$entity;
    }

    /**
     * @return int
     */
    public function setLock()
    {
        $entity = $this->getLock();

        if ($entity instanceof Entity) {
            $entity->tryings += 1;

            // if reached the maximum, then set lock
            if ($entity->tryings >= self::MAX_TRYINGS) {
                $entity->timeLock = new DateTime(gmdate('Y-m-d H:i:s'));
            }

            return $this->update($entity->toArray(), ['id_client_lock' => $entity->clientLockId]);
        }
        // on error
        return 0;
    }

    /**
     * @return int
     */
    public function releaseLock()
    {
        $entity = $this->getLock();

        if ($entity instanceof Entity) {
            $entity->tryings = 0;
            $entity->timeLock = null;

            return $this->update($entity->toArray(), ['id_client_lock' => $entity->clientLockId]);
        }
        // on error
        return 0;
    }
}
