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

use WebHemi\Acl\Table;
use WebHemi\Acl\Role\Provider as RoleProvider;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Exception;
use Zend\Authentication\AuthenticationService;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class AclService
 * @package WebHemi\Acl
 */
class AclService implements DependencyInjectionInterface
{
    /** @var  Acl */
    protected $acl;
    /** @var  AuthenticationService */
    protected $auth;

    public function __construct(Acl $acl)
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
        var_dump('aclService::init');
    }

    /**
     * @param $resource
     * @param null $role
     * @return bool
     */
    public function isAllowed($resource, $role = null)
    {
        try {
            if (empty($role)) {
                // Todo: create table connection chain
//                $role = $this->auth->hasIdentity()
//                    ? $this->auth->getIdentity()->getRole()
//                    : RoleProvider::DEFAULT_ROLE;
            }

            if (!$this->acl->hasResource($resource)) {
                return false;
            }

            list($class, $action) = explode(':', $resource);

            // allow access for logout page, invalid role or non-forced resources
            if ('logout' == $action || !$this->acl->hasRole($role)) {
                return true;
            }

            return $this->acl->isAllowed($role, $resource);
        } catch (Exception\InvalidArgumentException $e) {
            // It is not necessary to terminate the whole script running. Fair enough to return with a FALSE.
            return false;
        }
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
