<?php
/**
 * @see       http://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

namespace WebHemi\Acl;

use Zend\Expressive\Router\RouterInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template;

class AclMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AclMiddleware($container->get(RouterInterface::class), $container->get(Template\TemplateRendererInterface::class));
    }
}
