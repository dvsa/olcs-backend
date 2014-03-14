<?php

/**
 * Tests the Bundle Hydrator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Utility;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Olcs\Db\Utility\BundleHydrator;
use OlcsEntities\Mocks\Entity;

/**
 * Tests the Bundle Hydrator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleHydratorTest extends AbstractHttpControllerTestCase
{
    private $doctrineObject;

    public function setUp()
    {
        $this->doctrineObject = $this->getMockBuilder(
            '\DoctrineModule\Stdlib\Hydrator\DoctrineObject', 'extract'
        )->disableOriginalConstructor()->getMock();
    }

    /**
     * Tests the getNestedEntityFromEntities method
     *  with a simple entity
     */
    public function testGetNestedEntityFromEntitiesWithSimpleEntity()
    {
        $bundleHydrator = new BundleHydrator($this->doctrineObject);

        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $json = '{"SimpleEntity/0":{"id":1,"name":"Bob"}}';

        $response = $bundleHydrator->getNestedEntityFromEntities(json_decode($json, true));

        $first = current($response);

        $this->assertTrue($first instanceof Entity\SimpleEntity);

        $this->assertEquals(1, $first->getId());
        $this->assertEquals('Bob', $first->getName());
    }

    /**
     * Tests the getNestedEntityFromEntities method
     *  with a nested entity
     *  with 1 child
     */
    public function testGetNestedEntityFromEntitiesWithNestedEntityWith1Child()
    {
        $bundleHydrator = new BundleHydrator($this->doctrineObject);

        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $json = '{"NestedEntity/0":{"id":1,"name":"Dad","__REFS":{"kids":["SimpleEntity/0"]}},"SimpleEntity/0":{"id":1,"name":"Bob"}}';

        $response = $bundleHydrator->getNestedEntityFromEntities(json_decode($json, true));

        $first = current($response);

        $this->assertTrue($first instanceof Entity\NestedEntity);

        $this->assertEquals(1, $first->getId());
        $this->assertEquals('Dad', $first->getName());
        $this->assertEquals(1, count($first->getKids()));
        $this->assertEquals('Bob', $first->getKids()[0]->getName());
    }

    /**
     * Tests the getNestedEntityFromEntities method
     *  with a nested entity
     *  with multiple children
     */
    public function testGetNestedEntityFromEntitiesWithNestedEntityWithMultipleChild()
    {
        $bundleHydrator = new BundleHydrator($this->doctrineObject);

        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $json = '{"NestedEntity/0":{"id":1,"name":"Dad","__REFS":{"kids":["SimpleEntity/0","SimpleEntity/1"]}},"SimpleEntity/0":{"id":1,"name":"Bob"},"SimpleEntity/1":{"id":2,"name":"Bill"}}';

        $response = $bundleHydrator->getNestedEntityFromEntities(json_decode($json, true));

        $first = current($response);

        $this->assertTrue($first instanceof Entity\NestedEntity);

        $this->assertEquals(1, $first->getId());
        $this->assertEquals('Dad', $first->getName());
        $this->assertEquals(2, count($first->getKids()));
        $this->assertEquals('Bob', $first->getKids()[0]->getName());
        $this->assertEquals('Bill', $first->getKids()[1]->getName());
    }

    /**
     * Tests the getNestedEntityFromEntities method
     *  with a nested entity
     *  with multiple children
     *  with a 1 to 1 relationship
     */
    public function testGetNestedEntityFromEntitiesWithNestedEntityWithMultipleChildWith1To1()
    {
        $bundleHydrator = new BundleHydrator($this->doctrineObject);

        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $json = '{"NestedEntity/0":{"id":1,"name":"Dad","__REFS":{"favorite":"SimpleEntity/0","kids":["SimpleEntity/0","SimpleEntity/1"]}},"SimpleEntity/0":{"id":1,"name":"Bob"},"SimpleEntity/1":{"id":2,"name":"Bill"}}';

        $response = $bundleHydrator->getNestedEntityFromEntities(json_decode($json, true));

        $first = current($response);

        $this->assertTrue($first instanceof Entity\NestedEntity);

        $this->assertEquals(1, $first->getId());
        $this->assertEquals('Dad', $first->getName());
        $this->assertEquals(2, count($first->getKids()));
        $this->assertEquals('Bob', $first->getKids()[0]->getName());
        $this->assertEquals('Bob', $first->getFavorite()->getName());
        $this->assertEquals('Bill', $first->getKids()[1]->getName());
    }

    /**
     * Tests the getNestedEntityFromEntities method
     *  with multiple nested entities
     *  with same children
     */
    public function testGetNestedEntityFromEntitiesWithMultipleNestedEntitiesWithSameChildren()
    {
        $bundleHydrator = new BundleHydrator($this->doctrineObject);

        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $json = '{"NestedEntity/0":{"id":1,"name":"Dad","__REFS":{"kids":["SimpleEntity/0","SimpleEntity/1"]}},"SimpleEntity/0":{"id":1,"name":"Bob"},"SimpleEntity/1":{"id":2,"name":"Bill"},"NestedEntity/1":{"id":2,"name":"Mum","__REFS":{"kids":["SimpleEntity/0","SimpleEntity/1"]}}}';

        $response = $bundleHydrator->getNestedEntityFromEntities(json_decode($json, true));

        $first = current($response);

        $this->assertTrue($first instanceof Entity\NestedEntity);

        $this->assertEquals(1, $first->getId());
        $this->assertEquals('Dad', $first->getName());
        $this->assertEquals(2, count($first->getKids()));
        $this->assertEquals('Bob', $first->getKids()[0]->getName());
        $this->assertEquals('Bill', $first->getKids()[1]->getName());

        $second = $response['NestedEntity/1'];

        $this->assertTrue($second instanceof Entity\NestedEntity);

        $this->assertEquals(2, $second->getId());
        $this->assertEquals('Mum', $second->getName());
        $this->assertEquals(2, count($second->getKids()));
        $this->assertEquals('Bob', $second->getKids()[0]->getName());
        $this->assertEquals('Bill', $second->getKids()[1]->getName());
    }

    /**
     * Tests the getNestedEntityFromEntities method
     *  with a missing entity class
     *
     * @expectedException \Olcs\Db\Exceptions\EntityTypeNotFoundException
     */
    public function testGetNestedEntityFromEntitiesWithMissingEntityClass()
    {
        $bundleHydrator = new BundleHydrator($this->doctrineObject);

        $json = '{"MISSINGENTITY\/0":{"username":"Bobby","password":"password","displayName":"BobbyTest","__REFS":{"roles":["Role\/0"]}},"Role\/0":{"name":"Test Role","handle":"testrole","__REFS":{"permissions":["Permission\/0","Permission\/1"]}},"Permission\/0":{"name":"Test Permission 1","handle":"testpermission1"},"Permission\/1":{"name":"Test Permission 2","handle":"testpermission2"}}';

        $bundleHydrator->getNestedEntityFromEntities(json_decode($json, true));
    }

    /**
     * Tests the getTopLevelEntitiesFromNestedEntity method
     *  without an entity or an array or entities
     *
     * @expectedException \Exception
     */
    public function testGetTopLevelEntitiesFromNestedEntityWithInvalidInput()
    {
        $bundleHydrator = new BundleHydrator($this->doctrineObject);

        $bundleHydrator->getTopLevelEntitiesFromNestedEntity('string');
    }

    /**
     * Tests the getTopLevelEntitiesFromNestedEntity method
     *  with a simple entity
     */
    public function testGetTopLevelEntitiesFromNestedEntityWithSimpleEntity()
    {
        $entityMock = new Entity\SimpleEntity();

        $entityMock->setId(1)->setName('Bobby');

        $properties = array(
            'id' => 1,
            'name' => 'Bobby'
        );

        $expected = array(
            'SimpleEntity/0' => $properties
        );

        $this->doctrineObject->expects($this->once())
            ->method('extract')
            ->with($entityMock)
            ->will($this->returnValue($properties));

        $bundleHydrator = new BundleHydrator($this->doctrineObject);
        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $response = $bundleHydrator->getTopLevelEntitiesFromNestedEntity($entityMock);

        $this->assertTrue(is_array($response));

        $this->assertEquals($expected, $response);
    }

    /**
     * Tests the getTopLevelEntitiesFromNestedEntity method
     *  with a nested entity
     */
    public function testGetTopLevelEntitiesFromNestedEntityWithNestedEntity()
    {
        $childMock = new Entity\SimpleEntity();

        $childMock->setId(1)->setName('Son');

        $entityMock = new Entity\NestedEntity();

        $entityMock->setId(7)->setName('Dad')->addKid($childMock);

        $childProperties = array(
            'id' => 1,
            'name' => 'Son'
        );

        $dadProperties = array(
            'id' => 7,
            'name' => 'Dad',
            'kids' => $entityMock->getKids()
        );

        $dadsExpected = array(
            'id' => 7,
            'name' => 'Dad',
            '__REFS' => array(
                'kids' => array(
                    'SimpleEntity/0'
                )
            )
        );

        $expected = array(
            'NestedEntity/0' => $dadsExpected,
            'SimpleEntity/0' => $childProperties
        );

        $this->doctrineObject->expects($this->at(0))
            ->method('extract')
            ->with($entityMock)
            ->will($this->returnValue($dadProperties));

        $this->doctrineObject->expects($this->at(1))
            ->method('extract')
            ->with($childMock)
            ->will($this->returnValue($childProperties));

        $bundleHydrator = new BundleHydrator($this->doctrineObject);
        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $response = $bundleHydrator->getTopLevelEntitiesFromNestedEntity($entityMock);

        $this->assertTrue(is_array($response));

        $this->assertEquals($expected, $response);
    }

    /**
     * Tests the getTopLevelEntitiesFromNestedEntity method
     *  with a nested entity
     *  with a 1 to 1 relationship
     */
    public function testGetTopLevelEntitiesFromNestedEntityWithNestedEntityWith1To1()
    {
        $childMock = new Entity\SimpleEntity();

        $childMock->setId(1)->setName('Son');

        $entityMock = new Entity\NestedEntity();

        $entityMock->setId(7)->setName('Dad')->addKid($childMock)->setFavorite($childMock);

        $childProperties = array(
            'id' => 1,
            'name' => 'Son'
        );

        $dadProperties = array(
            'id' => 7,
            'name' => 'Dad',
            'favorite' => $childMock,
            'kids' => $entityMock->getKids()
        );

        $dadsExpected = array(
            'id' => 7,
            'name' => 'Dad',
            '__REFS' => array(
                'favorite' => 'SimpleEntity/0',
                'kids' => array(
                    'SimpleEntity/0'
                )
            )
        );

        $expected = array(
            'NestedEntity/0' => $dadsExpected,
            'SimpleEntity/0' => $childProperties
        );

        $this->doctrineObject->expects($this->at(0))
            ->method('extract')
            ->with($entityMock)
            ->will($this->returnValue($dadProperties));

        $this->doctrineObject->expects($this->at(1))
            ->method('extract')
            ->with($childMock)
            ->will($this->returnValue($childProperties));

        $bundleHydrator = new BundleHydrator($this->doctrineObject);
        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $response = $bundleHydrator->getTopLevelEntitiesFromNestedEntity($entityMock);

        $this->assertTrue(is_array($response));

        $this->assertEquals($expected, $response);
    }

    /**
     * Tests the getTopLevelEntitiesFromNestedEntity method
     *  with a nested entity
     *  and multiple children
     */
    public function testGetTopLevelEntitiesFromNestedEntityWithNestedEntityMultipleChildren()
    {
        $childMock = new Entity\SimpleEntity();

        $childMock->setId(1)->setName('Son');

        $childMock2 = new Entity\SimpleEntity();

        $childMock2->setId(2)->setName('Son 2');

        $entityMock = new Entity\NestedEntity();

        $entityMock->setId(7)->setName('Dad')->addKid($childMock)->addKid($childMock2);

        $childProperties = array(
            'id' => 1,
            'name' => 'Son'
        );

        $child2Properties = array(
            'id' => 2,
            'name' => 'Son 2'
        );

        $dadProperties = array(
            'id' => 7,
            'name' => 'Dad',
            'kids' => $entityMock->getKids()
        );

        $dadsExpected = array(
            'id' => 7,
            'name' => 'Dad',
            '__REFS' => array(
                'kids' => array(
                    'SimpleEntity/0',
                    'SimpleEntity/1'
                )
            )
        );

        $expected = array(
            'NestedEntity/0' => $dadsExpected,
            'SimpleEntity/0' => $childProperties,
            'SimpleEntity/1' => $child2Properties
        );

        $this->doctrineObject->expects($this->at(0))
            ->method('extract')
            ->with($entityMock)
            ->will($this->returnValue($dadProperties));

        $this->doctrineObject->expects($this->at(1))
            ->method('extract')
            ->with($childMock)
            ->will($this->returnValue($childProperties));

        $this->doctrineObject->expects($this->at(2))
            ->method('extract')
            ->with($childMock2)
            ->will($this->returnValue($child2Properties));

        $bundleHydrator = new BundleHydrator($this->doctrineObject);
        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $response = $bundleHydrator->getTopLevelEntitiesFromNestedEntity($entityMock);

        $this->assertTrue(is_array($response));

        $this->assertEquals($expected, $response);
    }

    /**
     * Tests the getTopLevelEntitiesFromNestedEntity method
     *  with multiple nested entities
     *  with different children
     */
    public function testGetTopLevelEntitiesFromNestedEntityWithMultipleNestedEntitiesWithDifferentChildren()
    {
        $childMock = new Entity\SimpleEntity();

        $childMock->setId(1)->setName('Child 1');

        $childMock2 = new Entity\SimpleEntity();

        $childMock2->setId(2)->setName('Child 2');

        $entityMock = new Entity\NestedEntity();

        $entityMock->setId(1)->setName('Adult 1')->addKid($childMock);

        $entityMock2 = new Entity\NestedEntity();

        $entityMock2->setId(2)->setName('Adult 2')->addKid($childMock2);

        $childProperties = array(
            'id' => 1,
            'name' => 'Child 1'
        );

        $childProperties2 = array(
            'id' => 2,
            'name' => 'Child 2'
        );

        $adultProperties = array(
            'id' => 1,
            'name' => 'Adult 1',
            'kids' => $entityMock->getKids()
        );

        $adultProperties2 = array(
            'id' => 2,
            'name' => 'Adult 2',
            'kids' => $entityMock2->getKids()
        );

        $adultExpected = array(
            'id' => 1,
            'name' => 'Adult 1',
            '__REFS' => array(
                'kids' => array(
                    'SimpleEntity/0'
                )
            )
        );

        $adultExpected2 = array(
            'id' => 2,
            'name' => 'Adult 2',
            '__REFS' => array(
                'kids' => array(
                    'SimpleEntity/1'
                )
            )
        );

        $expected = array(
            'NestedEntity/0' => $adultExpected,
            'SimpleEntity/0' => $childProperties,
            'NestedEntity/1' => $adultExpected2,
            'SimpleEntity/1' => $childProperties2
        );

        $this->doctrineObject->expects($this->at(0))
            ->method('extract')
            ->with($entityMock)
            ->will($this->returnValue($adultProperties));

        $this->doctrineObject->expects($this->at(1))
            ->method('extract')
            ->with($childMock)
            ->will($this->returnValue($childProperties));

        $this->doctrineObject->expects($this->at(2))
            ->method('extract')
            ->with($entityMock2)
            ->will($this->returnValue($adultProperties2));

        $this->doctrineObject->expects($this->at(3))
            ->method('extract')
            ->with($childMock2)
            ->will($this->returnValue($childProperties2));


        $bundleHydrator = new BundleHydrator($this->doctrineObject);
        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $response = $bundleHydrator->getTopLevelEntitiesFromNestedEntity(array($entityMock, $entityMock2));

        $this->assertTrue(is_array($response));

        $this->assertEquals($expected, $response);
    }

    /**
     * Tests the getTopLevelEntitiesFromNestedEntity method
     *  with multiple nested entities
     *  with same children
     */
    public function testGetTopLevelEntitiesFromNestedEntityWithMultipleNestedEntitiesWithSameChildren()
    {
        $childMock = new Entity\SimpleEntity();

        $childMock->setId(1)->setName('Child 1');

        $childMock2 = new Entity\SimpleEntity();

        $childMock2->setId(2)->setName('Child 2');

        $entityMock = new Entity\NestedEntity();

        $entityMock->setId(1)->setName('Adult 1')->addKid($childMock)->addKid($childMock2);

        $entityMock2 = new Entity\NestedEntity();

        $entityMock2->setId(2)->setName('Adult 2')->addKid($childMock)->addKid($childMock2);

        $childProperties = array(
            'id' => 1,
            'name' => 'Child 1'
        );

        $childProperties2 = array(
            'id' => 2,
            'name' => 'Child 2'
        );

        $adultProperties = array(
            'id' => 1,
            'name' => 'Adult 1',
            'kids' => $entityMock->getKids()
        );

        $adultProperties2 = array(
            'id' => 2,
            'name' => 'Adult 2',
            'kids' => $entityMock2->getKids()
        );

        $adultExpected = array(
            'id' => 1,
            'name' => 'Adult 1',
            '__REFS' => array(
                'kids' => array(
                    'SimpleEntity/0',
                    'SimpleEntity/1'
                )
            )
        );

        $adultExpected2 = array(
            'id' => 2,
            'name' => 'Adult 2',
            '__REFS' => array(
                'kids' => array(
                    'SimpleEntity/0',
                    'SimpleEntity/1'
                )
            )
        );

        $expected = array(
            'NestedEntity/0' => $adultExpected,
            'SimpleEntity/0' => $childProperties,
            'SimpleEntity/1' => $childProperties2,
            'NestedEntity/1' => $adultExpected2
        );

        $this->doctrineObject->expects($this->at(0))
            ->method('extract')
            ->with($entityMock)
            ->will($this->returnValue($adultProperties));

        $this->doctrineObject->expects($this->at(1))
            ->method('extract')
            ->with($childMock)
            ->will($this->returnValue($childProperties));

        $this->doctrineObject->expects($this->at(2))
            ->method('extract')
            ->with($childMock2)
            ->will($this->returnValue($childProperties2));

        $this->doctrineObject->expects($this->at(3))
            ->method('extract')
            ->with($entityMock2)
            ->will($this->returnValue($adultProperties2));


        $bundleHydrator = new BundleHydrator($this->doctrineObject);
        $bundleHydrator->setEntityNamespace('\OlcsEntities\Mocks\Entity\\');

        $response = $bundleHydrator->getTopLevelEntitiesFromNestedEntity(array($entityMock, $entityMock2));

        $this->assertTrue(is_array($response));

        $this->assertEquals($expected, $response);
    }
}
