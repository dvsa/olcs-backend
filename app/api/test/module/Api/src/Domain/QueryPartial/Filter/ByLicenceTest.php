<?php

/**
 * Filter By Licence Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryPartial\Filter;

use Dvsa\Olcs\Api\Domain\QueryPartial\Filter\ByLicence;
use Dvsa\OlcsTest\Api\Domain\QueryPartial\QueryPartialTestCase;
use Mockery as m;

/**
 * Filter By Licence Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ByLicenceTest extends QueryPartialTestCase
{
    public function setUp()
    {
        $this->sut = new ByLicence();

        parent::setUp();
    }

    public function testModifyQuery()
    {
        $licenceId = 69;

        $expr = m::mock();

        $this->qb->shouldReceive('getRootAliases')
            ->andReturn(['a'])
            ->shouldReceive('andWhere')
            ->once()
            ->with($expr)
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->once()
            ->with('licenceId', 69)
            ->andReturnSelf();

        $this->qb->shouldReceive('expr->eq')
            ->with('a.licence', ':licenceId')
            ->andReturn($expr);

        $this->sut->modifyQuery($this->qb, [$licenceId]);
    }
}
