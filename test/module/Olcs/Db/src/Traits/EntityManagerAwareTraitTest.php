<?php

/**
 * Tests EntityManagerAwareTrait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace OlcsTest\Db\Service;

use Olcs\Db\Traits\EntityManagerAwareTrait;

/**
 * Tests EntityManagerAwareTrait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class EntityManagerAwareTraitTest extends \PHPUnit\Framework\TestCase
{
    private $trait;

    private $emMock;

    /**
     * Mock the trait
     */
    public function setUp()
    {
        $this->trait = $this->getMockForTrait('\Olcs\Db\Traits\EntityManagerAwareTrait');
    }

    /**
     * Helper method to create and set an entity manager mock
     */
    public function setEntityManager()
    {
        $this->emMock = $this->getMockBuilder(
            '\Doctrine\ORM\EntityManager',
            array(
                'persist',
                'flush',
                'commit',
                'getConnection',
                'close'
            )
        )->disableOriginalConstructor()->getMock();

        $this->trait->setEntityManager($this->emMock);
    }

    /**
     * Test getEntityManager
     *  with entity manager
     *
     * @group Traits
     * @group EntityManagerAwareTrait
     */
    public function testGetEntityManagerWithEntityManager()
    {
        $this->setEntityManager();

        $this->assertEquals($this->emMock, $this->trait->getEntityManager());
    }

    /**
     * Test getEntityManager
     *  without entity manager
     *
     * @group Traits
     * @group EntityManagerAwareTrait
     *
     * @expectedException \LogicException
     */
    public function testGetEntityManagerWithoutEntityManager()
    {
        $this->trait->getEntityManager();
    }

    /**
     * Test dbPersist
     *
     * @group Traits
     * @group EntityManagerAwareTrait
     */
    public function testDbPersist()
    {
        $this->setEntityManager();

        $entity = new \stdClass();

        $this->emMock->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->trait->dbPersist($entity);
    }

    /**
     * Test dbFlush
     *
     * @group Traits
     * @group EntityManagerAwareTrait
     */
    public function testDbFlush()
    {
        $this->setEntityManager();

        $this->emMock->expects($this->once())
            ->method('flush');

        $this->trait->dbFlush();
    }

    /**
     * Test dbStartTransaction
     *
     * @group Traits
     * @group EntityManagerAwareTrait
     */
    public function testDbStartTransaction()
    {
        $connectionMock = $this->createPartialMock('\stdClass', array('beginTransaction'));

        $this->setEntityManager();

        $this->emMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connectionMock));

        $connectionMock->expects($this->once())
            ->method('beginTransaction');

        $this->trait->dbStartTransaction();
    }

    /**
     * Test dbCommit
     *
     * @group Traits
     * @group EntityManagerAwareTrait
     */
    public function testDbCommit()
    {
        $this->setEntityManager();

        $this->emMock->expects($this->once())
            ->method('commit');

        $this->trait->dbCommit();
    }

    /**
     * Test dbRollback
     *
     * @group Traits
     * @group EntityManagerAwareTrait
     */
    public function testDbRollback()
    {
        $connectionMock = $this->createPartialMock('\stdClass', array('rollback'));

        $this->setEntityManager();

        $this->emMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($connectionMock));

        $this->emMock->expects($this->once())
            ->method('close');

        $connectionMock->expects($this->once())
            ->method('rollback');

        $this->trait->dbRollback();
    }
}
