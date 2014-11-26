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
use PHPUnit_Framework_TestCase;

/**
 * BundleQuery Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BundleQueryTest extends PHPUnit_Framework_TestCase
{
    protected $qb;
    protected $sm;
    protected $sut;

    protected function setUp()
    {
        $this->qb = m::mock();
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);

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
            ->with('mf');

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

        $mockEntityManager = m::mock();

        $expressionBuilder = m::mock()
            ->shouldReceive('setQueryBuilder')
            ->with($this->qb)
            ->shouldReceive('setEntityManager')
            ->with($mockEntityManager)
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
            ->andReturn($mockEntityManager)
            ->shouldReceive('leftJoin')
            ->with('m.foo', 'mf', 'WITH', 'mf.name = ?0')
            ->shouldReceive('addSelect')
            ->with('mf');

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

        $mockEntityManager = m::mock();

        $expressionBuilder = m::mock()
            ->shouldReceive('setQueryBuilder')
            ->with($this->qb)
            ->shouldReceive('setEntityManager')
            ->with($mockEntityManager)
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
            ->andReturn($mockEntityManager)
            ->shouldReceive('leftJoin')
            ->with('m.foo', 'mf', 'WITH', 'mf.name = ?0')
            ->shouldReceive('addSelect')
            ->with('mf')
            // Second child
            ->shouldReceive('leftJoin')
            ->with('m.fee', 'mf0', 'WITH', 'mf0.name = ?1')
            ->shouldReceive('addSelect')
            ->with('mf0');

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
            ->with('m')
            ->shouldReceive('leftJoin')
            ->with('m.foo', 'mf', null, null)
            ->shouldReceive('addSelect')
            ->with('mf');

        $this->sut->build($config);
    }
}
