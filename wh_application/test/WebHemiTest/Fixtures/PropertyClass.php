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

namespace WebHemiTest\Fixtures;

use Zend\Diactoros\Response;

/**
 * Class PropertyClass
 * @package WebHemiTest\Fixtures
 */
class PropertyClass
{
    /** @var  mixed */
    public $prop;

    /**
     * PropertyClass constructor.
     * @param mixed $prop
     */
    public function __construct($prop = null)
    {
        $this->prop = $prop;
    }

    /**
     * @return bool
     */
    public function __invoke()
    {
        return ':)';
    }

    /**
     * @param mixed $prop
     */
    public function setProperty($prop = null)
    {
        $this->prop = $prop;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAnyProperty($name, $value)
    {
        $this->{$name} = $value;
    }
}
