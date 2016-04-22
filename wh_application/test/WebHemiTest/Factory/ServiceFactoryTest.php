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

use WebHemi\Factory\ServiceFactory;
use Zend\Expressive\Router\ZendRouter;
use Zend\Expressive\Router\RouterInterface;
use WebHemiTest\Fixtures\ContainerTrait;
use WebHemiTest\Fixtures\GetConfigTrait;
use WebHemiTest\Fixtures\PropertyClass;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use ArrayObject;
use InvalidArgumentException;

/**
 * Class ServiceFactoryTest
 * @package WebHemiTest\Factory
 */
class ServiceFactoryTest extends TestCase
{
    use GetConfigTrait;
    use ContainerTrait;

    /** @var ObjectProphecy */
    protected $container;

    /**
     * Set up unit test
     */
    protected function setUp()
    {
        $this->container = $this->mockContainerInterface();
    }

    /**
     * Test factory for throwing exception
     */
    public function testFactoryForNonExistingClass()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $config = [
            'dependencies' => [
                'service_factory' => []
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();
        $factory($this->container->reveal(), 'SomeNonExistingClassForTheTest');
    }

    /**
     * Test factory for instantiate a non-service class
     */
    public function testFactoryForNonServiceClass()
    {
        $config = [
            'dependencies' => [
                'service_factory' => []
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();
        $result = $factory($this->container->reveal(), PropertyClass::class);

        $this->assertInstanceOf(PropertyClass::class, $result);
    }

    /**
     * Test factory with aliased service name
     */
    public function testFactoryWithAlias()
    {
        $config = [
            'dependencies' => [
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();
        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
    }

    /**
     * Test factory with aliased service name which not exists
     */
    public function testFactoryWithNonExistingAlias()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $config = [
            'dependencies' => [
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => 'SomeNonExistingClassForTheTest'
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $factory($this->container->reveal(), 'serviceAlias');
    }

    /**
     * Test factory for service constructor where the parameter is a class
     */
    public function testFactoryForClassArguments()
    {
        $config = [
            'dependencies' => [
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'arguments' => [ArrayObject::class]
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertAttributeInstanceOf(ArrayObject::class, 'prop', $result);
    }

    /**
     * Test factory for service constructor where the parameter is an other service
     */
    public function testFactoryForServiceArguments()
    {
        $router = $this->prophesize(ZendRouter::class);
        $this->injectServiceInContainer($this->container, RouterInterface::class, $router->reveal());

        $config = [
            'dependencies' => [
                'invokables' => [
                    RouterInterface::class => ZendRouter::class,
                ],
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'arguments' => [RouterInterface::class]
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertAttributeInstanceOf(ZendRouter::class, 'prop', $result);
    }

    /**
     * Test factory for service constructor where the parameter is a scalar
     */
    public function testFactoryForScalarArguments()
    {
        $config = [
            'dependencies' => [
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'arguments' => ['1999-12-12 23:59:59']
                    ]
                ]
            ]
        ];

        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertEquals('1999-12-12 23:59:59', $result->prop);
    }

    /**
     * Test factory for service constructor where the parameter is a scalar that also a className
     */
    public function testFactoryForForcedScalarArguments()
    {
        $router = $this->prophesize(ZendRouter::class);
        $this->injectServiceInContainer($this->container, RouterInterface::class, $router->reveal());

        $config = [
            'dependencies' => [
                'invokables' => [
                    RouterInterface::class => ZendRouter::class,
                ],
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'arguments' => [':' . RouterInterface::class]
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertAttributeNotInstanceOf(ZendRouter::class, 'prop', $result);
        $this->assertAttributeNotInstanceOf(RouterInterface::class, 'prop', $result);
        $this->assertEquals(RouterInterface::class, $result->prop);
    }

    /**
     * Test factory for calling a non existing method on the service instance
     */
    public function testFactoryForCallServiceMethodNotExists()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $config = [
            'dependencies' => [
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'calls' => [
                            ['someMethodThatDoesNotExists' => []]
                        ]
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $factory($this->container->reveal(), 'serviceAlias');
    }

    /**
     * Test factory for calling a method on the service instance with no parameters
     */
    public function testFactoryForCallServiceMethodNoParam()
    {
        $config = [
            'dependencies' => [
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'calls' => [
                            ['setProperty' => []]
                        ]
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertNull($result->prop);
    }

    /**
     * Test factory for calling a method on the service instance with a parameter that is a classname
     */
    public function testFactoryForCallServiceMethodParamIsClass()
    {
        $config = [
            'dependencies' => [
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'calls' => [
                            ['setProperty' => [ArrayObject::class]]
                        ]
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertAttributeInstanceOf(ArrayObject::class, 'prop', $result);
    }

    /**
     * Test factory for calling a method on the service instance with a parameter that is a service
     */
    public function testFactoryForCallServiceMethodParamIsService()
    {
        $router = $this->prophesize(ZendRouter::class);
        $this->injectServiceInContainer($this->container, RouterInterface::class, $router->reveal());

        $config = [
            'dependencies' => [
                'invokables' => [
                    RouterInterface::class => ZendRouter::class,
                ],
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'calls' => [
                            ['setProperty' => [RouterInterface::class]]
                        ]
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertAttributeInstanceOf(ZendRouter::class, 'prop', $result);
    }

    /**
     * Test factory for calling a method on the service instance with a parameter that is a scalar
     */
    public function testFactoryForCallServiceMethodParamIsScalar()
    {
        $config = [
            'dependencies' => [
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'calls' => [
                            ['setProperty' => ['1999-12-12 23:59:59']]
                        ]
                    ]
                ]
            ]
        ];

        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertEquals('1999-12-12 23:59:59', $result->prop);
    }

    /**
     * Test factory for calling a method on the service instance with a parameter that is a forced scalar which is a service name too
     */
    public function testFactoryForCallServiceMethodParamIsForcedScalar()
    {
        $router = $this->prophesize(ZendRouter::class);
        $this->injectServiceInContainer($this->container, RouterInterface::class, $router->reveal());

        $config = [
            'dependencies' => [
                'invokables' => [
                    RouterInterface::class => ZendRouter::class,
                ],
                'service_factory' => [
                    'serviceAlias' => [
                        'class' => PropertyClass::class,
                        'calls' => [
                            ['setProperty' => [':' . RouterInterface::class]]
                        ]
                    ]
                ]
            ]
        ];
        $config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
        $this->injectServiceInContainer($this->container, 'config', $config);

        $factory = new ServiceFactory();

        $result = $factory($this->container->reveal(), 'serviceAlias');

        $this->assertInstanceOf(PropertyClass::class, $result);
        $this->assertAttributeNotInstanceOf(ZendRouter::class, 'prop', $result);
        $this->assertAttributeNotInstanceOf(RouterInterface::class, 'prop', $result);
        $this->assertEquals(RouterInterface::class, $result->prop);
    }
}
