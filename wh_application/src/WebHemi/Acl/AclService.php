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

namespace WebHemi\Acl;

use WebHemi\Acl\Role\Provider as RoleProvider;
use WebHemi\Acl\Role\Entity as AclRoleEntity;
use WebHemi\User\Entity as UserEntity;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Exception;
use Zend\Authentication\AuthenticationService;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class AclService
 * @package WebHemi\Acl
 *
 * @method UserEntity $this->auth->getIdentity()
 */
class AclService implements DependencyInjectionInterface
{
    /** @var  ZendAcl */
    protected $acl;
    /** @var  AuthenticationService */
    protected $auth;

    public function __construct(ZendAcl $acl)
    {
        // set core ACL service
        $this->acl = $acl;
        // deny all by default
        $this->acl->deny();
    }

    /**
     * Build ACL graph
     */
    public function init()
    {
        // todo implement ACL service, check WebHemi2\Acl\Acl
//        var_dump('ACL SERVICE');
//        dumpDefinitions();
    }

    /**
     * @param $resource
     * @param null $role
     * @return bool
     */
    public function isAllowed($resource, $role = null)
    {
//        if (APPLICATION_MODULE == APPLICATION_MODULE_ADMIN) return false;

        return true;
//        try {
//            if (empty($role)) {
//                $role = RoleProvider::DEFAULT_ROLE;
//
//                if ($this->auth->hasIdentity()) {
//                    /** @var UserEntity $userEntity */
//                    $userEntity = $this->auth->getIdentity();
//                    /** @var AclRoleEntity $role */
//                    $role = $userEntity->getCurrentUserRole()->name;
//                }
//            }
//
//            if (!$this->acl->hasResource($resource)) {
//                return false;
//            }
//
//            list($class, $action) = explode(':', $resource);
//            unset($class);
//
//            // allow access for logout page, invalid role or non-forced resources
//            if ('logout' == $action || !$this->acl->hasRole($role)) {
//                return true;
//            }
//
//            return $this->acl->isAllowed($role, $resource);
//        } catch (Exception\InvalidArgumentException $e) {
//            // It is not necessary to terminate the whole script running. Fair enough to return with a FALSE.
//            return false;
//        }
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
