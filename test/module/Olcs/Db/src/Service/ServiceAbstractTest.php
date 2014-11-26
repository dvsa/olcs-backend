<?php

/**
 * Tests ServiceAbstract
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use OlcsTest\Bootstrap;
use Mockery as m;

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
        $this->sm->setAllowOverride(true);
        $this->em = $this->getMock(
            '\Doctrine\ORM\EntityManager',
            [
                'persist',
                'flush',
                'getUnitOfWork',
                'getClassMetadata',
                'getMetadataFactory',
                'createQueryBuilder',
                'find',
                'lock',
                'remove'
            ],
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

        $mockDoctrineObject = $this->mockHydrator();

        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

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

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($hydrationData, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

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

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($hydrationData, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

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

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($hydrationData, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

        $mockAddressService = $this->getMock('\stdClass', ['update']);
        $mockAddressService->expects($this->once())
            ->method('update')
            ->with($addressId, $addressData);

        $mockServiceFactory = $this->getMock('\stdClass', ['getService']);
        $mockServiceFactory->expects($this->once())
            ->method('getService')
            ->with('Address')
            ->will($this->returnValue($mockAddressService));

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
    public function testGetWithoutBundleWithoutResult()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $id = 7;
        $data = array();
        $expectedParms = array(
            'foo' => 'bar'
        );

        $this->mockLogger->expects($this->once())->method('info');

        $mockQuery = m::mock()
            ->shouldReceive('getArrayResult')
            ->andReturn(null)
            ->getMock();

        $mockQueryBuilder = m::mock()
            ->shouldReceive('select')
            ->with(array('m'))
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', 'm')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('WHERE CLAUSE')
            ->andReturnSelf()
            ->shouldReceive('setParameters')
            ->with($expectedParms)
            ->andReturnSelf()
            ->shouldReceive('getQuery')
            ->andReturn($mockQuery)
            ->getMock();

        $this->em->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($mockQueryBuilder));

        $mockExpressionBuilder = m::mock()
            ->shouldReceive('setQueryBuilder')
            ->with($mockQueryBuilder)
            ->shouldReceive('setEntityManager')
            ->with($this->em)
            ->shouldReceive('setEntity')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub')
            ->shouldReceive('setParams')
            ->with(array())
            ->shouldReceive('buildWhereExpression')
            ->with(array('id' => $id), 'm')
            ->andReturn('WHERE CLAUSE')
            ->shouldReceive('getParams')
            ->andReturn($expectedParms)
            ->getMock();

        $this->sm->setService('ExpressionBuilder', $mockExpressionBuilder);

        $this->assertNull($this->sut->get($id, $data));
    }

    /**
     * @group service_abstract
     */
    public function testGetWithoutBundleWithResult()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $id = 7;
        $data = array();
        $expectedParms = array(
            'foo' => 'bar'
        );
        $expectedResult = array(
            'id' => 7,
            'name' => 'foo'
        );

        $this->mockLogger->expects($this->once())->method('info');

        $mockQuery = m::mock()
            ->shouldReceive('getArrayResult')
            ->andReturn(array($expectedResult))
            ->getMock();

        $mockQueryBuilder = m::mock()
            ->shouldReceive('select')
            ->with(array('m'))
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', 'm')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('WHERE CLAUSE')
            ->andReturnSelf()
            ->shouldReceive('setParameters')
            ->with($expectedParms)
            ->andReturnSelf()
            ->shouldReceive('getQuery')
            ->andReturn($mockQuery)
            ->getMock();

        $this->em->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($mockQueryBuilder));

        $mockExpressionBuilder = m::mock()
            ->shouldReceive('setQueryBuilder')
            ->with($mockQueryBuilder)
            ->shouldReceive('setEntityManager')
            ->with($this->em)
            ->shouldReceive('setEntity')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub')
            ->shouldReceive('setParams')
            ->with(array())
            ->shouldReceive('buildWhereExpression')
            ->with(array('id' => $id), 'm')
            ->andReturn('WHERE CLAUSE')
            ->shouldReceive('getParams')
            ->andReturn($expectedParms)
            ->getMock();

        $this->sm->setService('ExpressionBuilder', $mockExpressionBuilder);

        $this->assertEquals($expectedResult, $this->sut->get($id, $data));
    }

    /**
     * @group service_abstract
     * @expectedException \Exception
     */
    public function testGetWithBundleWithInvalidJson()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $id = 7;
        $data = array(
            'bundle' => '[INVALID JSON]'
        );

        $this->mockLogger->expects($this->once())->method('info');

        $this->sut->get($id, $data);
    }

    /**
     * @group service_abstract
     */
    public function testGetWithBundle()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $id = 7;

        $bundleConfig = array(
            'foo' => 'cake'
        );

        $data = array('bundle' => json_encode($bundleConfig));

        $expectedParms = array(
            'foo' => 'bar'
        );

        $expectedResult = array(
            'id' => 7,
            'name' => 'foo'
        );

        $this->mockLogger->expects($this->once())->method('info');

        $mockQuery = m::mock()
            ->shouldReceive('getArrayResult')
            ->andReturn(array($expectedResult))
            ->getMock();

        $mockQueryBuilder = m::mock()
            ->shouldReceive('select')
            ->with(array('m'))
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', 'm')
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('WHERE CLAUSE')
            ->andReturnSelf()
            ->shouldReceive('setParameters')
            ->with($expectedParms)
            ->andReturnSelf()
            ->shouldReceive('getQuery')
            ->andReturn($mockQuery)
            ->getMock();

        $this->em->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($mockQueryBuilder));

        $mockExpressionBuilder = m::mock()
            ->shouldReceive('setQueryBuilder')
            ->with($mockQueryBuilder)
            ->shouldReceive('setEntityManager')
            ->with($this->em)
            ->shouldReceive('setEntity')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub')
            ->shouldReceive('setParams')
            ->with($expectedParms)
            ->shouldReceive('buildWhereExpression')
            ->with(array('id' => $id), 'm')
            ->andReturn('WHERE CLAUSE')
            ->shouldReceive('getParams')
            ->andReturn($expectedParms)
            ->getMock();

        $this->sm->setService('ExpressionBuilder', $mockExpressionBuilder);

        $mockBundleQuery = m::mock()
            ->shouldReceive('setQueryBuilder')
            ->with($mockQueryBuilder)
            ->shouldReceive('build')
            ->with($bundleConfig)
            ->shouldReceive('getParams')
            ->andReturn($expectedParms)
            ->getMock();

        $this->sm->setService('BundleQuery', $mockBundleQuery);

        $this->assertEquals($expectedResult, $this->sut->get($id, $data));
    }

    /**
     * @group service_abstract
     */
    public function testDeleteWithoutEntity()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $id = 5;

        $this->mockLogger->expects($this->once())->method('info');

        $this->em->expects($this->once())
            ->method('find')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', $id)
            ->will($this->returnValue(false));

        $this->assertFalse($this->sut->delete($id));
    }

    /**
     * @group service_abstract
     */
    public function testDelete()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $id = 5;

        $this->mockLogger->expects($this->once())->method('info');

        $mockEntity = m::mock();

        $this->em->expects($this->once())
            ->method('find')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', $id)
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('remove')
            ->with($mockEntity);

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->sut->delete($id));
    }

    /**
     * @group service_abstract
     * @expectedException \Olcs\Db\Exceptions\NoVersionException
     */
    public function testUpdateWithoutVersion()
    {
        $id = 7;

        $data = array();

        $this->mockLogger->expects($this->once())->method('info');

        $this->sut->update($id, $data);
    }

    /**
     * @group service_abstract
     */
    public function testUpdateWithVersionEntityNotFound()
    {
        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = null;

        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->mockLogger->expects($this->once())->method('info');

        $this->assertFalse($this->sut->update($id, $data));
    }

    /**
     * @group service_abstract
     */
    public function testUpdateWithVersionWithEntity()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stub\EntityStub');

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = $this->getMock('\OlcsTest\Db\Service\Stub\EntityStub', array('clearProperties'));
        $mockEntity->expects($this->once())->method('clearProperties');

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf('\OlcsTest\Db\Service\Stub\EntityStub'))
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));
        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->mockLogger->expects($this->once())->method('info');

        $this->em->expects($this->once())
            ->method('persist')
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->sut->update($id, $data));
    }

    /**
     * @group service_abstract
     */
    public function testPatchWithVersionWithEntity()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stub\EntityStub');

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = $this->getMock('\OlcsTest\Db\Service\Stub\EntityStub', array('clearProperties'));
        $mockEntity->expects($this->once())
            ->method('clearProperties');

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf('\OlcsTest\Db\Service\Stub\EntityStub'))
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));
        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->mockLogger->expects($this->once())->method('info');

        $this->em->expects($this->once())
            ->method('persist')
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->sut->patch($id, $data));
    }

    /**
     * @group service_abstract
     * @expectedException \Olcs\Db\Exceptions\NoVersionException
     */
    public function testPatchWithoutVersion()
    {
        $id = 7;

        $data = array(
        );

        $this->mockLogger->expects($this->once())->method('info');

        $this->sut->patch($id, $data);
    }

    /**
     * @group service_abstract
     */
    public function testPatchWithVersionEntityNotFound()
    {
        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = null;

        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->mockLogger->expects($this->once())->method('info');

        $this->assertFalse($this->sut->patch($id, $data));
    }

    protected function mockHydrator()
    {
        $mockDoctrineObject = $this->getMock('\stdClass', ['hydrate']);

        $mockHydratorManager = $this->getMock('\stdClass', ['get']);
        $mockHydratorManager->expects($this->once())
            ->method('get')
            ->with('DoctrineModule\Stdlib\Hydrator\DoctrineObject')
            ->will($this->returnValue($mockDoctrineObject));

        $this->sm->setService('HydratorManager', $mockHydratorManager);

        return $mockDoctrineObject;
    }
}
