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
        'layout' => 'layout/default',
        'map' => [
            'layout/default' => 'wh_application/templates/default_theme/view/layout/default.phtml',
            'error/error'    => 'wh_application/templates/default_theme/view/error/error.phtml',
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