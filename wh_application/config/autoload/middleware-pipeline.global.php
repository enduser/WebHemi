<?php

use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Helper;

return [
    'dependencies' => [
        'factories' => [
            WebHemi\Auth\AuthMiddleware::class => WebHemi\Auth\AuthMiddlewareFactory::class,
            WebHemi\Acl\AclMiddleware::class => WebHemi\Acl\AclMiddlewareFactory::class,
            WebHemi\Error\ErrorMiddleware::class => WebHemi\Error\ErrorMiddlewareFactory::class,
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

        'errsdor' => [
            'middleware' => [
                WebHemi\Error\ErrorMiddleware::class
            ],
            'error'    => true,
            'priority' => -10000,
        ],
    ],
];
