<?php
/**
 *
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
 *
 */

namespace WebHemi\Auth;

use Zend\Authentication\AuthenticationService as ZendAuthService;

/**
 * Class AuthenticationService
 * @package WebHemi\Auth
 *
 * @method Adapter getAdapter()
 * @method Storage\Session getStorage()
 */
class AuthenticationService extends ZendAuthService
{
    /**
     * AuthenticationService constructor.
     * @param Storage\Session|null $storage
     * @param Adapter|null $adapter
     */
    public function __construct(Storage\Session $storage = null, Adapter $adapter = null)
    {
        parent::__construct($storage, $adapter);
    }
}