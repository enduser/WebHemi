<?php

return [
    'dependencies' => [
        'factories' => [
            'Zend\Expressive\FinalHandler' =>
                Zend\Expressive\Container\TemplatedErrorHandlerFactory::class,

            Zend\Expressive\Template\TemplateRendererInterface::class =>
                Zend\Expressive\ZendView\ZendViewRendererFactory::class,

            Zend\View\HelperPluginManager::class =>
                Zend\Expressive\ZendView\HelperPluginManagerFactory::class,
        ],
    ],

    'templates' => [
        'layout' => 'layout/layout',
        'map' => [
            'layout/layout' => 'wh_application/templates/default_theme/view/layout/layout.phtml',
            'error/error'    => 'wh_application/templates/default_theme/view/error/500.phtml',
            'error/401'      => 'wh_application/templates/default_theme/view/error/401.phtml',
            'error/403'      => 'wh_application/templates/default_theme/view/error/403.phtml',
            'error/404'      => 'wh_application/templates/default_theme/view/error/404.phtml',
        ],
        'paths' => [
            'web-hemi' => ['wh_application/templates/default_theme/view/web-hemi'],
            'layout'   => ['wh_application/templates/default_theme/view/layout'],
            'error'    => ['wh_application/templates/default_theme/view/error'],
        ],
    ],

    'view_helpers' => [
        // zend-servicemanager-style configuration for adding view helpers:
        // - 'aliases'
        // - 'invokables'
        // - 'factories'
        // - 'abstract_factories'
        // - etc.
    ],
];
