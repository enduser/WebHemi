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

namespace WebHemi\Middleware;

use Interop\Container\ContainerInterface;
use ReflectionClass;

/**
 * Class MiddlewareFactory
 * @package WebHemi\Middleware
 */
class MiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $canonicalName
     * @param string $requestedName
     * @return object
     */
    public function __invoke(ContainerInterface $container, $canonicalName = null, $requestedName = null)
    {
        // Construct a new ReflectionClass object for the requested action
        $reflection = new ReflectionClass($requestedName);
        // Get the constructor
        $constructor = $reflection->getConstructor();
        if (is_null($constructor)) {
            // There is no constructor, just return a new class
            return new $requestedName;
        }

        // Get the parameters
        $parameters = $constructor->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            // Get the parameter class
            $class = $parameter->getClass();
            // Get the class from the container
            $dependencies[] = $container->get($class->getName());
        }

        // Return the requested class and inject its dependencies
        return $reflection->newInstanceArgs($dependencies);
    }
}
