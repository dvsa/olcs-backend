<?php

/**
 * Tests Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Db\Service\Factory;

/**
 * Tests Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FactoryTest extends MockeryTestCase
{

    /**
     * Test createService
     *
     * @group Service
     * @group Factory
     */
    public function testCreateService()
    {
        $serviceManager = $this->getMockBuilder(
            '\Zend\ServiceManager\ServiceManager'
        )->disableOriginalConstructor()->getMock();

        $factory = new Factory();

        $this->assertEquals($factory, $factory->createService($serviceManager));
    }

    /**
     * Test getServiceClassName
     *
     * @group Service
     * @group Factory
     */
    public function testGetServiceClassName()
    {
        $factory = new Factory();

        $this->assertEquals('\Olcs\Db\Service\\Test', $factory->getServiceClassName('Test'));
    }

    /**
     * Test getService
     *  Non existant Service
     *
     * @group Service
     * @group Factory
     */
    public function testGetServiceMissingService()
    {
        $missingServiceName = 'Missing';

        $mockEntityManager = $this->createMock('\stdClass');

        $serviceManager = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $serviceManager->setService('Config', ['entity_namespaces' => ['Missing' => 'Missing']]);
        $serviceManager->setService('doctrine.entitymanager.orm_default', $mockEntityManager);

        $mockGenericService = $this->createPartialMock(
            \stdClass::class,
            array('setEntityName', 'setEntityManager', 'setServiceLocator')
        );

        $mockGenericName = get_class($mockGenericService);

        $factory = $this->createPartialMock('\Olcs\Db\Service\Factory', array('getServiceClassName'));

        $factory->expects($this->at(0))
            ->method('getServiceClassName')
            ->with($missingServiceName)
            ->will($this->returnValue('MissingClassName'));

        $factory->expects($this->at(1))
            ->method('getServiceClassName')
            ->with('Generic')
            ->will($this->returnValue($mockGenericName));

        $factory->createService($serviceManager);

        $service = $factory->getService($missingServiceName);

        $this->assertTrue($service instanceof $mockGenericName);
    }

    /**
     * Test getService
     *
     * @group Service
     * @group Factory
     */
    public function testGetService()
    {
        $mockService = $this->createPartialMock('\stdClass', array('setEntityManager', 'setServiceLocator'));

        $className = get_class($mockService);

        $mockEntityManager = $this->createMock('\stdClass');

        $serviceManager = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $serviceManager->setService('Config', ['entity_namespaces' => []]);
        $serviceManager->setService('doctrine.entitymanager.orm_default', $mockEntityManager);

        $factory = $this->createPartialMock('\Olcs\Db\Service\Factory', array('getServiceClassName'));

        $factory->expects($this->once())
            ->method('getServiceClassName')
            ->will($this->returnValue($className));

        $factory->createService($serviceManager);

        $service = $factory->getService('MockService');

        $this->assertTrue($service instanceof $className);
    }
}
