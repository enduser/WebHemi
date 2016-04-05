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

use DateTime;
use ArrayObject;

/**
 * Class Entity
 * @package WebHemi\Client\Lock
 *
 * @property int $clientLockId
 * @property string $clientIp
 * @property int $tryings
 * @property DateTime $timeLock
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

        $this->clientLockId = (isset($data['id_client_lock'])) ? (int)$data['id_client_lock'] : null;
        $this->clientIp = (isset($data['client_ip'])) ? $data['client_ip'] : null;
        $this->tryings = (isset($data['tryings'])) ? (int)$data['tryings'] : 0;
        $this->timeLock = (isset($data['time_lock'])) ? new DateTime($data['time_lock']) : null;

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
            'id_client_lock' => $this->clientLockId,
            'client_ip' => $this->clientIp,
            'tryings' => $this->tryings,
            'time_lock' => $this->timeLock ? $this->timeLock->format('Y-m-d H:i:s') : null,
        ];
    }
}
