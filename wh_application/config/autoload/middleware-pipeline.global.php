<?php
/**
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
 */

use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Helper;

return [
    'dependencies' => [
        'factories' => [
            WebHemi\Auth\AuthMiddleware::class => WebHemi\Middleware\MiddlewareFactory::class,
            WebHemi\Acl\AclMiddleware::class => WebHemi\Middleware\MiddlewareFactory::class,
            WebHemi\Error\ErrorMiddleware::class => WebHemi\Middleware\MiddlewareFactory::class,
            WebHemi\Application\ApplicationMiddleware::class => WebHemi\Application\ApplicationMiddlewareFactory::class,
        ],
    ],
    'middleware_pipeline' => [
        'always' => [
            'middleware' => [
                WebHemi\Application\ApplicationMiddleware::class
            ],
            'priority' => 10000,
        ],

        'routing' => [
            'middleware' => [
                ApplicationFactory::ROUTING_MIDDLEWARE,

                WebHemi\Auth\AuthMiddleware::class,
                WebHemi\Acl\AclMiddleware::class,

                ApplicationFactory::DISPATCH_MIDDLEWARE,
            ],
            'priority' => 1,
        ],

        'error' => [
            'middleware' => [
                WebHemi\Error\ErrorMiddleware::class
            ],
            'error'    => true,
            'priority' => -10000,
        ],
    ],
];
