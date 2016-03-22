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
    'applications' => [
        'Admin' => [
            // The default type is "subdir". "Subdomain" only when vhost supports it.
            'type' => 'subdomain',
            'path' => 'admin',
            'theme' => 'default',
        ],
        'Website' => [
            // The only supported type for this application is "subdomain".
            'type' => 'subdomain',
            'path' => 'www',
            'theme' => 'default',
        ],
    ]
];