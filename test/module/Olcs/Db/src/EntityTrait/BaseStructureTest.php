<?php

/**
 * Test BaseStructureTest Entity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Entity;

use PHPUnit_Framework_TestCase;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Test BaseStructureTest Entity
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BaseStructureTest extends PHPUnit_Framework_TestCase
{

    private $entity;

    /**
     * Setup the entity
     */
    public function setUp()
    {
        $this->getMockEntity();
    }

    /**
     * Mock the entities methods
     *
     * @param array $methods
     */
    public function getMockEntity($methods = array())
    {
        $this->entity = $this->getMockForTrait(
            '\Olcs\Db\EntityTrait\BaseStructure',
            array(),
            '',
            true,
            true,
            true,
            $methods
        );
    }

    /**
     * Test setCreatedBy
     *
     * @dataProvider providerSetCreatedBy
     */
    public function testSetCreatedBy($input, $output)
    {
        $this->entity->setCreatedBy($input);

        $this->assertEquals($output, $this->entity->getCreatedBy());
    }

    /**
     * Provider for setCreatedBy
     */
    public function providerSetCreatedBy()
    {
        $mock = $this->getMock('\Olcs\Db\Entity\User');

        return array(
            array(null, null),
            array($mock, $mock)
        );
    }

    /**
     * Test setCreatedOn
     *
     * @dataProvider providerSetCreatedOn
     */
    public function testSetCreatedOn($input, $output)
    {
        $this->entity->setCreatedOn($input);

        $this->assertEquals($output, $this->entity->getCreatedOn());
    }

    /**
     * Provider for setCreatedOn
     */
    public function providerSetCreatedOn()
    {
        $date = new \DateTime();

        return array(
            array($date, $date)
        );
    }

    /**
     * Test setLastUpdatedBy
     *
     * @dataProvider providerSetLastUpdatedBy
     */
    public function testSetLastUpdatedBy($input, $output)
    {
        $this->entity->setLastUpdatedBy($input);

        $this->assertEquals($output, $this->entity->getLastUpdatedBy());
    }

    /**
     * Provider for setLastUpdatedBy
     */
    public function providerSetLastUpdatedBy()
    {
        $mock = $this->getMock('\Olcs\Db\Entity\User');

        return array(
            array(null, null),
            array($mock, $mock)
        );
    }

    /**
     * Test setLastUpdatedOn
     *
     * @dataProvider providerSetLastUpdatedOn
     */
    public function testSetLastUpdatedOn($input, $output)
    {
        $this->entity->setLastUpdatedOn($input);

        $this->assertEquals($output, $this->entity->getLastUpdatedOn());
    }

    /**
     * Provider for setLastUpdatedOn
     */
    public function providerSetLastUpdatedOn()
    {
        $date = new \DateTime();

        return array(
            array($date, $date)
        );
    }

    /**
     * Test setVersion
     *
     * @dataProvider providerSetVersion
     */
    public function testSetVersion($input, $output)
    {
        $this->entity->setVersion($input);

        $this->assertEquals($output, $this->entity->getVersion());
    }

    /**
     * Provider for setVersion
     */
    public function providerSetVersion()
    {
        return array(
            array(0, 0),
            array(1, 1),
            array(999, 999)
        );
    }

    /**
     * Test setDefaultsOnPrePersist
     */
    public function testSetDefaultsOnPrePersist()
    {
        $this->getMockEntity(
            array(
                'setCreatedOn',
                'setLastUpdatedOn',
                'setVersion'
            )
        );

        $this->entity->expects($this->once())
            ->method('setCreatedOn');

        $this->entity->expects($this->once())
            ->method('setLastUpdatedOn');

        $this->entity->expects($this->once())
            ->method('setVersion')
            ->with(1);

        $this->entity->setDefaultsOnPrePersist();
    }

    /**
     * Test clearProperties
     */
    public function testClearProperties()
    {
        $this->entity->setVersion(1);

        $this->assertEquals(1, $this->entity->getVersion());

        $this->entity->clearProperties(array('version'));

        $this->assertEquals(null, $this->entity->getVersion());
    }


    /**
     * Test clearProperties
     *  with collection
     */
    public function testClearPropertiesWithCollection()
    {
        $collection = new ArrayCollection(array('foo' => 'bar'));

        $this->entity->setVersion($collection);

        $this->assertEquals($collection, $this->entity->getVersion());

        $this->entity->clearProperties(array('version'));

        $version = $this->entity->getVersion();

        $this->assertTrue($version instanceof ArrayCollection);

        $versionArray = $version->toArray();

        $this->assertTrue(empty($versionArray));
    }
}
