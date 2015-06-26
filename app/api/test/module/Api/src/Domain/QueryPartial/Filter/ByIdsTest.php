<?php

/**
 * Filter By Ids Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryPartial\Filter;

use Dvsa\Olcs\Api\Domain\QueryPartial\Filter\ByIds;
use Dvsa\OlcsTest\Api\Domain\QueryPartial\QueryPartialTestCase;
use Mockery as m;

/**
 * Filter By Ids Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ByIdsTest extends QueryPartialTestCase
{
    public function setUp()
    {
        $this->sut = new ByIds();

        parent::setUp();
    }

    public function testModifyQuery()
    {
        $ids = [111, 222];

        $expr = m::mock();

        $this->qb->shouldReceive('getRootAliases')
            ->andReturn(['a'])
            ->shouldReceive('andWhere')
            ->once()
            ->with($expr)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->once()
            ->with('byIds', [111, 222])
            ->andReturnSelf();

        $this->qb->shouldReceive('expr->in')
            ->with('a.id', ':byIds')
            ->andReturn($expr);

        $this->sut->modifyQuery($this->qb, [$ids]);
    }
}
