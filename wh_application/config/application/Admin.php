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
            WebHemi\Action\Admin\IndexAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\Admin\LoginAction::class => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Action\Admin\LogoutAction::class => WebHemi\Factory\MiddlewareFactory::class,
        ],
        'service_factory' => [
            WebHemi\Action\Admin\IndexAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
            WebHemi\Action\Admin\LoginAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
            WebHemi\Action\Admin\LogoutAction::class => ['arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]],
        ]
    ],
    'templates' => [
        'map' => [
            'layout/login' => 'view/layout/login.phtml',
            'layout/layout' => 'view/layout/admin.phtml',
            'layout/error' => 'view/layout/error.phtml',
            'error/error' => 'view/error/500.phtml',
            'error/500' => 'view/error/500.phtml',
            'error/401' => 'view/error/401.phtml',
            'error/403' => 'view/error/403.phtml',
            'error/404' => 'view/error/404.phtml'
        ]
    ],
    'routes' => [
        [
            'name' => 'index',
            'path' => '/',
            'middleware' => WebHemi\Action\Admin\IndexAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'login',
            'path' => '/login/',
            'middleware' => WebHemi\Action\Admin\LoginAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'logout',
            'path' => '/logout/',
            'middleware' => WebHemi\Action\Admin\LogoutAction::class,
            'allowed_methods' => ['GET'],
        ],
    ],
];
