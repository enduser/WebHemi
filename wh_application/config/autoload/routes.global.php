<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\ZendRouter::class,
            WebHemi\Action\PingAction::class => WebHemi\Action\PingAction::class,
        ],
        'factories' => [
            WebHemi\Action\HomePageAction::class => WebHemi\Action\HomePageFactory::class,
        ],
    ],

    'routes' => [
        [
            'name' => 'home',
            'path' => '/',
            'middleware' => WebHemi\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'api.ping',
            'path' => '/api/ping',
            'middleware' => WebHemi\Action\PingAction::class,
            'allowed_methods' => ['GET'],
        ],
    ],
];
