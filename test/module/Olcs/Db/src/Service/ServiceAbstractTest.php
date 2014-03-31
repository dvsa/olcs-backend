<?php

/**
 * Tests ServiceAbstract
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;
use Olcs\Db\Service\ServiceAbstract;

/**
 * Tests ServiceAbstract
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ServiceAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the service
     */
    protected function getMockService($methods = array())
    {
        $this->service = $this->getMockForAbstractClass(
            '\Olcs\Db\Service\ServiceAbstract',
            array(),
            '',
            true,
            true,
            true,
            // Mocked methods
            $methods
        );
    }

    /**
     * Test create
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testCreate()
    {
        $this->getMockService(array('log', 'getNewEntity', 'getDoctrineHydrator', 'dbPersist', 'dbFlush'));

        $data = array();

        $id = 7;

        $firstEntity = $this->getMock('\stdClass', array('getId'));

        $firstEntity->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $mockDoctrineHydrator = $this->getMock('\stdClass', array('hydrate'));

        $mockDoctrineHydrator->expects($this->once())
            ->method('hydrate')
            ->with($data, $firstEntity)
            ->will($this->returnValue($firstEntity));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getNewEntity')
            ->will($this->returnValue($firstEntity));

        $this->service->expects($this->once())
            ->method('getDoctrineHydrator')
            ->will($this->returnValue($mockDoctrineHydrator));

        $this->service->expects($this->once())
            ->method('dbPersist')
            ->with($firstEntity);

        $this->service->expects($this->once())
            ->method('dbFlush');

        $this->assertEquals($id, $this->service->create($data));
    }

    /**
     * Test get
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGet()
    {
        $this->getMockService(array('log', 'getDoctrineHydrator', 'getEntityById'));

        $id = 7;

        $data = array(
            'foo' => 'bar'
        );

        $mockEntity = $this->getMock('\stdClass');

        $mockDoctrineHydrator = $this->getMock('\stdClass', array('extract'));

        $mockDoctrineHydrator->expects($this->once())
            ->method('extract')
            ->with($mockEntity)
            ->will($this->returnValue($data));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getEntityById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('getDoctrineHydrator')
            ->will($this->returnValue($mockDoctrineHydrator));

        $this->assertEquals($data, $this->service->get($id));
    }

    /**
     * Test get
     *  with no entity
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetWithoutEntity()
    {
        $this->getMockService(array('log', 'getEntityById'));

        $id = 7;

        $data = array(
            'foo' => 'bar'
        );

        $mockEntity = null;

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getEntityById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->assertEquals(false, $this->service->get($id));
    }

    /**
     * Test getList
     *  Empty results
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetListEmptyResults()
    {
        $this->getMockService(array('log', 'getValidSearchFields', 'getEntityManager', 'getEntityName', 'canSoftDelete'));

        $data = array(
            'fooBar' => 'bar',
            'cakeBar' => 'bar',
            'barFor' => 'black sheep',
            'numberOfStuff' => 1
        );

        $searchableFields = array('fooBar', 'barFor', 'numberOfStuff', 'somethingElse');

        $expectedParams = array(
            'fooBar' => 'bar',
            'barFor' => 'black sheep',
            'numberOfStuff' => 1
        );

        $results = array();

        $expected = array(
            'Count' => 0,
            'Results' => array()
        );

        $mockEntity = $this->getMock('\stdClass', array(), array(), 'MockEntity');

        $mockEntityName = get_class($mockEntity);

        $mockQuery = $this->getMock('\stdClass', array('getResult'));

        $mockQuery->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($results));

        $mockQueryBuilder = $this->getMock('\stdClass', array('select', 'from', 'where', 'setParameters', 'getQuery'));

        $mockQueryBuilder->expects($this->once())
            ->method('select');

        $mockQueryBuilder->expects($this->once())
            ->method('from')
            ->with('mock_entity');

        $mockQueryBuilder->expects($this->at(2))
            ->method('where')
            ->with('a.foo_bar LIKE :fooBar');

        $mockQueryBuilder->expects($this->at(3))
            ->method('where')
            ->with('a.bar_for LIKE :barFor');

        $mockQueryBuilder->expects($this->at(4))
            ->method('where')
            ->with('a.number_of_stuff = :numberOfStuff');

        $mockQueryBuilder->expects($this->at(5))
            ->method('where')
            ->with('a.is_deleted = 0');

        $mockQueryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($expectedParams);

        $mockQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMock('\stdClass', array('createQueryBuilder'));

        $mockEntityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($mockQueryBuilder));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getValidSearchFields')
            ->will($this->returnValue($searchableFields));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->service->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue($mockEntityName));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->assertEquals($expected, $this->service->getList($data));
    }

    /**
     * Test getList
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetList()
    {
        $this->getMockService(array('log', 'getValidSearchFields', 'getEntityManager', 'getEntityName', 'canSoftDelete', 'getBundledHydrator'));

        $data = array(
            'fooBar' => 'bar',
            'cakeBar' => 'bar',
            'barFor' => 'black sheep',
            'numberOfStuff' => 1
        );

        $searchableFields = array('fooBar', 'barFor', 'numberOfStuff', 'somethingElse');

        $expectedParams = array(
            'fooBar' => 'bar',
            'barFor' => 'black sheep',
            'numberOfStuff' => 1
        );

        $results = array(
            array('foo' => 'bar'),
            array('bar' => 'log')
        );

        $expected = array(
            'Count' => 2,
            'Results' => $results
        );

        $mockBundleHydrator = $this->getMock('\stdClass', array('getTopLevelEntitiesFromNestedEntity'));

        $mockBundleHydrator->expects($this->once())
            ->method('getTopLevelEntitiesFromNestedEntity')
            ->with($results)
            ->will($this->returnValue($results));

        $mockEntity = $this->getMock('\stdClass', array(), array(), 'MockEntity');

        $mockEntityName = get_class($mockEntity);

        $mockQuery = $this->getMock('\stdClass', array('getResult'));

        $mockQuery->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($results));

        $mockQueryBuilder = $this->getMock('\stdClass', array('select', 'from', 'where', 'setParameters', 'getQuery'));

        $mockQueryBuilder->expects($this->once())
            ->method('select');

        $mockQueryBuilder->expects($this->once())
            ->method('from')
            ->with('mock_entity');

        $mockQueryBuilder->expects($this->at(2))
            ->method('where')
            ->with('a.foo_bar LIKE :fooBar');

        $mockQueryBuilder->expects($this->at(3))
            ->method('where')
            ->with('a.bar_for LIKE :barFor');

        $mockQueryBuilder->expects($this->at(4))
            ->method('where')
            ->with('a.number_of_stuff = :numberOfStuff');

        $mockQueryBuilder->expects($this->at(5))
            ->method('where')
            ->with('a.is_deleted = 0');

        $mockQueryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($expectedParams);

        $mockQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($mockQuery));

        $mockEntityManager = $this->getMock('\stdClass', array('createQueryBuilder'));

        $mockEntityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($mockQueryBuilder));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getValidSearchFields')
            ->will($this->returnValue($searchableFields));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->service->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue($mockEntityName));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->service->expects($this->once())
            ->method('getBundledHydrator')
            ->will($this->returnValue($mockBundleHydrator));

        $this->assertEquals($expected, $this->service->getList($data));
    }

    /**
     * Test Update
     *  Without version
     *
     * @expectedException \Olcs\Db\Exceptions\NoVersionException
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testUpdateWithoutVersion()
    {
        $this->getMockService(array('log'));

        $id = 7;

        $data = array(

        );

        $this->service->expects($this->once())
            ->method('log');

        $this->service->update($id, $data);
    }

    /**
     * Test Update
     *  With Version
     *  With Soft Delete
     *  Entity not found
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testUpdateWithVersionWithSoftDeleteEntityNotFound()
    {
        $this->getMockService(array('log', 'canSoftDelete', 'getUnDeletedById'));

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = null;

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->service->expects($this->once())
            ->method('getUnDeletedById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->assertFalse($this->service->update($id, $data));
    }

    /**
     * Test Update
     *  With Version
     *  Without Soft Delete
     *  Entity not found
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testUpdateWithVersionWithoutSoftDeleteEntityNotFound()
    {
        $this->getMockService(array('log', 'canSoftDelete', 'getEntityManager', 'getEntityName'));

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = null;

        $mockEntityManager = $this->getMock('\stdClass', array('find'));

        $mockEntityManager->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(false));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->assertFalse($this->service->update($id, $data));
    }

    /**
     * Test Update
     *  With Version
     *  With Soft Delete
     *  With Entity
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testUpdateWithVersionWithSoftDeleteWithEntity()
    {
        $this->getMockService(array('log', 'canSoftDelete', 'getUnDeletedById', 'getDoctrineHydrator', 'getEntityManager', 'dbPersist', 'dbFlush'));

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = $this->getMock('\stdClass');

        $mockHydrator = $this->getMock('\stdClass', array('hydrate'));

        $mockHydrator->expects($this->once())
            ->method('hydrate')
            ->with($data, $mockEntity)
            ->will($this->returnValue($mockEntity));

        $mockEntityManager = $this->getMock('\stdClass', array('lock'));

        $mockEntityManager->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->service->expects($this->once())
            ->method('getUnDeletedById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('getDoctrineHydrator')
            ->will($this->returnValue($mockHydrator));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->service->expects($this->once())
            ->method('dbPersist')
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('dbFlush');

        $this->assertTrue($this->service->update($id, $data));
    }

    /**
     * Test Patch
     *  Without version
     *
     * @expectedException \Olcs\Db\Exceptions\NoVersionException
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testPatchWithoutVersion()
    {
        $this->getMockService(array('log'));

        $id = 7;

        $data = array(

        );

        $this->service->expects($this->once())
            ->method('log');

        $this->service->patch($id, $data);
    }

    /**
     * Test Patch
     *  With Version
     *  With Soft Delete
     *  Entity not found
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testPatchWithVersionWithSoftDeleteEntityNotFound()
    {
        $this->getMockService(array('log', 'canSoftDelete', 'getUnDeletedById'));

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = null;

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->service->expects($this->once())
            ->method('getUnDeletedById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->assertFalse($this->service->patch($id, $data));
    }

    /**
     * Test Patch
     *  With Version
     *  Without Soft Delete
     *  Entity not found
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testPatchWithVersionWithoutSoftDeleteEntityNotFound()
    {
        $this->getMockService(array('log', 'canSoftDelete', 'getEntityManager', 'getEntityName'));

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = null;

        $mockEntityManager = $this->getMock('\stdClass', array('find'));

        $mockEntityManager->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(false));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->assertFalse($this->service->patch($id, $data));
    }

    /**
     * Test Patch
     *  With Version
     *  With Soft Delete
     *  With Entity
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testPatchWithVersionWithSoftDeleteWithEntity()
    {
        $this->getMockService(array('log', 'canSoftDelete', 'getUnDeletedById', 'getDoctrineHydrator', 'getEntityManager', 'dbPersist', 'dbFlush'));

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = $this->getMock('\stdClass');

        $mockHydrator = $this->getMock('\stdClass', array('hydrate'));

        $mockHydrator->expects($this->once())
            ->method('hydrate')
            ->with($data, $mockEntity)
            ->will($this->returnValue($mockEntity));

        $mockEntityManager = $this->getMock('\stdClass', array('lock'));

        $mockEntityManager->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->service->expects($this->once())
            ->method('getUnDeletedById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('getDoctrineHydrator')
            ->will($this->returnValue($mockHydrator));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->service->expects($this->once())
            ->method('dbPersist')
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('dbFlush');

        $this->assertTrue($this->service->patch($id, $data));
    }

    /**
     * Test Delete
     *  Without entity
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testDeleteWithoutEntity()
    {
        $this->getMockService(array('log', 'getEntityById'));

        $id = 7;

        $mockEntity = null;

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getEntityById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->assertFalse($this->service->delete($id));
    }

    /**
     * Test Delete
     *  With entity
     *  With soft delete
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testDeleteWithEntityWithSoftDelete()
    {
        $this->getMockService(array('log', 'getEntityById', 'canSoftDelete', 'dbPersist', 'dbFlush'));

        $id = 7;

        $mockEntity = $this->getMock('\stdClass', array('setIsDeleted'));

        $mockEntity->expects($this->once())
            ->method('setIsDeleted')
            ->with(true);

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getEntityById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->service->expects($this->once())
            ->method('dbPersist')
            ->with($mockEntity);

        $this->service->expects($this->once())
            ->method('dbFlush');

        $this->assertTrue($this->service->delete($id));
    }

    /**
     * Test Delete
     *  With entity
     *  Without soft delete
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testDeleteWithEntityWithoutSoftDelete()
    {
        $this->getMockService(array('log', 'getEntityById', 'canSoftDelete', 'getEntityManager', 'dbFlush'));

        $id = 7;

        $mockEntity = $this->getMock('\stdClass');

        $mockEntityManager = $this->getMock('\stdClass', array('remove'));

        $mockEntityManager->expects($this->once())
            ->method('remove')
            ->with($mockEntity);

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getEntityById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(false));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->service->expects($this->once())
            ->method('dbFlush');

        $this->assertTrue($this->service->delete($id));
    }

    /**
     * Test getDoctrineHydrator
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetDoctrineHydrator()
    {
        $this->getMockService(array('getEntityManager'));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\Orm\EntityManager')->disableOriginalConstructor()->getMock();

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $hydrator = $this->service->getDoctrineHydrator();

        $this->assertTrue($hydrator instanceof \DoctrineModule\Stdlib\Hydrator\DoctrineObject);
    }

    /**
     * Test getBundledHydrator
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetBundledHydrator()
    {
        $this->getMockService(array('getEntityManager'));

        $mockEntityManager = $this->getMockBuilder('\Doctrine\Orm\EntityManager')->disableOriginalConstructor()->getMock();

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $hydrator = $this->service->getBundledHydrator();

        $this->assertTrue($hydrator instanceof \OlcsEntities\Utility\BundleHydrator);
    }

    /**
     * Test getNewEntity
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetNewEntity()
    {
        $this->getMockService(array('getEntityName'));

        $mockEntity = $this->getMock('\stdClass', array(), array(), 'MockEntity');

        $className = get_class($mockEntity);

        $this->service->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue($className));

        $entity = $this->service->getNewEntity();

        $this->assertTrue($entity instanceof $className);
    }

    /**
     * Test getEntityName
     *  with entityName set
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetEntityNameWithEntityNameSet()
    {
        $this->getMockService();

        $this->service->setEntityName('BOB');

        $this->assertEquals('BOB', $this->service->getEntityName());
    }

    /**
     * Test getEntityName
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetEntityName()
    {
        $this->getMockService();

        $className = get_class($this->service);

        $this->assertEquals('\OlcsEntities\Entity\\' . $className, $this->service->getEntityName());
    }

    /**
     * Test canSoftDelete
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testCanSoftDelete()
    {
        $this->getMockService();

        $this->assertFalse($this->service->canSoftDelete());
    }

    /**
     * Test getUnDeletedById
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetUnDeletedById()
    {
        $this->getMockService(array('getEntityManager'));

        $id = 7;

        $return = array(
            'foo' => 'bar'
        );

        $mockRepository = $this->getMock('\stdClass', array('findOneBy'));

        $mockRepository->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $id, 'isDeleted' => 0))
            ->will($this->returnValue($return));

        $mockEntityManager = $this->getMock('\stdClass', array('getRepository'));

        $mockEntityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($mockRepository));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->assertEquals($return, $this->service->getUnDeletedById($id));
    }

    /**
     * Test getEntityById
     *  With soft delete
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetEntityByIdWithSoftDelete()
    {
        $this->getMockService(array('canSoftDelete', 'getUnDeletedById'));

        $id = 7;

        $mockEntity = array('foo' => 'bar');

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->service->expects($this->once())
            ->method('getUnDeletedById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

        $this->assertEquals($mockEntity, $this->service->getEntityById($id));
    }

    /**
     * Test getEntityById
     *  Without soft delete
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testGetEntityByIdWithoutSoftDelete()
    {
        $this->getMockService(array('canSoftDelete', 'getEntityManager'));

        $id = 7;

        $mockEntity = array('foo' => 'bar');

        $mockEntityManager = $this->getMock('\stdClass', array('find'));

        $mockEntityManager->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(false));

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $this->assertEquals($mockEntity, $this->service->getEntityById($id));
    }

    public function testGetService()
    {
        $this->getMockService(array('getServiceLocator'));

        $name = 'Bob';

        $mockServiceFactory = $this->getMock('\stdClass', array('getService'));

        $mockServiceFactory->expects($this->once())
            ->method('getService')
            ->with($name);

        $mockServiceLocator = $this->getMock('\stdClass', array('get'));

        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('serviceFactory')
            ->will($this->returnValue($mockServiceFactory));

        $this->service->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));

        $this->service->getService($name);
    }
}
