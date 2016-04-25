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

namespace WebHemi\Auth\Storage;

use Zend\Session\Container as SessionContainer;
use Zend\Authentication\Storage\StorageInterface;
use WebHemi\User\Table as UserTable;
use WebHemi\User\Entity as UserEntity;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class Session
 * @package WebHemi\Auth\Storage
 */
class Session implements DependencyInjectionInterface, StorageInterface
{
    const NAMESPACE_DEFAULT = 'WebHemiAuth';

    const MEMBER_DEFAULT = 'storage';

    const SESSION_SALT_DEFAULT = 'WebHemi';

    /** @var UserTable */
    protected $userTable;
    /** @var SessionContainer */
    protected $session;
    /**@var string */
    protected $namespace = self::NAMESPACE_DEFAULT;
    /**@var string */
    protected $member = self::MEMBER_DEFAULT;
    /** @var  UserEntity */
    protected $resolvedIdentity;

    /**
     * Session constructor.
     */
    public function __construct()
    {
        $this->secureConfigSession();
        $this->session = new SessionContainer($this->namespace);
    }

    /**
     * Overwrite PHP settings to be more secure
     *
     * @codeCoverageIgnore
     */
    protected function secureConfigSession()
    {
        ini_set('session.entropy_file', '/dev/urandom');
        ini_set('session.entropy_length', '16');
        ini_set('session.hash_function', 'sha256');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_httponly', '1');

        // hide session name
        session_name(SESSION_COOKIE_PREFIX . '-' . bin2hex(self::SESSION_SALT_DEFAULT));
        // set session lifetime to 1 hour
        session_set_cookie_params(3600);
    }

    /**
     * Regenerate Storage Session Id
     *
     * @codeCoverageIgnore
     */
    public function regenerateStorageId()
    {
        $this->session->getManager()->regenerateId();
    }

    /**
     * Check whether the storage is empty
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function isEmpty()
    {
        return !isset($this->session->{$this->member});
    }

    /**
     * Retrieve the contents of storage
     *
     * @return UserEntity
     */
    public function read()
    {
        if (null !== $this->resolvedIdentity) {
            return $this->resolvedIdentity;
        }

        $identity = $this->session->{$this->member};

        // unserialized object doesn't contain the dependencies
        if ($identity instanceof UserEntity) {
            $identity = $identity->userId;
        }

        $identity = $this->userTable->getUserById($identity);

        if ($identity instanceof UserEntity) {
            $this->resolvedIdentity = $identity;
        } else {
            $this->resolvedIdentity = null;
        }

        return $this->resolvedIdentity;
    }

    /**
     * Write contents to storage
     *
     * @param  mixed $contents
     */
    public function write($contents)
    {
        $this->resolvedIdentity = null;
        $this->session->{$this->member} = $contents;
    }

    /**
     * Clear contents from storage
     */
    public function clear()
    {
        $this->resolvedIdentity = null;
        unset($this->session->{$this->member});
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
