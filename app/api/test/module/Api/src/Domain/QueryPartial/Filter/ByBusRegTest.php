<?php

/**
 * Filter By Bus Reg Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryPartial\Filter;

use Dvsa\Olcs\Api\Domain\QueryPartial\Filter\ByBusReg;
use Dvsa\OlcsTest\Api\Domain\QueryPartial\QueryPartialTestCase;
use Mockery as m;

/**
 * Filter By Bus Reg Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ByBusRegTest extends QueryPartialTestCase
{
    public function setUp()
    {
        $this->sut = new ByBusReg();

        parent::setUp();
    }

    public function testModifyQuery()
    {
        $busRegId = 69;

        $expr = m::mock();

        $this->qb->shouldReceive('getRootAliases')
            ->andReturn(['a'])
            ->shouldReceive('andWhere')
            ->once()
            ->with($expr)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->once()
            ->with('busRegId', 69)
            ->andReturnSelf();

        $this->qb->shouldReceive('expr->eq')
            ->with('a.busReg', ':busRegId')
            ->andReturn($expr);

        $this->sut->modifyQuery($this->qb, [$busRegId]);
    }
}
