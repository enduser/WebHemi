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

namespace WebHemi\Router;

use WebHemi\Router\Exception as RouterException;
use Zend\Expressive\Router\ZendRouter;
use Zend\Expressive\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebHemi\Application\DependencyInjectionInterface;
use WebHemi\Action\Website\ViewAction;

/**
 * Class Middleware
 * @package WebHemi\Router
 */
class Middleware implements DependencyInjectionInterface
{
    /** @var  ZendRouter */
    protected $router;

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
        /** @var RouteResult $routeResult */
        $routeResult = $this->router->match($request);

        // Cut middleware sequence and go to ErrorMiddleware directly
        if ($routeResult->isFailure()) {
            throw new RouterException();
        }

        // For Website module the content is under dynamic URIs, so we need to check in the database
        if (APPLICATION_MODULE == APPLICATION_MODULE_WEBSITE && $routeResult->getMatchedMiddleware() == ViewAction::class) {
            var_dump($routeResult);
            $matchedParams = $routeResult->getMatchedParams();
            $requestedUri = $matchedParams['customPath'];


            // @todo implement
            // If dynamic path not found, raise an error
            if (!$requestedUri) {
                throw new RouterException();
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
    public function injectDependency($property, $service)
    {
        $this->{$property} = $service;
    }
}
