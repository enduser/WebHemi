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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Authentication\Result;
use WebHemi\Auth\AuthenticationService;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class AclMiddleware
 * @package WebHemi\Acl
 */
class AclMiddleware implements DependencyInjectionInterface
{
    /** @var  AuthenticationService */
    protected $auth;
    /** @var  int */
    protected $number;
    /** @var array */
    protected $dependency = [
        'auth' => AuthenticationService::class,
        'number' => 3.141596
    ];

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return mixed
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // TODO: build ACL graph, check accessibility, throw an error when forbidden
        //throw new \Exception('Forbidden', 403);

        if ($this->auth && !$this->auth->hasIdentity()) {
            $this->auth->getAdapter()->setIdentity('admin');
            $this->auth->getAdapter()->setCredential('admin');
            /** @var Result $result */
            $result = $this->auth->authenticate();

            if ($result->getCode() != Result::SUCCESS) {
                throw new \Exception(implode('; ', $result->getMessages()), 403);
            }
        }
        return $next($request, $response);
    }

    /**
     * Injects a service into the class
     *
     * @param string $property
     * @param object $service
     * @return void
     */
    public function injectDependency($property, $service) {
        $this->{$property} = $service;
    }
}
