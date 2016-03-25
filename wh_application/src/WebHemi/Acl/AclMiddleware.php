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
use WebHemi\Auth\AuthenticationService;

/**
 * Class AclMiddleware
 * @package WebHemi\Acl
 */
class AclMiddleware
{
    /** @var  AuthenticationService */
    protected $auth;

    /**
     * AclMiddleware constructor.
     * @param AuthenticationService $auth
     */
    public function __construct(AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

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

        if (!$this->auth->hasIdentity()) {
            $this->auth->getAdapter()->setIdentity('admin');
            $this->auth->getAdapter()->setCredential('admin');
            $this->auth->authenticate();
        }
        return $next($request, $response);
    }
}
