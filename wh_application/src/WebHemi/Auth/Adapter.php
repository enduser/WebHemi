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
use Zend\Crypt\Password\Bcrypt;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Validator\Ip as IpValidator;
use WebHemi\Client\Lock\Table as ClientLockTable;
use WebHemi\User\Table as UserTable;
use WebHemi\User\Entity as UserEntity;
use DateTime;

/**
 * Class Adapter
 * @package WebHemi\Auth
 */
class Adapter implements AdapterInterface
{
    /** Default bcrypt password cost */
    const PASSWORD_COST = 14;

    /** @var string */
    public $identity = null;

    /** @var  array */
    protected $serverData;
    /** @var string */
    protected $credential;
    /** @var UserTable */
    protected $userTable;
    /** @var ClientLockTable  */
    protected $clientLockTable;
    /** @var UserEntity */
    protected $verifiedUser;

    /**
     * Adapter constructor.
     * @param UserTable $userTable
     * @param ClientLockTable $clientLockTable
     */
    public function __construct(UserTable $userTable, ClientLockTable $clientLockTable)
    {
        $this->userTable = $userTable;
        $this->clientLockTable = $clientLockTable;

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
        /** @var UserEntity $userModel */

        if (!isset($this->verifiedUser)) {
            if (strpos($this->identity, '@') !== false) {
                // identified by email
                $userModel = $this->userTable->getUserByEmail($this->identity);
            } else {
                // identified by username
                $userModel = $this->userTable->getUserByName($this->identity);
            }
//var_dump($userModel);exit;
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(self::PASSWORD_COST);

            // if identity not found
            if (!$userModel) {
                $authResult = new Result(
                    Result::FAILURE_IDENTITY_NOT_FOUND,
                    $this->identity,
                    ['A record with the supplied identity could not be found.']
                );
            } elseif (!$userModel->isActive || !$userModel->isEnabled) {
                // else if the identity exists but not activated or disabled
                $authResult = new Result(
                    Result::FAILURE_UNCATEGORIZED,
                    $this->identity,
                    ['A record with the supplied identity is not available.']
                );
            } elseif (!$bcrypt->verify($this->credential, $userModel->password)) {
                // else if the supplied credential is not valid
                $authResult = new Result(
                    Result::FAILURE_CREDENTIAL_INVALID,
                    $this->identity,
                    ['Supplied credential is invalid.']
                );
            }
        } else {
            $userModel = $this->verifiedUser;
        }

        // if authentication was successful
        if (!isset($authResult) && $userModel instanceof UserEntity) {
            // update some additional info
            $remoteAddress = new RemoteAddress();
            $ipValidator = new IpValidator();

            $ipAddress = $remoteAddress->setUseProxy()->getIpAddress();

            if (!$ipValidator->isValid($ipAddress)) {
                $ipAddress = $this->serverData['REMOTE_ADDR'];
            }

            $userModel->lastIp = $ipAddress;
            $userModel->timeLogin = new DateTime(gmdate('Y-m-d H:i:s'));

            // if no hash has been set yet
            if (empty($userModel->hash)) {
                $userModel->hash = md5($userModel->username . '-' . $userModel->email);
            }

            $this->userTable->update($userModel->toArray());

            // result success
            $authResult = new Result(
                Result::SUCCESS,
                $userModel,
                ['Authentication successful.']
            );

            // avoid auth process in the same runtime
            $this->verifiedUser = $userModel;

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
}
