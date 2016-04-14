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

use Interop\Container\ContainerInterface;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

trait ContainerTrait
{
    /**
     * @return mixed
     */
    protected function mockContainerInterface()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has(Argument::type('string'))->willReturn(false);
        return $container;
    }

    /**
     * @param ObjectProphecy $container
     * @param string $serviceName
     * @param mixed $service
     */
    protected function injectServiceInContainer(ObjectProphecy $container, $serviceName, $service)
    {
        $container->has($serviceName)->willReturn(true);
        $container->get($serviceName)->willReturn($service);
    }
}
