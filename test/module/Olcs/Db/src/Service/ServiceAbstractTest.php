<?php

/**
 * Tests ServiceAbstract
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use PHPUnit_Framework_TestCase;

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
            '\Olcs\Db\Service\ServiceAbstract', array(), '', true, true, true,
            // Mocked methods
            $methods
        );
    }

    /**
     * Helper to generate stubbed entity properties; as we use reflection
     * these are objects themselves so need their getName method mocking
     *
     * @param array $properties
     *
     * @return array
     */
    protected function generateProperties($properties = array())
    {
        $final = [];
        foreach ($properties as $property) {
            $mock = $this->getMock('\stdClass', ['getName']);
            $mock->expects($this->once())
                ->method('getName')
                ->will($this->returnValue($property));

            $final[] = $mock;
        }

        return $final;
    }

    public function testGetPaginator()
    {
        $this->getMockService();

        $this->assertInstanceOf('\Doctrine\ORM\Tools\Pagination\Paginator', $this->service->getPaginator('foo'));
    }

    /**
     * Tests that the get pagination method gives us only the required fields.
     *
     * @dataProvider dpTestGetPaginationValues
     */
    public function testGetPaginationValues($input, $output)
    {
        $this->getMockService();

        $this->assertEquals($output, $this->service->getPaginationValues($input));
    }

    public function dpTestGetPaginationValues()
    {
        return array(
            array(
                array(
                    'page' => 1, 'limit' => 100, 'sort' => 'somecolumn', 'order' => 'asc', 'other' => 'ovalue'
                ),
                array(
                    'page' => 1, 'limit' => 100, 'sort' => 'somecolumn', 'order' => 'asc'
                ),
                array(
                    'page' => 1, 'limit' => 100, 'other' => 'ovalue'
                ),
                array(
                    'page' => 1, 'limit' => 100
                ),
            ),
        );
    }

    /**
     * Tests that the get order by method gives us only the required fields.
     *
     * @dataProvider dpTestGetOrderByValues
     */
    public function testGetOrderByValues($input, $output)
    {
        $this->getMockService();

        $this->assertEquals($output, $this->service->getOrderByValues($input));
    }

    public function dpTestGetOrderByValues()
    {
        return array(
            array(
                array(
                    'sort' => 'somecolumn', 'order' => 'asc', 'extra' => 'ignored'
                ),
                array(
                    'sort' => 'somecolumn', 'order' => 'asc'
                )
            ),
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
        $this->getMockService(
            array(
                'log', 'getNewEntity', 'getDoctrineHydrator',
                'dbPersist', 'dbFlush', 'getEntityPropertyNames'
            )
        );

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

        $this->service->expects($this->once())
            ->method('getEntityPropertyNames')
            ->will($this->returnValue([]));

        $this->assertEquals($id, $this->service->create($data));
    }

    /**
     * Test create With new Address
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testCreateWithNewAddress()
    {
        $this->getMockService(
            array(
                'log', 'getNewEntity', 'getDoctrineHydrator',
                'dbPersist', 'dbFlush', 'getService', 'getEntityPropertyNames'
            )
        );

        $data = array(
            'addresses' => array(
                'address' => array(
                )
            )
        );

        $expected = array(
            'address' => 1
        );

        $id = 7;

        $firstEntity = $this->getMock('\stdClass', array('getId'));

        $firstEntity->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $mockDoctrineHydrator = $this->getMock('\stdClass', array('hydrate'));

        $mockDoctrineHydrator->expects($this->once())
            ->method('hydrate')
            ->with($expected, $firstEntity)
            ->will($this->returnValue($firstEntity));

        $mockAddressService = $this->getMock('\stdClass', array('create'));

        $mockAddressService->expects($this->once())
            ->method('create')
            ->will($this->returnValue(1));

        $this->service->expects($this->once())
            ->method('getService')
            ->with('Address')
            ->will($this->returnValue($mockAddressService));

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

        $this->service->expects($this->once())
            ->method('getEntityPropertyNames')
            ->will($this->returnValue(['address']));

        $this->assertEquals($id, $this->service->create($data));
    }

    /**
     * Test create With Existing Address
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testCreateWithExistingAddress()
    {
        $this->getMockService(
            array(
                'log', 'getNewEntity', 'getDoctrineHydrator',
                'dbPersist', 'dbFlush', 'getService', 'getEntityPropertyNames'
            )
        );

        $data = array(
            'addresses' => array(
                'address' => array(
                    'id' => 3
                )
            )
        );

        $expected = array(
            'address' => 3
        );

        $id = 7;

        $firstEntity = $this->getMock('\stdClass', array('getId'));

        $firstEntity->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $mockDoctrineHydrator = $this->getMock('\stdClass', array('hydrate'));

        $mockDoctrineHydrator->expects($this->once())
            ->method('hydrate')
            ->with($expected, $firstEntity)
            ->will($this->returnValue($firstEntity));

        $mockAddressService = $this->getMock('\stdClass', array('update'));

        $mockAddressService->expects($this->once())
            ->method('update');

        $this->service->expects($this->once())
            ->method('getService')
            ->with('Address')
            ->will($this->returnValue($mockAddressService));

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

        $this->service->expects($this->once())
            ->method('getEntityPropertyNames')
            ->will($this->returnValue(['address']));

        $this->assertEquals($id, $this->service->create($data));
    }

    /**
     * Test create With new Address which is not an entity property
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testCreateWithNewAddressInvalidEntityProperty()
    {
        $this->getMockService(
            array(
                'log', 'getNewEntity', 'getDoctrineHydrator',
                'dbPersist', 'dbFlush', 'getService', 'getEntityPropertyNames'
            )
        );

        $data = array(
            'validProperty' => 'valid',
            'addresses' => array(
                'address' => array(
                )
            )
        );

        $expected = array(
            'validProperty' => 'valid',
        );

        $id = 7;

        $firstEntity = $this->getMock('\stdClass', array('getId'));

        $firstEntity->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $mockDoctrineHydrator = $this->getMock('\stdClass', array('hydrate'));

        $mockDoctrineHydrator->expects($this->once())
            ->method('hydrate')
            ->with($expected, $firstEntity)
            ->will($this->returnValue($firstEntity));

        $this->service->expects($this->never())
            ->method('getService')
            ->with('Address');

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

        $this->service->expects($this->once())
            ->method('getEntityPropertyNames')
            ->will($this->returnValue(['validProperty']));

        $this->assertEquals($id, $this->service->create($data));
    }

    /**
     * Test create With Existing Address which is not an entity property
     *
     * @group Service
     * @group ServiceAbstract
     */
    public function testCreateWithExistingAddressInvalidEntityProperty()
    {
        $this->getMockService(
            array(
                'log', 'getNewEntity', 'getDoctrineHydrator',
                'dbPersist', 'dbFlush', 'getService', 'getEntityPropertyNames'
            )
        );

        $data = array(
            'validProperty' => 'valid',
            'addresses' => array(
                'address' => array(
                    'id' => 3
                )
            )
        );

        $expected = array(
            'validProperty' => 'valid',
        );

        $id = 7;

        $firstEntity = $this->getMock('\stdClass', array('getId'));

        $firstEntity->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($id));

        $mockDoctrineHydrator = $this->getMock('\stdClass', array('hydrate'));

        $mockDoctrineHydrator->expects($this->once())
            ->method('hydrate')
            ->with($expected, $firstEntity)
            ->will($this->returnValue($firstEntity));

        $this->service->expects($this->never())
            ->method('getService')
            ->with('Address');

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

        $this->service->expects($this->once())
            ->method('getEntityPropertyNames')
            ->will($this->returnValue(['validProperty']));

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
        $this->getMockService(array('log', 'getEntityById', 'getBundleCreator'));

        $id = 7;

        $data = array(
            'foo' => 'bar'
        );

        $mockEntity = $this->getMock('\stdClass');

        $mockBundleCreator = $this->getMock('\stdClass', array('buildEntityBundle'));

        $mockBundleCreator->expects($this->once())
            ->method('buildEntityBundle')
            ->will($this->returnValue($data));

        $this->service->expects($this->once())
            ->method('getBundleCreator')
            ->will($this->returnValue($mockBundleCreator));

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('getEntityById')
            ->with($id)
            ->will($this->returnValue($mockEntity));

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
        $this->getMockService(
            array(
                'log',
                'getValidSearchFields',
                'getEntityManager',
                'getEntityName',
                'canSoftDelete',
                'setOrderBy',
                'getPaginator'
            )
        );

        $page = '2';
        $resultLimit = '25';

        $data = array(
            'fooBar' => 'bar',
            'cakeBar' => 'bar',
            'barFor' => 'black sheep',
            'numberOfStuff' => 1,
            'page' => $page,
            'limit' => $resultLimit
        );

        $searchableFields = array('fooBar', 'barFor', 'numberOfStuff', 'somethingElse');

        $expectedParams = array(
            'fooBar' => 'bar',
            'barFor' => 'black sheep',
            'numberOfStuff' => 1,
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

        $mockQueryBuilder = $this->getMock(
            '\stdClass',
            array(
                'select', 'from', 'where', 'andWhere', 'setParameters', 'getQuery', 'setFirstResult', 'setMaxResults'
            )
        );

        $mockQueryBuilder->expects($this->once())
            ->method('select');

        $mockQueryBuilder->expects($this->once())
            ->method('from')
            ->with('MockEntity');

        $mockQueryBuilder->expects($this->once())
            ->method('where')
            ->with('a.fooBar LIKE :fooBar');

        $mockQueryBuilder->expects($this->at(4))
            ->method('andWhere')
            ->with('a.numberOfStuff = :numberOfStuff');

        $mockQueryBuilder->expects($this->at(5))
            ->method('andWhere')
            ->with('a.isDeleted = 0');

        $mockQueryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($expectedParams);

        $mockQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($mockQuery));

        // Start: Pagination
        $mockQueryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with($this->equalTo(($page * $resultLimit) - $resultLimit));
        $mockQueryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with($this->equalTo($resultLimit));
        // End: Pagination

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
            ->method('setOrderBy')
            ->with($this->equalTo($mockQueryBuilder), $this->equalTo($data));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $mockPaginator = $this->getMock('\StdClass');

        $this->service->expects($this->once())
            ->method('getPaginator')
            ->with($this->equalTo($mockQuery), false)
            ->will($this->returnValue(array()));

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
        $this->getMockService(
            array(
                'log',
                'getValidSearchFields',
                'getEntityManager', 'getEntityName',
                'canSoftDelete',
                'getDoctrineHydrator',
                'setOrderBy',
                'getPaginator'
            )
        );

        $page = '2';
        $resultLimit = '25';

        $data = array(
            'fooBar' => 'bar',
            'cakeBar' => 'bar',
            'barFor' => 'NULL',
            'numberOfStuff' => 1,
            'page' => $page,
            'limit' => $resultLimit
        );

        $searchableFields = array('fooBar', 'barFor', 'numberOfStuff', 'somethingElse');

        $expectedParams = array(
            'fooBar' => 'bar',
            'numberOfStuff' => 1
        );

        $results = array(
            array('foo' => 'bar')
        );

        $expected = array(
            'Count' => 1,
            'Results' => $results
        );

        $mockDoctrineHydrator = $this->getMock('\stdClass', array('extract'));

        $mockDoctrineHydrator->expects($this->any())
            ->method('extract')
            ->will($this->returnValue(array('foo' => 'bar')));

        $mockEntity = $this->getMock('\stdClass', array(), array(), 'MockEntity');

        $mockEntityName = get_class($mockEntity);

        $mockQuery = $this->getMock('\stdClass', array('getResult'));

        $mockQuery->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($results));

        $mockQueryBuilder = $this->getMock(
            '\stdClass',
            array(
                'select',
                'from',
                'where',
                'andWhere',
                'setParameters',
                'getQuery',
                'setFirstResult',
                'setMaxResults'
            )
        );

        $mockQueryBuilder->expects($this->once())
            ->method('select');

        $mockQueryBuilder->expects($this->once())
            ->method('from')
            ->with('MockEntity');

        $mockQueryBuilder->expects($this->once())
            ->method('where')
            ->with('a.fooBar LIKE :fooBar');

        $mockQueryBuilder->expects($this->at(4))
            ->method('andWhere')
            ->with('a.numberOfStuff = :numberOfStuff');

        $mockQueryBuilder->expects($this->at(5))
            ->method('andWhere')
            ->with('a.isDeleted = 0');

        $mockQueryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($expectedParams);

        $mockQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($mockQuery));

        // Start: Pagination
        $mockQueryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with($this->equalTo(($page * $resultLimit) - $resultLimit));
        $mockQueryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with($this->equalTo($resultLimit));
        // End: Pagination

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
            ->method('setOrderBy')
            ->with($this->equalTo($mockQueryBuilder), $this->equalTo($data));

        $this->service->expects($this->once())
            ->method('canSoftDelete')
            ->will($this->returnValue(true));

        $this->service->expects($this->any())
            ->method('getDoctrineHydrator')
            ->will($this->returnValue($mockDoctrineHydrator));

        $mockPaginator = $this->getMock('\StdClass');

        $this->service->expects($this->once())
            ->method('getPaginator')
            ->with($this->equalTo($mockQuery), false)
            ->will($this->returnValue($results));

        $this->assertEquals($expected, $this->service->getList($data));
    }

    public function testSetOrderByWithoutOrder()
    {
        $data = [
            'sort' => 'aField',
            'some' => 'value',
        ];

        $string = 'a.aField';

        $this->getMockService(array());

        $mockQueryBuilder = $this->getMock('\stdClass', ['orderBy']);
        $mockQueryBuilder->expects($this->once())
                         ->method('orderBy')
                         ->with($string);

        $this->service->setOrderBy($mockQueryBuilder, $data);
    }

    public function testSetOrderByWithOrder()
    {
        $data = [
            'sort' => 'aField',
            'order' => 'DESC',
            'some' => 'value',
        ];

        $fieldString = 'a.aField';
        $orderString = 'DESC';
        $this->getMockService(array());

        $mockQueryBuilder = $this->getMock('\stdClass', ['orderBy']);
        $mockQueryBuilder->expects($this->once())
                         ->method('orderBy')
                         ->with($fieldString, $orderString);

        $this->service->setOrderBy($mockQueryBuilder, $data);
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
        $this->getMockService(array('log', 'canSoftDelete', 'getUnDeletedById', 'processAddressEntity'));

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = null;

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('processAddressEntity')
            ->will($this->returnValue($data));

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
        $this->getMockService(
            array('log', 'canSoftDelete', 'getEntityManager', 'getEntityName', 'processAddressEntity')
        );

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
            ->method('processAddressEntity')
            ->will($this->returnValue($data));

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
        $this->getMockService(
            array(
                'log', 'canSoftDelete', 'getUnDeletedById', 'getDoctrineHydrator',
                'getEntityManager', 'dbPersist', 'dbFlush', 'getEntityPropertyNames'
            )
        );

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = $this->getMock('\stdClass', array('clearProperties'));

        $mockEntity->expects($this->once())
            ->method('clearProperties');

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
            ->method('getEntityPropertyNames')
            ->will($this->returnValue([]));

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
        $this->getMockService(array('log', 'canSoftDelete', 'getUnDeletedById', 'processAddressEntity'));

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = null;

        $this->service->expects($this->once())
            ->method('log');

        $this->service->expects($this->once())
            ->method('processAddressEntity')
            ->will($this->returnValue($data));

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
        $this->getMockService(
            array('log', 'canSoftDelete', 'getEntityManager', 'getEntityName', 'processAddressEntity')
        );

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
            ->method('processAddressEntity')
            ->will($this->returnValue($data));

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
        $this->getMockService(
            array(
                'log', 'canSoftDelete', 'getUnDeletedById', 'getDoctrineHydrator',
                'getEntityManager', 'dbPersist', 'dbFlush', 'getEntityPropertyNames'
            )
        );

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = $this->getMock('\stdClass', array('clearProperties'));

        $mockEntity->expects($this->once())
            ->method('clearProperties');

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

        $this->service->expects($this->once())
            ->method('getEntityPropertyNames')
            ->will($this->returnValue([]));

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

        $mockEntityManager = $this->getMockBuilder(
            '\Doctrine\Orm\EntityManager'
        )->disableOriginalConstructor()->getMock();

        $this->service->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($mockEntityManager));

        $hydrator = $this->service->getDoctrineHydrator();

        $this->assertTrue($hydrator instanceof \DoctrineModule\Stdlib\Hydrator\DoctrineObject);
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

    /**
     * Test getService
     */
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

    /**
     * Test getReflectedEntity
     */
    public function testGetReflectedEntity()
    {
        $this->getMockService(array('getEntityName'));

        $this->service->expects($this->once())
            ->method('getEntityName')
            ->will($this->returnValue('\stdClass'));

        $this->assertTrue($this->service->getReflectedEntity() instanceof \ReflectionClass);
    }

    /**
     * Test getEntityPropertyNames
     */
    public function testGetEntityPropertyNames()
    {
        $this->getMockService(array('getReflectedEntity'));

        $mockEntity = $this->getMock('\stdClass', ['getProperties']);
        $mockEntity->expects($this->once())
            ->method('getProperties')
            ->will($this->returnValue($this->generateProperties(['foo', 'bar'])));

        $this->service->expects($this->once())
            ->method('getReflectedEntity')
            ->will($this->returnValue($mockEntity));

        $this->assertEquals(
            ['foo', 'bar'],
            $this->service->getEntityPropertyNames()
        );
    }

    /**
     * Test getValidSearchFields
     */
    public function testGetValidSearchFields()
    {
        $expected = array(
            'Bob',
            'Foo'
        );

        $property1 = $this->getMock('\stdClass', array('getName'));
        $property1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('Bob'));

        $property2 = $this->getMock('\stdClass', array('getName'));
        $property2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('Foo'));

        $properties = array($property1, $property2);

        $this->getMockService(array('getReflectedEntity'));

        $reflectedMock = $this->getMock('\stdClass', array('getProperties'));

        $reflectedMock->expects($this->once())
            ->method('getProperties')
            ->will($this->returnValue($properties));

        $this->service->expects($this->once())
            ->method('getReflectedEntity')
            ->will($this->returnValue($reflectedMock));

        $this->assertEquals($expected, $this->service->getValidSearchFields());

        // Test again, so we know it's been cached
        $this->assertEquals($expected, $this->service->getValidSearchFields());
    }
}
