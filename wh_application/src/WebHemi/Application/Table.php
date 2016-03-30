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

use Zend\Db\Exception;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

/**
 * Class Table
 * @package WebHemi\Application
 */
class Table extends AbstractTableGateway
{
    /** @var string */
    protected $table = 'webhemi_application';

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
     * @param int $applicationId
     *
     * @return Entity
     */
    public function getApplicationById($applicationId)
    {
        $rowSet = $this->select(['id_application' => (int)$applicationId]);
        return $rowSet->current();
    }

    /**
     * Retrieve entity by user name
     *
     * @param string $name
     *
     * @return Entity
     */
    public function getApplicationByName($name)
    {
        $rowSet = $this->select(['name' => $name]);
        return $rowSet->current();
    }

    /**
     * Retrieve entity by email address
     *
     * @return Entity
     */
    public function getCurrentApplication()
    {
        return $this->getApplicationByName(APPLICATION_MODULE);
    }
}
