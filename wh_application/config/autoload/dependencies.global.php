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
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\ZendRouter::class,
            Zend\Expressive\Helper\ServerUrlHelper::class => Zend\Expressive\Helper\ServerUrlHelper::class,
        ],
        'factories' => [
            'Zend\Expressive\FinalHandler'                            => Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,
            Zend\View\HelperPluginManager::class                      => Zend\Expressive\ZendView\HelperPluginManagerFactory::class,
            Zend\Expressive\Template\TemplateRendererInterface::class => Zend\Expressive\ZendView\ZendViewRendererFactory::class,
            Zend\Expressive\Application::class                        => Zend\Expressive\Container\ApplicationFactory::class,
            Zend\Expressive\Helper\UrlHelper::class                   => Zend\Expressive\Helper\UrlHelperFactory::class,
            Zend\Db\Adapter\Adapter::class                            => Zend\Db\Adapter\AdapterServiceFactory::class,
            WebHemi\Acl\Rule\Table::class                             => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Acl\Role\Table::class                             => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Acl\Resource\Table::class                         => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Application\Table::class                          => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Client\Lock\Table::class                          => WebHemi\Factory\DbTableFactory::class,
            WebHemi\User\Table::class                                 => WebHemi\Factory\DbTableFactory::class,
            WebHemi\User\Role\Table::class                            => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Auth\Adapter::class                               => WebHemi\Factory\ServiceFactory::class,
            WebHemi\Auth\Storage\Session::class                       => WebHemi\Factory\ServiceFactory::class,
            Zend\Authentication\AuthenticationService::class          => WebHemi\Factory\ServiceFactory::class,
            WebHemi\Acl\AclMiddleware::class                          => WebHemi\Factory\MiddlewareFactory::class,
            WebHemi\Error\ErrorMiddleware::class                      => WebHemi\Factory\MiddlewareFactory::class,
        ],
        'service_factory' => [
            WebHemi\User\Table::class => [
              'class'     => WebHemi\User\Table::class,
              'arguments' => [Zend\Db\Adapter\Adapter::class]
            ],

            WebHemi\User\Role\Table::class => [
                'class'     => WebHemi\User\Role\Table::class,
                'arguments' => [Zend\Db\Adapter\Adapter::class]
            ],

            WebHemi\Application\Table::class => [
                'class'     => WebHemi\Application\Table::class,
                'arguments' => [Zend\Db\Adapter\Adapter::class]
            ],

            WebHemi\Client\Lock\Table::class => [
                'class'     => WebHemi\Client\Lock\Table::class,
                'arguments' => [Zend\Db\Adapter\Adapter::class]
            ],

            WebHemi\Acl\Rule\Table::class => [
                'class'     => WebHemi\Acl\Rule\Table::class,
                'arguments' => [Zend\Db\Adapter\Adapter::class]
            ],

            WebHemi\Acl\Role\Table::class => [
                'class'     => WebHemi\Acl\Role\Table::class,
                'arguments' => [Zend\Db\Adapter\Adapter::class]
            ],

            WebHemi\Acl\Resource\Table::class => [
                'class'     => WebHemi\Acl\Resource\Table::class,
                'arguments' => [Zend\Db\Adapter\Adapter::class]
            ],

            WebHemi\Auth\Adapter::class => [
                'class' => WebHemi\Auth\Adapter::class,
                'calls' => [
                    ['injectDependency' => [':userTable',        WebHemi\User\Table::class]],
                    ['injectDependency' => [':userRoleTable',    WebHemi\User\Role\Table::class]],
                    ['injectDependency' => [':aclRoleTable',     WebHemi\Acl\Role\Table::class]],
                    ['injectDependency' => [':applicationTable', WebHemi\Application\Table::class]],
                    ['injectDependency' => [':clientLockTable',  WebHemi\Client\Lock\Table::class]]
                ],
            ],

            WebHemi\Auth\Storage\Session::class => [
                'class' => WebHemi\Auth\Storage\Session::class,
                'calls' => [
                    ['injectDependency' => [':userTable', WebHemi\User\Table::class]]
                ]
            ],

            Zend\Authentication\AuthenticationService::class => [
                'class'     => Zend\Authentication\AuthenticationService::class,
                'arguments' => [WebHemi\Auth\Storage\Session::class, WebHemi\Auth\Adapter::class]
            ],

            WebHemi\Acl\AclMiddleware::class => [
                'class' => WebHemi\Acl\AclMiddleware::class,
                'calls' => [
                    ['injectDependency' => [':auth', Zend\Authentication\AuthenticationService::class]],
                ]
            ],

            WebHemi\Error\ErrorMiddleware::class => [
                'class'     => WebHemi\Error\ErrorMiddleware::class,
                'arguments' => [Zend\Expressive\Template\TemplateRendererInterface::class]
            ],
        ]
    ],
];
