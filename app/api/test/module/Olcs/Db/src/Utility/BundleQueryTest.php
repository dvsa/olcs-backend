<?php

/**
 * BundleQuery Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Utility;

use OlcsTest\Bootstrap;
use Olcs\Db\Utility\BundleQuery;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * BundleQuery Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleQueryTest extends TestCase
{
    protected $qb;
    protected $sm;
    protected $sut;
    protected $em;

    protected function setUp()
    {
        $this->em = m::mock();

        $this->qb = m::mock();
        $this->qb->shouldReceive('getEntityManager')
            ->andReturn($this->em);

        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new BundleQuery();
        $this->sut->setQueryBuilder($this->qb);
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @group bundle_query
     */
    public function testGetParams()
    {
        $this->assertEquals(array(), $this->sut->getParams());
    }

    /**
     * @group bundle_query
     */
    public function testBuildWithoutChildren()
    {
        $config = array();

        $this->qb->shouldReceive('addSelect')
            ->with('m');

        $this->sut->build($config);
    }

    /**
     * @group bundle_query
     */
    public function testBuildWithChildren()
    {
        $config = array(
            'children' => array(
                'foo' => array()
            )
        );

        $this->qb->shouldReceive('addSelect')
            ->with('m')
            ->shouldReceive('leftJoin')
            ->with('m.foo', 'mf', null, null)
            ->shouldReceive('addSelect')
            ->with('mf')
            ->shouldReceive('getRootEntities')
            ->andReturn(['\Some\Entity']);

        $metadata = new \stdClass();
        $metadata->associationMappings = [
            'foo' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ]
        ];

        $this->em->shouldReceive('getClassMetadata')
            ->with('\Some\Entity')
            ->andReturn($metadata);

        $this->sut->build($config);
    }

    /**
     * @group bundle_query
     */
    public function testBuildWithChildrenWithCriteria()
    {
        $config = array(
            'children' => array(
                'foo' => array(
                    'criteria' => array(
                        'name' => 'bob'
                    )
                )
            )
        );

        $expressionBuilder = m::mock()
            ->shouldReceive('setQueryBuilder')
            ->with($this->qb)
            ->shouldReceive('setEntityManager')
            ->with($this->em)
            ->shouldReceive('setParams')
            ->with(array())
            ->shouldReceive('buildWhereExpression')
            ->with(array('name' => 'bob'), 'mf')
            ->andReturn('mf.name = ?0')
            ->shouldReceive('getParams')
            ->andReturn(array('bob'))
            ->getMock();

        $this->sm->setService('ExpressionBuilder', $expressionBuilder);

        $this->qb->shouldReceive('addSelect')
            ->with('m')
            ->shouldReceive('getEntityManager')
            ->andReturn($this->em)
            ->shouldReceive('leftJoin')
            ->with('m.foo', 'mf', 'WITH', 'mf.name = ?0')
            ->shouldReceive('addSelect')
            ->with('mf')
            ->shouldReceive('getRootEntities')
            ->andReturn(['\Some\Entity']);

        $metadata = new \stdClass();
        $metadata->associationMappings = [
            'foo' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ]
        ];

        $this->em->shouldReceive('getClassMetadata')
            ->with('\Some\Entity')
            ->andReturn($metadata);

        $this->sut->build($config);

        $this->assertEquals(array('bob'), $this->sut->getParams());
    }

    /**
     * @group bundle_query
     */
    public function testBuildWithChildrenWithCriteriaWithMultipleChildren()
    {
        $config = array(
            'children' => array(
                'foo' => array(
                    'criteria' => array(
                        'name' => 'bob'
                    )
                ),
                'fee' => array(
                    'criteria' => array(
                        'name' => 'bob'
                    )
                )
            )
        );

        $expressionBuilder = m::mock()
            ->shouldReceive('setQueryBuilder')
            ->with($this->qb)
            ->shouldReceive('setEntityManager')
            ->with($this->em)
            // First child
            ->shouldReceive('setParams')
            ->with(array())
            ->shouldReceive('buildWhereExpression')
            ->with(array('name' => 'bob'), 'mf')
            ->andReturn('mf.name = ?0')
            ->shouldReceive('getParams')
            ->andReturn(array('bob'))
            ->once()
            // Second child
            ->shouldReceive('setParams')
            ->with(array('bob'))
            ->shouldReceive('buildWhereExpression')
            ->with(array('name' => 'bob'), 'mf0')
            ->andReturn('mf0.name = ?1')
            ->shouldReceive('getParams')
            ->andReturn(array('bob', 'bob'))
            ->once()
            ->getMock();

        $this->sm->setService('ExpressionBuilder', $expressionBuilder);

        $this->qb->shouldReceive('addSelect')
            ->with('m')
            // First child
            ->shouldReceive('getEntityManager')
            ->andReturn($this->em)
            ->shouldReceive('leftJoin')
            ->with('m.foo', 'mf', 'WITH', 'mf.name = ?0')
            ->shouldReceive('addSelect')
            ->with('mf')
            // Second child
            ->shouldReceive('leftJoin')
            ->with('m.fee', 'mf0', 'WITH', 'mf0.name = ?1')
            ->shouldReceive('addSelect')
            ->with('mf0')
            ->shouldReceive('getRootEntities')
            ->andReturn(['\Some\Entity']);

        $metadata = new \stdClass();
        $metadata->associationMappings = [
            'foo' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ],
            'fee' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ]
        ];

        $this->em->shouldReceive('getClassMetadata')
            ->with('\Some\Entity')
            ->andReturn($metadata);

        $this->sut->build($config);

        $this->assertEquals(array('bob', 'bob'), $this->sut->getParams());
    }

    /**
     * @group bundle_query
     */
    public function testBuildWithSimpleChild()
    {
        $config = array(
            'children' => array(
                'foo'
            )
        );

        $this->qb->shouldReceive('addSelect')
            ->once()
            ->with('m')
            ->shouldReceive('leftJoin')
            ->once()
            ->with('m.foo', 'mf', null, null)
            ->shouldReceive('addSelect')
            ->once()
            ->with('mf')
            ->shouldReceive('getRootEntities')
            ->andReturn(['\Some\Entity']);

        $metadata = new \stdClass();
        $metadata->associationMappings = [
            'foo' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ],
            'fee' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ]
        ];

        $this->em->shouldReceive('getClassMetadata')
            ->with('\Some\Entity')
            ->andReturn($metadata);

        $this->sut->build($config);
    }

    /**
     * @group bundle_query
     */
    public function testBuildWithSimpleChildRequired()
    {
        $config = array(
            'children' => array(
                'foo' => array(
                    'required' => true,
                )
            )
        );

        $this->qb->shouldReceive('addSelect')
            ->once()
            ->with('m')
            ->shouldReceive('innerJoin')
            ->once()
            ->with('m.foo', 'mf', null, null)
            ->shouldReceive('addSelect')
            ->with('mf')
            ->shouldReceive('getRootEntities')
            ->andReturn(['\Some\Entity']);

        $metadata = new \stdClass();
        $metadata->associationMappings = [
            'foo' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ]
        ];

        $this->em->shouldReceive('getClassMetadata')
            ->with('\Some\Entity')
            ->andReturn($metadata);

        $this->sut->build($config);
    }

    /**
     * @group bundle_query
     */
    public function testBuildWithSimpleChildRequireNone()
    {
        $config = array(
            'children' => array(
                'foo' => array(
                    'requireNone' => true,
                )
            )
        );

        $this->qb->shouldReceive('addSelect')
            ->once()
            ->with('m')
            ->shouldReceive('leftJoin')
            ->once()
            ->with('m.foo', 'mf', null, null)
            ->shouldReceive('addSelect')
            ->once()
            ->with('mf')
            ->shouldReceive('andWhere')
            ->once()
            ->with('mf.id IS NULL')
            ->shouldReceive('getRootEntities')
            ->andReturn(['\Some\Entity']);

        $metadata = new \stdClass();
        $metadata->associationMappings = [
            'foo' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ],
            'fee' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ]
        ];

        $this->em->shouldReceive('getClassMetadata')
            ->with('\Some\Entity')
            ->andReturn($metadata);

        $this->sut->build($config);
    }

    /**
     * @group bundle_query
     */
    public function testBuildWithSimpleChildWithSortOrder()
    {
        $config = array(
            'sort' => 'foo',
            'order' => 'ASC',
            'children' => array(
                'foo'
            )
        );

        $this->qb->shouldReceive('addSelect')
            ->once()
            ->with('m')
            ->shouldReceive('leftJoin')
            ->once()
            ->with('m.foo', 'mf', null, null)
            ->shouldReceive('addSelect')
            ->once()
            ->with('mf')
            ->shouldReceive('addOrderBy')
            ->once()
            ->with('m.foo', 'ASC')
            ->shouldReceive('getRootEntities')
            ->andReturn(['\Some\Entity']);

        $metadata = new \stdClass();
        $metadata->associationMappings = [
            'foo' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ],
            'fee' => [
                'targetEntity' => [
                    '\Some\Entity'
                ]
            ]
        ];

        $this->em->shouldReceive('getClassMetadata')
            ->with('\Some\Entity')
            ->andReturn($metadata);

        $this->sut->build($config);
    }
}
