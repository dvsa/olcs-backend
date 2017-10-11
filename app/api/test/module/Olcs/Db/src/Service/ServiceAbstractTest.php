<?php

/**
 * Tests ServiceAbstract
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Service;

use OlcsTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests ServiceAbstract
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ServiceAbstractTest extends MockeryTestCase
{
    /**
     * SUT
     *
     * @var \Olcs\Db\Service\ServiceAbstract
     */
    protected $sut;

    protected $sm;

    protected $em;

    protected function setUp()
    {
        $this->sut = $this->getMockForAbstractClass(
            '\Olcs\Db\Service\ServiceAbstract',
            array(),
            'Foo'
        );

        $this->sm = Bootstrap::getServiceManager();
        $this->em = $this->createPartialMock(
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
            ]
        );

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
        $this->sm->setService('Config', ['entity_namespaces' => ['Foo' => 'FooSpace']]);

        $this->assertEquals('\Dvsa\Olcs\Api\Entity\FooSpace\Foo', $this->sut->getEntityName());
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

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($hydrationData, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

        $mockAddressService = $this->createPartialMock('\stdClass', ['create']);
        $mockAddressService->expects($this->once())
            ->method('create')
            ->with($addressData)
            ->will($this->returnValue($addressId));

        $mockServiceFactory = $this->createPartialMock('\stdClass', ['getService']);
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

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($hydrationData, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'));

        $mockAddressService = $this->createPartialMock('\stdClass', ['update']);
        $mockAddressService->expects($this->once())
            ->method('update')
            ->with($addressId, $addressData);

        $mockServiceFactory = $this->createPartialMock('\stdClass', ['getService']);
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

        $language = 'en-gb';

        $mockQuery = m::mock()
            ->shouldReceive('getArrayResult')
            ->andReturn(null)
            ->shouldReceive('setHint')
            ->with(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            )
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(
                \Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS,
                true
            )
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language)
            ->andReturnSelf()
            ->getMock();

        $this->sut->setLanguage($language);

        $mockQueryBuilder = m::mock()
            ->shouldReceive('select')
            ->with(array('m'))
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', 'm')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
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

        $language = 'en-gb';

        $mockQuery = m::mock()
            ->shouldReceive('getArrayResult')
            ->andReturn(array($expectedResult))
            ->shouldReceive('setHint')
            ->with(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            )
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(
                \Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS,
                true
            )
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language)
            ->andReturnSelf()
            ->getMock();

        $this->sut->setLanguage($language);

        $mockQueryBuilder = m::mock()
            ->shouldReceive('select')
            ->with(array('m'))
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', 'm')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
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

        $language = 'en-gb';

        $mockQuery = m::mock()
            ->shouldReceive('getArrayResult')
            ->andReturn(array($expectedResult))
            ->shouldReceive('setHint')
            ->with(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            )
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(
                \Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS,
                true
            )
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1)
            ->andReturnSelf()
            ->shouldReceive('setHint')
            ->with(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $language)
            ->andReturnSelf()
            ->getMock();

        $this->sut->setLanguage($language);

        $mockQueryBuilder = m::mock()
            ->shouldReceive('select')
            ->with(array('m'))
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', 'm')
            ->andReturnSelf()
            ->shouldReceive('andWhere')
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
            ->shouldReceive('getRefDataReplacements')
            ->andReturn([])
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

        $this->sm->setService('Config', ['entity_namespaces' => ['Foo' => 'FooSpace']]);

        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->assertFalse($this->sut->update($id, $data));
    }

    /**
     * @group service_abstract
     */
    public function testUpdateWithVersionWithEntity()
    {
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = $this->createPartialMock('\OlcsTest\Db\Service\Stubs\EntityStub', array('clearProperties'));
        $mockEntity->expects($this->once())->method('clearProperties');

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'))
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));
        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

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
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');

        $id = 7;

        $data = array(
            'version' => 1
        );

        $mockEntity = $this->createPartialMock('\OlcsTest\Db\Service\Stubs\EntityStub', array('clearProperties'));
        $mockEntity->expects($this->once())
            ->method('clearProperties');

        $mockDoctrineObject = $this->mockHydrator();
        $mockDoctrineObject->expects($this->once())
            ->method('hydrate')
            ->with($data, $this->isInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub'))
            ->will($this->returnValue($mockEntity));

        $this->em->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));
        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

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

        $data = array();

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

        $this->sm->setService('Config', ['entity_namespaces' => ['Foo' => 'FooSpace']]);

        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $this->assertFalse($this->sut->patch($id, $data));
    }

    protected function mockHydrator()
    {
        $mockDoctrineObject = $this->createPartialMock('\stdClass', ['hydrate']);

        $mockHydratorManager = $this->createPartialMock('\stdClass', ['get']);
        $mockHydratorManager->expects($this->any())
            ->method('get')
            ->with('DoctrineModule\Stdlib\Hydrator\DoctrineObject')
            ->will($this->returnValue($mockDoctrineObject));

        $this->sm->setService('HydratorManager', $mockHydratorManager);

        return $mockDoctrineObject;
    }

    /**
     * @group service_abstract
     */
    public function testCreateWithCascade()
    {
        $this->sm->setService('Config', ['entity_namespaces' => ['EntityStub' => '']]);
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $this->sut->setEntityNamespace('\OlcsTest\Db\Service\Stubs\\');

        $data = array(
            'foo' => 'bar',
            'childList' => array(
                array(
                    'child' => 1
                ),
                array(
                    'child' => 2,
                    'relative' => array(
                        'relative' => 3
                    ),
                    '_OPTIONS_' => array(
                        'cascade' => array(
                            'single' => array(
                                'relative' => array(
                                    'entity' => 'EntityStub',
                                    'parent' => 'cousin'
                                )
                            )
                        )
                    )
                )
            ),
            'relative' => array(
                'relative' => 4
            ),
            '_OPTIONS_' => array(
                'cascade' => array(
                    'list' => array(
                        'childList' => array(
                            'entity' => 'EntityStub',
                            'parent' => 'parent'
                        )
                    ),
                    'single' => array(
                        'relative' => array(
                            'entity' => 'EntityStub'
                        )
                    )
                )
            )
        );

        $mockDoctrineObject = $this->mockHydrator();

        $mockDoctrineObject->expects($this->any())
            ->method('hydrate')
            ->will(
                $this->returnCallback(
                    function ($data, $entity) {
                        $entity->setData($data);
                    }
                )
            );

        $this->em->expects($this->once())
            ->method('persist')
            ->will(
                $this->returnCallback(
                    function ($entity) {

                        // Assert we have the correct foo property
                        $this->assertEquals('bar', $entity->data['foo']);

                        // We should still have 2 children
                        $this->assertCount(2, $entity->data['childList']);

                        // Both children should be entities now
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['childList'][0]);
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['childList'][1]);

                        // Both children should have the correct id's
                        $this->assertEquals(1, $entity->data['childList'][0]->data['child']);
                        $this->assertEquals(2, $entity->data['childList'][1]->data['child']);

                        // Both children should have the correct parent
                        $this->assertSame($entity, $entity->data['childList'][0]->parent);
                        $this->assertSame($entity, $entity->data['childList'][1]->parent);

                        // The second child, should have a relative
                        $this->assertInstanceOf(
                            '\OlcsTest\Db\Service\Stubs\EntityStub',
                            $entity->data['childList'][1]->data['relative']
                        );

                        // That cousin, should be the parent entity (A bit obscure but it's right)
                        $this->assertSame(
                            $entity->data['childList'][1],
                            $entity->data['childList'][1]->data['relative']->cousin
                        );

                        // This relative should now be an entity
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['relative']);

                        // This relative should have the right data
                        $this->assertEquals(4, $entity->data['relative']->data['relative']);
                    }
                )
            );

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertNull($this->sut->create($data));
    }

    /**
     * @group service_abstract
     */
    public function testCreateWithCascadeWithUpdate()
    {
        $this->sm->setService('Config', ['entity_namespaces' => ['EntityStub' => '']]);
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $this->sut->setEntityNamespace('\OlcsTest\Db\Service\Stubs\\');

        $data = array(
            'foo' => 'bar',
            'childList' => array(
                array(
                    'id' => 7,
                    'child' => 1
                ),
                array(
                    'child' => 2,
                    'relative' => array(
                        'relative' => 3
                    ),
                    '_OPTIONS_' => array(
                        'cascade' => array(
                            'single' => array(
                                'relative' => array(
                                    'entity' => 'EntityStub',
                                    'parent' => 'cousin'
                                )
                            )
                        )
                    )
                )
            ),
            'relative' => array(
                'relative' => 4
            ),
            '_OPTIONS_' => array(
                'cascade' => array(
                    'list' => array(
                        'childList' => array(
                            'entity' => 'EntityStub',
                            'parent' => 'parent'
                        )
                    ),
                    'single' => array(
                        'relative' => array(
                            'entity' => 'EntityStub'
                        )
                    )
                )
            )
        );

        $mockChild = m::mock('\OlcsTest\Db\Service\Stubs\EntityStub')->makePartial();

        $this->em->expects($this->once())
            ->method('find')
            ->with('\OlcsTest\Db\Service\Stubs\EntityStub', 7)
            ->will($this->returnValue($mockChild));

        $mockDoctrineObject = $this->mockHydrator();

        $mockDoctrineObject->expects($this->any())
            ->method('hydrate')
            ->will(
                $this->returnCallback(
                    function ($data, $entity) {
                        $entity->setData($data);
                    }
                )
            );

        $this->em->expects($this->once())
            ->method('persist')
            ->will(
                $this->returnCallback(
                    function ($entity) use ($mockChild) {

                        // Assert we have the correct foo property
                        $this->assertEquals('bar', $entity->data['foo']);

                        // We should still have 2 children
                        $this->assertCount(2, $entity->data['childList']);

                        // Both children should be entities now
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['childList'][0]);
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['childList'][1]);

                        // Both children should have the correct id's
                        $this->assertEquals(1, $entity->data['childList'][0]->data['child']);
                        $this->assertEquals(2, $entity->data['childList'][1]->data['child']);

                        $this->assertSame($mockChild, $entity->data['childList'][0]);

                        // Both children should have the correct parent
                        $this->assertSame($entity, $entity->data['childList'][0]->parent);
                        $this->assertSame($entity, $entity->data['childList'][1]->parent);

                        // The second child, should have a relative
                        $this->assertInstanceOf(
                            '\OlcsTest\Db\Service\Stubs\EntityStub',
                            $entity->data['childList'][1]->data['relative']
                        );

                        // That cousin, should be the parent entity (A bit obscure but it's right)
                        $this->assertSame(
                            $entity->data['childList'][1],
                            $entity->data['childList'][1]->data['relative']->cousin
                        );

                        // This relative should now be an entity
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['relative']);

                        // This relative should have the right data
                        $this->assertEquals(4, $entity->data['relative']->data['relative']);
                    }
                )
            );

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertNull($this->sut->create($data));
    }

    /**
     * @group service_abstract
     */
    public function testUpdateWithCascade()
    {
        $this->sm->setService('Config', ['entity_namespaces' => ['EntityStub' => '']]);
        $this->sut->setEntityName('\OlcsTest\Db\Service\Stubs\EntityStub');
        $this->sut->setEntityNamespace('\OlcsTest\Db\Service\Stubs\\');

        $id = 7;

        $data = array(
            'version' => 1,
            'foo' => 'bar',
            'childList' => array(
                array(
                    'child' => 1
                ),
                array(
                    'child' => 2,
                    'relative' => array(
                        'relative' => 3
                    ),
                    '_OPTIONS_' => array(
                        'cascade' => array(
                            'single' => array(
                                'relative' => array(
                                    'entity' => 'EntityStub',
                                    'parent' => 'cousin'
                                )
                            )
                        )
                    )
                )
            ),
            'relative' => array(
                'relative' => 4
            ),
            '_OPTIONS_' => array(
                'cascade' => array(
                    'list' => array(
                        'childList' => array(
                            'entity' => 'EntityStub',
                            'parent' => 'parent'
                        )
                    ),
                    'single' => array(
                        'relative' => array(
                            'entity' => 'EntityStub'
                        )
                    )
                )
            )
        );

        $mockEntity = m::mock('\OlcsTest\Db\Service\Stubs\EntityStub')->makePartial();
        $mockEntity->shouldReceive('clearProperties');

        $this->em->expects($this->once())
            ->method('lock')
            ->will($this->returnValue($mockEntity));
        $this->em->expects($this->once())
            ->method('find')
            ->will($this->returnValue($mockEntity));

        $mockDoctrineObject = $this->mockHydrator();

        $mockDoctrineObject->expects($this->any())
            ->method('hydrate')
            ->will(
                $this->returnCallback(
                    function ($data, $entity) {
                        $entity->setData($data);
                        return $entity;
                    }
                )
            );

        $this->em->expects($this->once())
            ->method('persist')
            ->will(
                $this->returnCallback(
                    function ($entity) use ($mockEntity) {

                        $this->assertSame($mockEntity, $entity);

                        // Assert we have the correct foo property
                        $this->assertEquals('bar', $entity->data['foo']);

                        // We should still have 2 children
                        $this->assertCount(2, $entity->data['childList']);

                        // Both children should be entities now
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['childList'][0]);
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['childList'][1]);

                        // Both children should have the correct id's
                        $this->assertEquals(1, $entity->data['childList'][0]->data['child']);
                        $this->assertEquals(2, $entity->data['childList'][1]->data['child']);

                        // Both children should have the correct parent
                        $this->assertSame($entity, $entity->data['childList'][0]->parent);
                        $this->assertSame($entity, $entity->data['childList'][1]->parent);

                        // The second child, should have a relative
                        $this->assertInstanceOf(
                            '\OlcsTest\Db\Service\Stubs\EntityStub',
                            $entity->data['childList'][1]->data['relative']
                        );

                        // That cousin, should be the parent entity (A bit obscure but it's right)
                        $this->assertSame(
                            $entity->data['childList'][1],
                            $entity->data['childList'][1]->data['relative']->cousin
                        );

                        // This relative should now be an entity
                        $this->assertInstanceOf('\OlcsTest\Db\Service\Stubs\EntityStub', $entity->data['relative']);

                        // This relative should have the right data
                        $this->assertEquals(4, $entity->data['relative']->data['relative']);
                    }
                )
            );

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->sut->update($id, $data));
    }
}
