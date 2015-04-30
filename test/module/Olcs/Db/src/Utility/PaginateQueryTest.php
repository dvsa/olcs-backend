<?php

/**
 * PaginateQuery Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace OlcsTest\Db\Utility;

use Olcs\Db\Utility\PaginateQuery;
use Mockery as m;
use PHPUnit_Framework_TestCase;

/**
 * PaginateQuery Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PaginateQueryTest extends PHPUnit_Framework_TestCase
{
    protected $qb;
    protected $sut;

    protected function setUp()
    {
        $this->qb = m::mock();

        $this->sut = new PaginateQuery();
        $this->sut->setQueryBuilder($this->qb);
    }

    /**
     * @group paginate_query
     * @expectedException \Olcs\Db\Exceptions\PaginationException
     */
    public function testSetPageOptionWithoutNumber()
    {
        $options = array('page' => 'A');

        $this->sut->setOptions($options);
    }

    /**
     * @group paginate_query
     * @expectedException \Olcs\Db\Exceptions\PaginationException
     */
    public function testSetLimitOptionWithoutNumber()
    {
        $options = array('limit' => 'A');

        $this->sut->setOptions($options);
    }

    /**
     * @group paginate_query
     * @expectedException \Olcs\Db\Exceptions\PaginationException
     */
    public function testSetOrderOptionWithoutAscOrDesc()
    {
        $options = array('order' => 'BLAP');

        $this->sut->setOptions($options);
    }

    /**
     * @group paginate_query
     */
    public function testFilterQueryWithoutOptions()
    {
        $options = array();

        $this->sut->setOptions($options);

        $this->qb->shouldReceive('setFirstResult')->never()
            ->shouldReceive('setMaxResults')->never()
            ->shouldReceive('orderBy')->never();

        $this->sut->filterQuery();
    }

    /**
     * @group paginate_query
     */
    public function testFilterQueryWithoutFilterOptions()
    {
        $options = array(
            'page' => 1,
            'order' => 'ASC'
        );

        $this->sut->setOptions($options);

        $this->qb->shouldReceive('setFirstResult')->never()
            ->shouldReceive('setMaxResults')->never()
            ->shouldReceive('orderBy')->never();

        $this->sut->filterQuery();
    }

    /**
     * @group paginate_query
     */
    public function testFilterQueryWithLimitOptions()
    {
        $options = array(
            'page' => 1,
            'order' => 'ASC',
            'limit' => 10
        );

        $this->sut->setOptions($options);

        $this->qb->shouldReceive('setFirstResult')
            ->with(0)
            ->shouldReceive('setMaxResults')
            ->with(10)
            ->shouldReceive('orderBy')->never();

        $this->sut->filterQuery();
    }

    /**
     * @group paginate_query
     */
    public function testFilterQueryWithOrderOptions()
    {
        $options = array(
            'page' => 1,
            'order' => 'ASC',
            'sort' => 'foo'
        );

        $this->sut->setOptions($options);

        $this->qb->shouldReceive('setFirstResult')->never()
            ->shouldReceive('setMaxResults')->never()
            ->shouldReceive('addOrderBy')
            ->with('m.foo', 'ASC');

        $this->sut->filterQuery();
    }

    /**
     * @group paginate_query
     */
    public function testFilterQueryWithAllOptions()
    {
        $options = array(
            'page' => 1,
            'limit' => 10,
            'order' => 'ASC',
            'sort' => 'foo'
        );

        $this->sut->setOptions($options);

        $this->qb->shouldReceive('setFirstResult')
            ->with(0)
            ->shouldReceive('setMaxResults')
            ->with(10)
            ->shouldReceive('addOrderBy')
            ->with('m.foo', 'ASC');

        $this->sut->filterQuery();
    }

    /**
     * @group paginate_query
     */
    public function testFilterQueryWithPageNumber()
    {
        $options = array(
            'page' => 3,
            'limit' => 10,
            'order' => 'ASC',
            'sort' => 'foo'
        );

        $this->sut->setOptions($options);

        $this->qb->shouldReceive('setFirstResult')
            ->with(20)
            ->shouldReceive('setMaxResults')
            ->with(10)
            ->shouldReceive('addOrderBy')
            ->with('m.foo', 'ASC');

        $this->sut->filterQuery();
    }

    /**
     * @group paginate_query
     */
    public function testFilterQueryWithPageNumberAndLimit()
    {
        $options = array(
            'page' => 3,
            'limit' => 7,
            'order' => 'ASC',
            'sort' => 'foo'
        );

        $this->sut->setOptions($options);

        $this->qb->shouldReceive('setFirstResult')
            ->with(14)
            ->shouldReceive('setMaxResults')
            ->with(7)
            ->shouldReceive('addOrderBy')
            ->with('m.foo', 'ASC');

        $this->sut->filterQuery();
    }

    /**
     * @group paginate_query
     */
    public function testFilterQueryWithNoLimit()
    {
        $options = array(
            'page' => 3,
            'limit' => 'all'
        );

        $this->sut->setOptions($options);

        $this->qb->shouldReceive('setFirstResult')->never()
            ->shouldReceive('setMaxResults')->never()
            ->shouldReceive('addOrderBy')->never();

        $this->sut->filterQuery();
    }
}
