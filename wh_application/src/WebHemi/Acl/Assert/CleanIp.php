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

namespace WebHemi\Acl\Assert;

use DateTime;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use WebHemi\Client\Lock\Table as ClientLockTable;
use WebHemi\Application\DependencyInjectionInterface;

class CleanIp implements DependencyInjectionInterface, AssertionInterface
{
    /** @var  ClientLockTable */
    protected $clientLockTable;

    /**
     * Return true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Acl $acl
     * @param  RoleInterface $role
     * @param  ResourceInterface $resource
     * @param  string $privilege
     *
     * @return boolean
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        // determine the current timestamp according to the UTC time
        $currentTime = new DateTime(gmdate('Y-m-d H:i:s'));
        $currentTimestamp = $currentTime->getTimestamp();

        $lockTime = $this->clientLockTable->getLock()->timeLock;
        $lockTimestamp = $lockTime instanceof DateTime ? $lockTime->getTimestamp() : $currentTimestamp;

        // determine the timeout in seconds
        $timeout = ClientLockTable::LOCK_TIME * 60;

        // if the lock times out, it should be released
        if ($timeout < $currentTimestamp - $lockTimestamp) {
            $this->clientLockTable->releaseLock();
        }

        return $this->clientLockTable->getLock()->tryings >= ClientLockTable::MAX_TRYINGS ? false : true;
    }

    /**
     * Injects a service into the class
     *
     * @param string $property
     * @param object $service
     * @return void
     */
    public function injectDependency($property, $service)
    {
        $this->{$property} = $service;
    }
}
