<?php
/**
 * @see       http://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

namespace WebHemi\Auth;

use Zend\Expressive\Router\RouterInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template;

class AuthMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthMiddleware($container->get(RouterInterface::class));
    }
}