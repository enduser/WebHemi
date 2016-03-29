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

use Zend\Db\Exception;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

/**
 * Class Table
 * @package WebHemi\User
 */
class Table extends AbstractTableGateway
{
    /** @var string */
    protected $table = 'webhemi_user';

    /**
     * Class constructor
     *
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Entity());
        $this->initialize();
    }

    /**
     * Retrieve entity by identifier
     *
     * @param int $userId
     *
     * @return Entity
     */
    public function getUserById($userId)
    {
        $data = [
            'user_id' => 1,
            'username' => 'admin',
            'email' => 'admin@foo.org',
            'password' => '$2y$14$H2WLOqAPyZqZBDPy/8NMEemMBIYQFJoaVQG.wuVrAG23e/UEz34GG',
            'hash' => 'a11abe47c50c5b9b4d28add27f80d601',
            'last_ip' => '192.168.56.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new Entity();
        $entity->exchangeArray($data);
        return $entity;

        $rowSet = $this->select(['id_user' => (int)$userId]);
        return $rowSet->current();
    }

    /**
     * Retrieve entity by user name
     *
     * @param string $username
     *
     * @return Entity
     */
    public function getUserByName($username)
    {
        $data = [
            'user_id' => 1,
            'username' => 'admin',
            'email' => 'admin@foo.org',
            'password' => '$2y$14$H2WLOqAPyZqZBDPy/8NMEemMBIYQFJoaVQG.wuVrAG23e/UEz34GG',
            'hash' => 'a11abe47c50c5b9b4d28add27f80d601',
            'last_ip' => '192.168.56.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new Entity();
        $entity->exchangeArray($data);
        return $entity;

        $rowSet = $this->select(['username' => $username]);
        return $rowSet->current();
    }

    /**
     * Retrieve entity by email address
     *
     * @param string $email
     *
     * @return Entity
     */
    public function getUserByEmail($email)
    {
        $data = [
            'user_id' => 1,
            'username' => 'admin',
            'email' => 'admin@foo.org',
            'password' => '$2y$14$H2WLOqAPyZqZBDPy/8NMEemMBIYQFJoaVQG.wuVrAG23e/UEz34GG',
            'hash' => 'a11abe47c50c5b9b4d28add27f80d601',
            'last_ip' => '192.168.56.1',
            'register_ip' => '127.0.0.1',
            'is_active' => true,
            'is_enabled' => true,
            'time_login' => '2016-03-29 07:24:11',
            'timeRegister' =>  '2016-03-24 16:25:12',
        ];

        $entity = new Entity();
        $entity->exchangeArray($data);
        return $entity;

        $rowSet = $this->select(['email' => $email]);
        return $rowSet->current();
    }
}
