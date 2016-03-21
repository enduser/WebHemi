<?php

use Zend\Db\Adapter;

return [
    'dependencies' => [
        'factories' => [
            Adapter\Adapter::class => Adapter\AdapterServiceFactory::class,
        ],
    ],
    'db' => [
        'driver'   => 'Pdo',
        'dsn'      => 'mysql:dbname=webhemi;charset=utf8;hostname=127.0.0.1',
        'user'     => 'username',
        'password' => 'password'
    ],
];
