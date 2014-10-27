<?php

/**
 * Tests BundleCreator class
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Service\Bundle;

use PHPUnit_Framework_TestCase;

use Olcs\Db\Service\Bundle\BundleCreator;

/**
 * Tests BundleCreator class
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @group BundleCreator
 */
class BundleCreatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the service
     *
     * @var object
     */
    private $service;

    /**
     * Holds the mock hydrator
     *
     * @var object
     */
    private $hydrator;

    /**
     * Setup the service
     */
    public function setUp()
    {
        $this->hydrator = $this->getMock('\stdClass', array('extract'));

        $this->service = new BundleCreator($this->hydrator);
    }

    /**
     * Test buildEntityBundles without json
     *
     * @expectedException \Exception
     */
    public function testBuildEntityBundlesWithoutJson()
    {
        $entity = $this->getMock('\stdClass', array());

        $extractedEntity = array(
            'foo' => 'bar'
        );

        $config = array(
            'bundle' => '[blah]'
        );

        $this->service->buildEntityBundle($entity, $config);
    }

    /**
     * Test buildEntityBundles without bundle
     */
    public function testBuildEntityBundlesWithoutBundle()
    {
        $entity = $this->getMock('\stdClass', array());

        $extractedEntity = array(
            'foo' => 'bar'
        );

        $config = array(

        );

        $this->hydrator->expects($this->once())
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->assertEquals($extractedEntity, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with custom properties
     */
    public function testBuildEntityBundlesWithCustomProperties()
    {
        $entity = $this->getMock('\stdClass', array());

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'properties' => array(
                        'id'
                    )
                )
            )
        );

        $this->hydrator->expects($this->once())
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->assertEquals(array('id' => '123'), $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with custom properties and date
     *
     * @group bundle_creator
     */
    public function testBuildEntityBundlesWithCustomPropertiesAndDate()
    {
        $entity = $this->getMock('\stdClass', array());

        $date = date('Y-m-d');

        $extractedEntity = array(
            'id' => '123',
            'date' => new \DateTime($date),
            'foo' => 'bar'
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'properties' => array(
                        'id',
                        'date'
                    )
                )
            )
        );

        $this->hydrator->expects($this->once())
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $bundle = $this->service->buildEntityBundle($entity, $config);

        $this->assertEquals(123, $bundle['id']);
        $this->assertStringStartsWith($date, $bundle['date']);
    }

    /**
     * Test buildEntityBundles with all properties
     */
    public function testBuildEntityBundlesWithAllProperties()
    {
        $entity = $this->getMock('\stdClass', array());

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'properties' => 'ALL'
                )
            )
        );

        $this->hydrator->expects($this->once())
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->assertEquals($extractedEntity, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with default properties
     */
    public function testBuildEntityBundlesWithDefaultProperties()
    {
        $entity = $this->getMock('\stdClass', array());

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $config = array(
            'bundle' => '[]'
        );

        $this->hydrator->expects($this->once())
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->assertEquals($extractedEntity, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with child
     */
    public function testBuildEntityBundlesWithChild()
    {
        $mockJazz = $this->getMock('\stdClass');

        $extractedJazz = array(
            'id' => '1'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'jazz' => $extractedJazz
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'properties' => array(
                        'id'
                    ),
                    'children' => array(
                        'jazz'
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockJazz)
            ->will($this->returnValue($extractedJazz));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with child and all properties
     */
    public function testBuildEntityBundlesWithChildAndAllProperties()
    {
        $mockJazz = $this->getMock('\stdClass');

        $extractedJazz = array(
            'id' => '1'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => $extractedJazz
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz'
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockJazz)
            ->will($this->returnValue($extractedJazz));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with children collection
     */
    public function testBuildEntityBundlesWithChildrenCollection()
    {
        $mockChild = $this->getMock('\stdClass');

        $children = array(
            $mockChild
        );

        $mockJazz = new \Doctrine\Common\Collections\ArrayCollection($children);

        $extractedJazz = array(
            'id' => '1'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => array(
                $extractedJazz
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz'
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockChild)
            ->will($this->returnValue($extractedJazz));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with multiple children
     */
    public function testBuildEntityBundlesWithMultipleChildren()
    {
        $mockChild = $this->getMock('\stdClass');

        $mockChild1 = $this->getMock('\stdClass');

        $children = array(
            $mockChild,
            $mockChild1
        );

        $mockJazz = new \Doctrine\Common\Collections\ArrayCollection($children);

        $extractedJazz = array(
            'id' => '1'
        );

        $extractedJazz1 = array(
            'id' => '1'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => array(
                $extractedJazz,
                $extractedJazz1
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz'
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockChild)
            ->will($this->returnValue($extractedJazz));

        $this->hydrator->expects($this->at(2))
            ->method('extract')
            ->with($mockChild1)
            ->will($this->returnValue($extractedJazz1));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with multiple children with custom properties
     */
    public function testBuildEntityBundlesWithMultipleChildrenWithCustomProperties()
    {
        $mockChild = $this->getMock('\stdClass');

        $mockChild1 = $this->getMock('\stdClass');

        $children = array(
            $mockChild,
            $mockChild1
        );

        $mockJazz = new \Doctrine\Common\Collections\ArrayCollection($children);

        $extractedJazz = array(
            'id' => '1',
            'foo' => 'bar',
            'this' => 'something'
        );

        $extractedJazz1 = array(
            'id' => '1',
            'foo' => 'cake',
            'this' => 'that'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => array(
                array('foo' => 'bar'),
                array('foo' => 'cake')
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz' => array(
                            'properties' => array(
                                'foo'
                            )
                        )
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockChild)
            ->will($this->returnValue($extractedJazz));

        $this->hydrator->expects($this->at(2))
            ->method('extract')
            ->with($mockChild1)
            ->will($this->returnValue($extractedJazz1));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with more nesting
     */
    public function testBuildEntityBundlesWithMoreNesting()
    {
        $mockFoo = $this->getMock('\stdClass');

        $mockChild = $this->getMock('\stdClass', array('getFoo'));

        $mockChild->expects($this->once())
            ->method('getFoo')
            ->will($this->returnValue($mockFoo));

        $children = array(
            $mockChild
        );

        $mockJazz = new \Doctrine\Common\Collections\ArrayCollection($children);

        $extractedJazz = array(
            'id' => '1',
            'this' => 'something'
        );

        $extractedFoo = array(
            'id' => '7'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => array(
                array(
                    'foo' => $extractedFoo,
                    'id' => '1',
                    'this' => 'something'
                )
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz' => array(
                            'children' => array(
                                'foo'
                            )
                        )
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockChild)
            ->will($this->returnValue($extractedJazz));

        $this->hydrator->expects($this->at(2))
            ->method('extract')
            ->with($mockFoo)
            ->will($this->returnValue($extractedFoo));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with more nesting with null properties and child
     */
    public function testBuildEntityBundlesWithMoreNestingWithNullPropertiesAndChild()
    {
        $mockFoo = $this->getMock('\stdClass');

        $mockChild = $this->getMock('\stdClass', array('getFoo'));

        $mockChild->expects($this->once())
            ->method('getFoo')
            ->will($this->returnValue($mockFoo));

        $children = array(
            $mockChild
        );

        $mockJazz = new \Doctrine\Common\Collections\ArrayCollection($children);

        $extractedJazz = array(
        );

        $extractedFoo = array(
            'id' => '7'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => array(
                array(
                    'foo' => $extractedFoo
                )
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz' => array(
                            'properties' => null,
                            'children' => array(
                                'foo'
                            )
                        )
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockChild)
            ->will($this->returnValue($extractedJazz));

        $this->hydrator->expects($this->at(2))
            ->method('extract')
            ->with($mockFoo)
            ->will($this->returnValue($extractedFoo));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with more nesting with null properties
     */
    public function testBuildEntityBundlesWithMoreNestingWithNullProperties()
    {
        $mockFoo = $this->getMock('\stdClass');

        $mockChild = $this->getMock('\stdClass');

        $children = array(
            $mockChild
        );

        $mockJazz = new \Doctrine\Common\Collections\ArrayCollection($children);

        $extractedJazz = array(
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => array(
                array()
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz' => array(
                            'properties' => null
                        )
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockChild)
            ->will($this->returnValue($extractedJazz));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with more nesting with null entity
     */
    public function testBuildEntityBundlesWithMoreNestingWithNullEntity()
    {
        $mockJazz = null;

        $extractedJazz = array(
            'id' => '1',
            'this' => 'something'
        );

        $extractedFoo = array(
            'id' => '7'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => array(
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz' => array(
                            'children' => array(
                                'foo'
                            )
                        )
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with children collection: criteria set and
     * matches
     */
    public function testBuildEntityBundlesWithChildrenCollectionCriteriaMatches()
    {
        $mockChild = $this->getMock('\stdClass');
        $mockChild->foo = 'bar';

        $children = array(
            $mockChild
        );

        $mockJazz = new \Doctrine\Common\Collections\ArrayCollection($children);

        $extractedJazz = array(
            'id' => '1'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123',
            'foo' => 'bar'
        );

        $expected = array(
            'id' => '123',
            'foo' => 'bar',
            'jazz' => array(
                $extractedJazz
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz' => array(
                            'criteria' => array(
                                'foo' => 'bar'
                            )
                        )
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->hydrator->expects($this->at(1))
            ->method('extract')
            ->with($mockChild)
            ->will($this->returnValue($extractedJazz));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }

    /**
     * Test buildEntityBundles with children collection: criteria set but doesnt match
     */
    public function testBuildEntityBundlesWithChildrenCollectionCriteriaNoMatches()
    {
        $mockChild = $this->getMock('\stdClass');
        $mockChild->foo = 'barbar';

        $children = array(
            $mockChild
        );

        $mockJazz = new \Doctrine\Common\Collections\ArrayCollection($children);

        $extractedJazz = array(
            'id' => '1'
        );

        $entity = $this->getMock('\stdClass', array('getJazz'));

        $entity->expects($this->once())
            ->method('getJazz')
            ->will($this->returnValue($mockJazz));

        $extractedEntity = array(
            'id' => '123'
        );

        $expected = array(
            'id' => '123',
            'jazz' => array(
            )
        );

        $config = array(
            'bundle' => json_encode(
                array(
                    'children' => array(
                        'jazz' => array(
                            'criteria' => array(
                                'foo' => 'bar'
                            )
                        )
                    )
                )
            )
        );

        $this->hydrator->expects($this->at(0))
            ->method('extract')
            ->with($entity)
            ->will($this->returnValue($extractedEntity));

        $this->assertEquals($expected, $this->service->buildEntityBundle($entity, $config));
    }
}
