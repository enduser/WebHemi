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

namespace WebHemi\Factory;

use Interop\Container\ContainerInterface;
use ReflectionClass;
use Exception;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class ServiceFactory
 * @package WebHemi\Factory
 */
class ServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param null $canonicalName
     * @param null $requestedName
     * @return object
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container, $canonicalName = null, $requestedName = null)
    {
        if (empty($requestedName)) {
            $requestedName = $canonicalName;
        }

        if (!class_exists($requestedName)) {
            throw new Exception('Cannot instantiate class: ' . $requestedName, 500);
        }

        // Construct a new ReflectionClass object for the requested action
        $reflection = new ReflectionClass($requestedName);
        // Get the constructor
        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            // There is no constructor, just return a new class
            $instance = new $requestedName;
        } else {
            // Get the parameters
            $parameters = $constructor->getParameters();
            $dependencies = [];
            foreach ($parameters as $parameter) {
                // Get the parameter class
                $class = $parameter->getClass();
                // Get the class from the container
                $dependencies[] = $container->get($class->getName());
            }
            // Instantiate the requested class and inject its dependencies via the constructor
            $instance = $reflection->newInstanceArgs($dependencies);
        }

        // Check the class for non-mandatory dependencies
        if ($reflection->implementsInterface(DependencyInjectionInterface::class)) {
            $properties = $reflection->getDefaultProperties();

            // Inject only when the dependency list is declared
            if (isset($properties['dependency']) && is_array($properties['dependency'])) {
                foreach ($properties['dependency'] as $property => $serviceName) {
                    if ($container->has($serviceName)) {
                        // If it is a registered service
                        $service = $container->get($serviceName);
                    } elseif (class_exists($serviceName)) {
                        // If it is a class
                        $service = new $serviceName();
                    }  else {
                        // If it is a scalar, array, object or resource
                        $service = $serviceName;
                    }

                    $instance->injectDependency($property, $service);
                }
            }
        }

        return $instance;
    }
}
