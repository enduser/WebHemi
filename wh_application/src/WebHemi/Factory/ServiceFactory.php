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
                                $arguments[] = $parameter;
                            }
                        }

                        if (method_exists($instance, $method)) {
                            $instance->{$method}(...$arguments);
                        }
                    }
                }
            }

            return $instance;
        } catch (Exception $e) {
            throw new Exception('Cannot instantiate class: ' . $className, 500);
        }
    }
}
