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

use WebHemi\Acl\Resource\Table as AclResourceTable;
use WebHemi\Acl\Resource\Entity as AclResourceEntity;
use WebHemi\Acl\Role\Table as AclRoleTable;
use WebHemi\Acl\Role\Entity as AclRoleEntity;
use WebHemi\Acl\Rule\Table as AclRuleTable;
use WebHemi\Acl\Rule\Entity as AclRuleEntity;
use WebHemi\Acl\Assert\CleanIp;
use WebHemi\User\Entity as UserEntity;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Exception as ZendException;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Authentication\AuthenticationService;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class AclService
 * @package WebHemi\Acl
 */
class AclService implements DependencyInjectionInterface
{
    /** @var  ZendAcl */
    protected $acl;
    /** @var  AuthenticationService */
    protected $auth;
    /** @var  AclResourceTable */
    protected $aclResourceTable;
    /** @var  AclRoleTable */
    protected $aclRoleTable;
    /** @var  AclRuleTable */
    protected $aclRuleTable;
    /** @var  CleanIp */
    protected $assertion;

    /**
     * AclService constructor.
     * @param ZendAcl $acl
     */
    public function __construct(ZendAcl $acl = null)
    {
        if ($acl instanceof ZendAcl) {
            // set core ACL service
            $this->acl = $acl;
            // deny all by default
            $this->acl->deny();
        }
    }

    /**
     * @return $this
     */
    public function init()
    {
        if ($this->acl instanceof ZendAcl) {
            $resources = $this->aclResourceTable->getResources();
            $roles = $this->aclRoleTable->getRoles();
            $rules = $this->aclRuleTable->getRules(true);

            /** @var AclResourceEntity $aclResourceEntity */
            foreach ($resources as $aclResourceEntity) {
                $this->acl->addResource($aclResourceEntity);
            }

            /** @var AclRoleEntity $aclRoleEntity */
            foreach ($roles as $aclRoleEntity) {
                $this->acl->addRole($aclRoleEntity);
            }

            /** @var AclRuleEntity $aclRuleEntity */
            foreach ($rules as $aclRuleEntity) {
                /** @var AclResourceEntity $resource */
                $resource = $aclRuleEntity->getResource();
                /** @var AclRoleEntity $role */
                $role = $aclRuleEntity->getRole();

                if ($resource && $this->acl->hasResource($resource->name) && $role && $this->acl->hasRole($role->name)) {
                    $this->acl->allow($role->name, $resource->name, null, $this->assertion);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $resourceName
     * @param null $roleName
     * @return bool
     */
    public function isAllowed($resourceName, $roleName = null)
    {
        try {
            if (empty($roleName)) {
                $roleName = AclRoleEntity::DEFAULT_ROLE;

                if ($this->auth->hasIdentity()) {
                    /** @var UserEntity $userEntity */
                    $userEntity = $this->auth->getIdentity();
                    /** @var AclRoleEntity $role */
                    $role = $userEntity->getCurrentUserRole();
                    $roleName = $role->name;
                }
            }

            // Deny for invalid resource
            if (!$this->acl->hasResource($resourceName)) {
                return false;
            }

            list($actionGroup, $actionName) = explode(':', $resourceName);
            unset($actionGroup);

            // Allow access for login and logout pages, invalid role or non-forced resources
            if ('login' == $actionName || 'logout' == $actionName || !$this->acl->hasRole($roleName)) {
                return true;
            }

            return $this->acl->isAllowed($roleName, $resourceName);
        } catch (ZendException\InvalidArgumentException $e) {
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
     *
     * @codeCoverageIgnore
     */
    public function injectDependency($property, $service)
    {
        $this->{$property} = $service;
    }
}
