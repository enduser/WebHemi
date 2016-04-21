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

namespace WebHemiTest\Acl;

use WebHemi\Acl\Assert\CleanIp;
use WebHemi\Client\Lock\Entity as ClientLockEntity;
use WebHemi\Client\Lock\Table as ClientLockTable;
use Prophecy\Argument;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Class ExceptionTest
 * @package WebHemiTest\Error
 */
class CleanIpTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [1, null, true],
            [2, null, true],
            [3, null, true],
            [4, null, true],
            [5, null, false],
            [5, gmdate('Y-m-d H:i:s', time()), false], // just locked
            [5, gmdate('Y-m-d H:i:s', time() - (ClientLockTable::LOCK_TIME * 60) + 20), false], // 20s to unlock
            [5, gmdate('Y-m-d H:i:s', time() - (ClientLockTable::LOCK_TIME * 60) - 20), true], // unlocked 20s ago
        ];
    }

    /**
     * @param int  $tryings
     * @param bool $expectedResult
     *
     * @dataProvider dataProvider
     */
    public function testAssert($tryings, $timeLock, $expectedResult)
    {
        $data = [
            'id_client_lock' => 10,
            'client_ip' => '127.0.0.1',
            'tryings' => $tryings,
            'time_lock' => $timeLock,
        ];

        $clientLockEntity = new ClientLockEntity();
        $clientLockEntity->exchangeArray($data);

        $clientLockTable = $this->prophesize(ClientLockTable::class);
        $clientLockTable->getLock()->willReturn($clientLockEntity);
        $clientLockTable->releaseLock()->willReturn(1);
        $clientLockTable->update(Argument::type('array'), Argument::type('array'))->willReturn(1);

        $cleanIp = new CleanIp();
        $cleanIp->injectDependency('clientLockTable', $clientLockTable->reveal());

        $result = $cleanIp->assert();

        $this->assertSame($expectedResult, $result);
    }
}
