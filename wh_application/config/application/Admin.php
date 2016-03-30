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

return [
    'dependencies' => [
        'invokables' => [
            WebHemi\Action\PingAction::class => WebHemi\Action\PingAction::class,
        ],
        'factories' => [
            WebHemi\Action\HomePageAction::class => WebHemi\Factory\MiddlewareFactory::class,
        ],
        'service_factory' => [
            WebHemi\Action\HomePageAction::class => [
                'class' => WebHemi\Action\HomePageAction::class,
                'calls' => [
                    ['injectDependency' => [':auth',     Zend\Authentication\AuthenticationService::class]],
                    ['injectDependency' => [':router',   Zend\Expressive\Router\RouterInterface::class]],
                    ['injectDependency' => [':template', Zend\Expressive\Template\TemplateRendererInterface::class]],
                    ['injectDependency' => [':config',   'config']]
                ]
            ],
        ]
    ],
    'templates' => [
        'map' => [
            'layout/login' => 'layout/login.phtml',
            'layout/layout' => 'layout/admin.phtml',
            'layout/error' => 'layout/error.phtml',
            'error/error' => 'error/500.phtml',
            'error/500' => 'error/500.phtml',
            'error/401' => 'error/401.phtml',
            'error/403' => 'error/403.phtml',
            'error/404' => 'error/404.phtml'
        ]
    ],
    'routes' => [
        [
            'name' => 'home',
            'path' => '/',
            'middleware' => WebHemi\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'authome',
            'path' => '/auth/',
            'middleware' => WebHemi\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'api.ping',
            'path' => '/api/ping/',
            'middleware' => WebHemi\Action\PingAction::class,
            'allowed_methods' => ['GET'],
        ],
    ],
];
