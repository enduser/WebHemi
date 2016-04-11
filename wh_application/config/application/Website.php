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
        'factories' => [
            WebHemi\Action\Website\IndexAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\Website\ViewAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\User\IndexAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\User\UserViewAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\User\UserProfileAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\User\UserEditAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\User\LoginAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\User\LogoutAction::class => WebHemi\Factory\MiddlewareFactory::class
        ],
        'service_factory' => [
            WebHemi\Action\Website\ViewAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
            WebHemi\Action\User\IndexAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
            WebHemi\Action\User\UserViewAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
            WebHemi\Action\User\UserProfileAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
            WebHemi\Action\User\UserEditAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
            WebHemi\Action\User\LoginAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
            WebHemi\Action\User\LogoutAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],


            WebHemi\Action\Website\IndexAction::class => [
                'class' => WebHemi\Action\Website\IndexAction::class,
                'arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class],
                'calls' => [
                    ['injectDependency' => [':auth',     Zend\Authentication\AuthenticationService::class]],
                    ['injectDependency' => [':router',   Zend\Expressive\Router\RouterInterface::class]],
                    ['injectDependency' => [':config',   'config']]
                ]
            ],
        ]
    ],
    'routes' => [
        [
            'name' => 'index',
            'path' => '/',
            'middleware' => WebHemi\Action\Website\IndexAction::class,
            'allowed_methods' => ['GET'],
        ],
//        [
//            'name' => 'view',
//            'type' => 'regex',
//            'middleware' => WebHemi\Action\Website\ViewAction::class,
//            'allowed_methods' => ['GET'],
//            'options' => [
//                'regex' => '\/(?:(?<category>[a-zA-Z0-9_-]+))?/(?<id>[\/a-zA-Z0-9_-]+)(\.(?<format>(json|html|rss)))?',
//                'defaults' => [
//                    'category' => 'default',
//                    'format' => 'html',
//                ],
//                'spec' => '/%category%/%id%.%format%',
//            ],
//        ],
//        [
//            'name' => 'user',
//            'path' => '/user/',
//            'middleware' => WebHemi\Action\User\IndexAction::class,
//            'allowed_methods' => ['GET'],
//        ],
//        [
//            'name' => 'user-view',
//            'type' => 'segment',
//            'path' => '/user/[:userName]/',
//            'middleware' => WebHemi\Action\User\UserViewAction::class,
//            'allowed_methods' => ['GET'],
//        ],
//        [
//            'name' => 'user-profile',
//            'path' => '/user/profile/',
//            'middleware' => WebHemi\Action\User\UserProfileAction::class,
//            'allowed_methods' => ['GET'],
//        ],
//        [
//            'name' => 'user-edit',
//            'path' => '/user/edit/',
//            'middleware' => WebHemi\Action\User\UserEditAction::class,
//            'allowed_methods' => ['GET'],
//        ],
        [
            'name' => 'login',
            'path' => '/login/',
            'middleware' => WebHemi\Action\User\LoginAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'logout',
            'path' => '/logout/',
            'middleware' => WebHemi\Action\User\LogoutAction::class,
            'allowed_methods' => ['GET'],
        ],
    ],
];
