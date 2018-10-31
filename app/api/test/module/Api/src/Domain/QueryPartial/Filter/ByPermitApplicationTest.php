<?php

/**
 * Filter By Application Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryPartial\Filter;

use Dvsa\Olcs\Api\Domain\QueryPartial\Filter\ByPermitApplication;
use Dvsa\OlcsTest\Api\Domain\QueryPartial\QueryPartialTestCase;
use Mockery as m;

class ByPermitApplicationTest extends QueryPartialTestCase
{
    public function setUp()
    {
        $this->sut = new ByPermitApplication();

        parent::setUp();
    }

    public function testModifyQuery()
    {
        $ecmtPermitApplicationId = 7;

        $expr = m::mock();

        $this->qb->shouldReceive('getRootAliases')
            ->andReturn(['a'])
            ->shouldReceive('andWhere')
            ->once()
            ->with($expr)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->once()
            ->with('ecmtPermitApplicationId', 7)
            ->andReturnSelf();

        $this->qb->shouldReceive('expr->eq')
            ->with('a.ecmtPermitApplication', ':ecmtPermitApplicationId')
            ->andReturn($expr);

        $this->sut->modifyQuery($this->qb, [$ecmtPermitApplicationId]);
    }
}
