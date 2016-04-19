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

namespace WebHemi\Db;

use Serializable;
use WebHemi\Application\DependencyInjectionInterface;

/**
 * Class AbstractEntity
 * @package WebHemi\Db
 * @codeCoverageIgnore
 */
abstract class AbstractEntity implements DependencyInjectionInterface, Serializable
{
    /**
     * Exchange array values into object properties.
     *
     * @param array $data
     *
     * @return AbstractEntity
     */
    abstract public function exchangeArray($data);

    /**
     * Exchange object properties into array
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Unserialize data from session.
     * Beware! The instance won't have the dependencies!
     *
     * @see WebHemi\Auth\Storage\Session::read()
     * @todo find a way to inject services
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->exchangeArray(unserialize($serialized));
    }

    /**
     * Display properties
     *
     * @return mixed
     */
    public function __debugInfo()
    {
        return get_object_vars($this);
    }

    /**
     * Injects a service into the class
     *
     * @param string $property
     * @param object $service
     * @return void
     */
    public function injectDependency($property, $service)
    {
        $this->{$property} = $service;
    }
}
