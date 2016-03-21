<?php
/**
 * @see       http://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

namespace WebHemi\Application;

use Interop\Container\ContainerInterface;

class ApplicationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ApplicationMiddleware($container);
    }
}
