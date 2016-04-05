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
use Exception;

/**
 * Class ServiceFactory
 * @package WebHemi\Factory
 *
 * @example Basic ServiceFactory configuration
 *
 * 'dependencies' => [
 *      'invokables' => [
 *          // ...
 *      ],
 *      // This class must be defined for services to use the service_factory feature
 *      'factories' => [
 *          SomeService::class => WebHemi\Factory\ServiceFactory::class,
 *          'SomeOtherServiceAlias' => WebHemi\Factory\ServiceFactory::class,
 *      ],
 *      // This is not part of the ZF core, only parsed and used by this class
 *      'service_factory' => [
 *          // The factory will instantiate the service with injecting dependencies as constructor arguments
 *          SomeService::class => [
 *              'arguments' => [SomeTable::class, SomeAdapter::class]
 *          ],
 *          // The factory will instantiate the service and injecting dependencies by setters
 *          'SomeOtherServiceAlias' => [
 *              // When alias is used as service name, the original class must be defined
 *              'class' => SomeOtherService::class,
 *              'calls' => [
 *                  ['classMethodName' => ['argument1', 'argument2', SomeZendService::class]]
 *              ]
 *          ]
 *      ]
 *  ]
 *
 * For both class constructor arguments and setter method arguments the following cases are possible:
 *
 * - the argument is a registered service: it will be retrieved from the the container and be passed its instance
 * - the argument is not a service, but an existing class: it will be instantiated and be passed its instance
 * - the argument is an object instance, an array, a resource or a scalar: it will be passes as is
 *
 * @example It's a possible to force a parameter to be scalar to make is sure it won't match any registered service:
 *
 *      // It will be found as a registered service (see config/container.php)
 *      'arguments' => ['config'],
 *
 *      // It will be used as a scalar after trimming the leading `:` character
 *      'arguments' => [':config'],
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
        $className = $requestedName ?: $canonicalName;
        $config = $container->get('config');

        try {
            if (!isset($config['dependencies']['service_factory'][$className])) {
                $instance = new $className;
            } else {
                // resolve possible alias
                $className = (isset($config['dependencies']['service_factory'][$className]['class']))
                    ? $config['dependencies']['service_factory'][$className]['class']
                    : $className;

                $arguments = [];

                // checking arguments
                if (isset($config['dependencies']['service_factory'][$className]['arguments'])) {
                    foreach ($config['dependencies']['service_factory'][$className]['arguments'] as $parameter) {
                        if ($container->has($parameter)) {
                            $arguments[] = $container->get($parameter);
                        } elseif (class_exists($parameter)) {
                            $arguments[] = new $parameter;
                        } else {
                            // support forcing to scalar
                            if (strpos($parameter, ':') === 0) {
                                $parameter = substr($parameter, 1);
                            }

                            $arguments[] = $parameter;
                        }
                    }
                }

                // instantiate the class with the given argument list
                $instance = new $className(...$arguments);

                // checking for data injection
                if (isset($config['dependencies']['service_factory'][$className]['calls'])) {
                    foreach ($config['dependencies']['service_factory'][$className]['calls'] as $dependency) {
                        $method = key($dependency);
                        $parameters = current($dependency);
                        $arguments = [];

                        foreach ($parameters as $parameter) {
                            if ($container->has($parameter)) {
                                $arguments[] = $container->get($parameter);
                            } elseif (class_exists($parameter)) {
                                $arguments[] = new $parameter;
                            } else {
                                // support forcing to scalar
                                if (strpos($parameter, ':') === 0) {
                                    $parameter = substr($parameter, 1);
                                }

                                $arguments[] = $parameter;
                            }
                        }

                        if (method_exists($instance, $method)) {
                            $instance->{$method}(...$arguments);
                        } else {
                            throw new Exception('Cannot call method ' . $method . ' in class: ' . $className, 500);
                        }
                    }
                }
            }
            return $instance;
        } catch (Exception $e) {
            throw new Exception('Cannot instantiate class: ' . $className . '. Error: ' . $e->getMessage(), 500);
        }
    }
}
