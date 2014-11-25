<?php

/**
 * Tests ServiceAbstract
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use OlcsTest\Bootstrap;

/**
 * Tests ServiceAbstract
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ServiceAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * SUT
     *
     * @var \Olcs\Db\Service\ServiceAbstract
     */
    protected $sut;

    protected $sm;

    protected $em;

    protected $mockLogger;

    protected function setUp()
    {
        $this->sut = $this->getMockForAbstractClass(
            '\Olcs\Db\Service\ServiceAbstract',
            array(),
            'Foo'
        );

        $this->mockLogger = $this->getMock('\Zend\Log\Logger', ['info']);
        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->getMock(
            '\Doctrine\ORM\EntityManager',
            ['persist', 'flush', 'getUnitOfWork', 'getClassMetadata', 'getMetadataFactory'],
            array(),
            '',
            false
        );

        $this->sut->setLogger($this->mockLogger);
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setEntityManager($this->em);
    }

    /**
     * @group service_abstract
     */
    public function testSetEntityName()
    {
        $entityName = 'FooBar';

        $this->sut->setEntityName($entityName);

        $this->assertEquals($entityName, $this->sut->getEntityName());
    }

    /**
     * @group service_abstract
     */
    public function testGetEntityName()
    {
        $this->assertEquals('\Olcs\Db\Entity\Foo', $this->sut->getEntityName());
    }

    /**
     * @group service_abstract
     */
    public function testCreateWithoutAddressData()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');

        $data = array(
            'foo' => 'bar'
        );

        $this->mockLogger->expects($this->once())->method('info');

        $mockDoctrineObject = $this->getMock('\stdClass', ['hydrate']);
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

        $mockHydratorManager = $this->getMock('\stdClass', ['get']);
        $mockHydratorManager->expects($this->once())
            ->method('get')
            ->with('DoctrineModule\Stdlib\Hydrator\DoctrineObject')
            ->will($this->returnValue($mockDoctrineObject));

        $this->sm->setAllowOverride(true);
        $this->sm->setService('HydratorManager', $mockHydratorManager);

        $this->em->expects($this->once())
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertNull($this->sut->create($data));
    }

    /**
     * @group service_abstract
     */
    public function testCreateWithAddressDataNotMatching()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');

        $data = array(
            'foo' => 'bar',
            'addresses' => array(
                'wrongKey' => array(
                    'addressLine1' => '123 Foo',
                    'addressLine2' => 'Bartown',
                    'postcode' => 'FO1BA'
                )
            )
        );

        $hydrationData = array(
            'foo' => 'bar'
        );

        $this->mockLogger->expects($this->once())->method('info');

        $mockDoctrineObject = $this->getMock('\stdClass', ['hydrate']);
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($hydrationData, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

        $mockHydratorManager = $this->getMock('\stdClass', ['get']);
        $mockHydratorManager->expects($this->once())
            ->method('get')
            ->with('DoctrineModule\Stdlib\Hydrator\DoctrineObject')
            ->will($this->returnValue($mockDoctrineObject));

        $this->sm->setAllowOverride(true);
        $this->sm->setService('HydratorManager', $mockHydratorManager);

        $this->em->expects($this->once())
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertNull($this->sut->create($data));
    }

    /**
     * @group service_abstract
     */
    public function testCreateWithAddressDataWithNewAddress()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');

        $addressId = 1;

        $addressData = array(
            'addressLine1' => '123 Foo',
            'addressLine2' => 'Bartown',
            'postcode' => 'FO1BA'
        );

        $data = array(
            'foo' => 'bar',
            'addresses' => array(
                'address' => $addressData
            )
        );

        $hydrationData = array(
            'foo' => 'bar',
            'address' => $addressId
        );

        $this->mockLogger->expects($this->once())->method('info');

        $mockDoctrineObject = $this->getMock('\stdClass', ['hydrate']);
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($hydrationData, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

        $mockHydratorManager = $this->getMock('\stdClass', ['get']);
        $mockHydratorManager->expects($this->once())
            ->method('get')
            ->with('DoctrineModule\Stdlib\Hydrator\DoctrineObject')
            ->will($this->returnValue($mockDoctrineObject));

        $mockAddressService = $this->getMock('\stdClass', ['create']);
        $mockAddressService->expects($this->once())
            ->method('create')
            ->with($addressData)
            ->will($this->returnValue($addressId));

        $mockServiceFactory = $this->getMock('\stdClass', ['getService']);
        $mockServiceFactory->expects($this->once())
            ->method('getService')
            ->with('Address')
            ->will($this->returnValue($mockAddressService));

        $this->sm->setAllowOverride(true);
        $this->sm->setService('HydratorManager', $mockHydratorManager);
        $this->sm->setService('serviceFactory', $mockServiceFactory);

        $this->em->expects($this->once())
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertNull($this->sut->create($data));
    }

    /**
     * @group service_abstract
     */
    public function testCreateWithAddressDataWithExistingAddress()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');

        $addressId = 7;

        $addressData = array(
            'id' => $addressId,
            'addressLine1' => '123 Foo',
            'addressLine2' => 'Bartown',
            'postcode' => 'FO1BA'
        );

        $data = array(
            'foo' => 'bar',
            'addresses' => array(
                'address' => $addressData
            )
        );

        $hydrationData = array(
            'foo' => 'bar',
            'address' => $addressId
        );

        $this->mockLogger->expects($this->once())->method('info');

        $mockDoctrineObject = $this->getMock('\stdClass', ['hydrate']);
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($hydrationData, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

        $mockHydratorManager = $this->getMock('\stdClass', ['get']);
        $mockHydratorManager->expects($this->once())
            ->method('get')
            ->with('DoctrineModule\Stdlib\Hydrator\DoctrineObject')
            ->will($this->returnValue($mockDoctrineObject));

        $mockAddressService = $this->getMock('\stdClass', ['update']);
        $mockAddressService->expects($this->once())
            ->method('update')
            ->with($addressId, $addressData);

        $mockServiceFactory = $this->getMock('\stdClass', ['getService']);
        $mockServiceFactory->expects($this->once())
            ->method('getService')
            ->with('Address')
            ->will($this->returnValue($mockAddressService));

        $this->sm->setAllowOverride(true);
        $this->sm->setService('HydratorManager', $mockHydratorManager);
        $this->sm->setService('serviceFactory', $mockServiceFactory);

        $this->em->expects($this->once())
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertNull($this->sut->create($data));
    }
}
