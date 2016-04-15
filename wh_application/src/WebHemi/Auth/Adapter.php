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

namespace WebHemi\Auth;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Validator\Ip as IpValidator;
use WebHemi\Client\Lock\Table as ClientLockTable;
use WebHemi\User\Table as UserTable;
use WebHemi\User\Entity as UserEntity;
use WebHemi\Application\DependencyInjectionInterface;
use DateTime;

/**
 * Class Adapter
 * @package WebHemi\Auth
 */
class Adapter implements DependencyInjectionInterface, AdapterInterface
{
    const PASSWORD_COST = 9;

    const PASSWORD_ALGORITHM = PASSWORD_DEFAULT;

    /** @var string */
    public $identity = null;

    /** @var  array */
    protected $serverData;
    /** @var string */
    protected $credential;
    /** @var UserTable */
    protected $userTable;
    /** @var ClientLockTable */
    protected $clientLockTable;
    /** @var UserEntity */
    protected $verifiedUser;

    /**
     * Adapter constructor.
     */
    public function __construct()
    {
        // Avoid access to super global
        $this->serverData = filter_input_array(INPUT_SERVER);
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        /** @var UserEntity $userEntity */

        if (!isset($this->verifiedUser)) {
            if (strpos($this->identity, '@') !== false) {
                // identified by email
                $userEntity = $this->userTable->getUserByEmail($this->identity);
            } else {
                // identified by username
                $userEntity = $this->userTable->getUserByName($this->identity);
            }

            // if identity not found
            if (!$userEntity) {
                $authResult = new Result(
                    Result::FAILURE_IDENTITY_NOT_FOUND,
                    $this->identity,
                    ['A record with the supplied identity could not be found.']
                );
            } elseif (!$userEntity->isActive || !$userEntity->isEnabled) {
                // else if the identity exists but not activated or disabled
                $authResult = new Result(
                    Result::FAILURE_UNCATEGORIZED,
                    $this->identity,
                    ['A record with the supplied identity is not available.']
                );
            } elseif (!password_verify($this->credential, $userEntity->password)) {
                // else if the supplied credential is not valid
                $authResult = new Result(
                    Result::FAILURE_CREDENTIAL_INVALID,
                    $this->identity,
                    ['Supplied credential is invalid. ' . sha1($this->credential)]
                );
            }
        } else {
            $userEntity = $this->verifiedUser;
        }

        // if authentication was successful
        if (!isset($authResult) && $userEntity instanceof UserEntity) {
            // update some additional info
            $remoteAddress = new RemoteAddress();
            $ipValidator = new IpValidator();

            $ipAddress = $remoteAddress->setUseProxy()->getIpAddress();

            if (!$ipValidator->isValid($ipAddress)) {
                $ipAddress = $this->serverData['REMOTE_ADDR'];
            }

            $userEntity->lastIp = $ipAddress;
            $userEntity->timeLogin = new DateTime(gmdate('Y-m-d H:i:s'));

            // if no hash has been set yet
            if (empty($userEntity->hash)) {
                $userEntity->hash = md5($userEntity->username . '-' . $userEntity->email);
            }

            $this->userTable->update($userEntity->toArray());

            // result success
            $authResult = new Result(
                Result::SUCCESS,
                $userEntity,
                ['Authentication successful.']
            );

            // avoid auth process in the same runtime
            $this->verifiedUser = $userEntity;

            // @TODO: implement lock
            // reset the counter
            //$this->clientLockTable->releaseLock();
        } else {
            // increment the counter so the ACL's IP assert can ban for a specific time (LockTable::LOCK_TIME)
            //$this->clientLockTable->setLock();
        }

        return $authResult;
    }

    /**
     * Set a pre-verified user for auto login
     *
     * @param UserEntity $verifiedUser
     *
     * @return Adapter
     */
    public function setVerifiedUser(UserEntity $verifiedUser)
    {
        $this->verifiedUser = $verifiedUser;
        return $this;
    }

    /**
     * Set the value to be used as the identity
     *
     * @param  string $identity
     *
     * @return Adapter
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * Set the credential value to be used
     *
     * @param  string $credential
     *
     * @return Adapter
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

    /**
     * Injects a service into the class
     *
     * @param string $property
     * @param object $service
     * @return void
     * 
     * @codeCoverageIgnore
     */
    public function injectDependency($property, $service)
    {
        $this->{$property} = $service;
    }
}
