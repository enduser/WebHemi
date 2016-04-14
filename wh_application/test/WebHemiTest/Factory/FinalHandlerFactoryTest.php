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

namespace WebHemiTest\Factory;

use WebHemi\Factory\FinalHandlerFactory;
use WebHemi\Error\ErrorHandler;
use WebHemiTest\Fixtures\ContainerTrait;
use Zend\Expressive\Template\TemplateRendererInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class FinalHandlerFactoryTest
 * @package WebHemiTest\Factory
 */
class FinalHandlerFactoryTest extends TestCase
{
    use ContainerTrait;

    /** @var ObjectProphecy */
    protected $container;

    /** @var  FinalHandlerFactory */
    protected $factory;

    /**
     * Set up unit test
     */
    protected function setUp()
    {
        $this->container = $this->mockContainerInterface();
        $this->factory   = new FinalHandlerFactory();
    }

    /**
     * Test the factory returns with the proper instance
     */
    public function testReturn()
    {
        $factory = $this->factory;
        $result  = $factory($this->container->reveal());
        $this->assertInstanceOf(ErrorHandler::class, $result);
    }

    /**
     * Test the factory as a service
     */
    public function testService()
    {
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $this->injectServiceInContainer($this->container, TemplateRendererInterface::class, $renderer->reveal());

        $factory = $this->factory;
        $result  = $factory($this->container->reveal());

        $this->assertInstanceOf(ErrorHandler::class, $result);
        $this->assertAttributeInstanceOf(TemplateRendererInterface::class, 'templateRenderer', $result);
    }
}
