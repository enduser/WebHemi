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

use Zend\Db\Adapter;

return [
    'dependencies' => [
        'factories' => [
            Adapter\Adapter::class => Adapter\AdapterServiceFactory::class,
            WebHemi\User\Table::class => WebHemi\Factory\DbTableFactory::class,
            WebHemi\User\Role\Table::class => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Application\Table::class => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Client\Lock\Table::class => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Acl\Rule\Table::class => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Acl\Role\Table::class => WebHemi\Factory\DbTableFactory::class,
            WebHemi\Acl\Resource\Table::class => WebHemi\Factory\DbTableFactory::class,
        ],
    ],
    'db' => [
        'driver'   => 'Pdo',
        'dsn'      => 'mysql:dbname=webhemi;charset=utf8;hostname=127.0.0.1',
        'user'     => 'username',
        'password' => 'password'
    ],
];
