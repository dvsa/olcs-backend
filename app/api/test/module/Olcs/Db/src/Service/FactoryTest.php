<?php

/**
 * Tests Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\Factory;

/**
 * Tests Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test createService
     *
     * @group Service
     * @group Factory
     */
    public function testCreateService()
    {
        $serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();

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
    public function testGetServiceInvalidService()
    {
        $serviceManager = $this->getMockBuilder(
            '\Zend\ServiceManager\ServiceManager'
        )->disableOriginalConstructor()->getMock();

        $factory = new Factory();

        $factory->createService($serviceManager);

        $this->assertFalse($factory->getService('Baopsdfjodsjflkdsjfl'));
    }

    /**
     * Test getService
     *
     * @group Service
     * @group Factory
     */
    public function testGetService()
    {
        $mockService = $this->getMock('MockService', array('setEntityManager', 'setServiceLocator'));

        $className = get_class($mockService);

        $mockEntityManager = $this->getMock('\stdClass');

        $serviceManager = $this->getMockBuilder(
            '\Zend\ServiceManager\ServiceManager',
            array('get')
        )->disableOriginalConstructor()->getMock();

        $serviceManager->expects($this->once())
            ->method('get')
            ->will($this->returnValue($mockEntityManager));

        $factory = $this->getMock('\Olcs\Db\Service\Factory', array('getServiceClassName'));

        $factory->expects($this->once())
            ->method('getServiceClassName')
            ->will($this->returnValue($className));

        $factory->createService($serviceManager);

        $service = $factory->getService('MockService');

        $this->assertTrue($service instanceof $className);
    }
}
