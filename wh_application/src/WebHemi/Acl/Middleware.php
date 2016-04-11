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

use WebHemi\Acl\Exception as AclException;
use Zend\Expressive\Router\ZendRouter;
use Zend\Expressive\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Authentication\AuthenticationService;
use WebHemi\User\Entity as UserEntity;
use WebHemi\Acl\Role\Entity as AclRoleEntity;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class Middleware
 * @package WebHemi\Acl
 */
class Middleware implements DependencyInjectionInterface
{
    /** @var  ZendRouter */
    protected $router;
    /** @var  AuthenticationService */
    protected $auth;
    /** @var  AclService */
    protected $acl;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return mixed
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        /** @var RouteResult $routeResult */
        $routeResult = $this->router->match($request);
        $middleware = str_replace(['WebHemi\\Action\\', 'Action'], [], $routeResult->getMatchedMiddleware());
        $resourceName = str_replace('\\', ':', $this->camelToDashed($middleware));

        if ($resourceName) {
            list($actionGroup, $actionName) = explode(':', $resourceName);
            unset($actionGroup);

            if ($this->auth->hasIdentity()) {
                /** @var UserEntity $userEntity */
                $userEntity = $this->auth->getIdentity();
                $roleName = $userEntity->getCurrentUserRole()->name;
            } else {
                $roleName = AclRoleEntity::DEFAULT_ROLE;
            }

            $allowed = $this->acl->isAllowed($resourceName, $roleName);

            if (!$allowed) {
                // in admin module if there's no authenticated user, the user should be redirected to the login page
                if (APPLICATION_MODULE == APPLICATION_MODULE_ADMIN
                    && $actionName != 'login'
                    && !$this->auth->hasIdentity()
                ) {
                    $redirect = '/login/';

                    if (APPLICATION_MODULE_TYPE == APPLICATION_MODULE_TYPE_SUBDIR) {
                        $redirect = '/' . APPLICATION_MODULE_URI . $redirect;
                    }

                    $response->withStatus(302);
                    return $response->withHeader('location', $redirect);
                } else {
                    // otherwise it's a 403 Forbidden error
                    throw new AclException();
                }
            }
        }

        return $next($request, $response);
    }

    /**
     * Converts
     *
     * @param $className
     * @return mixed
     */
    protected function camelToDashed($className)
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $className));
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
