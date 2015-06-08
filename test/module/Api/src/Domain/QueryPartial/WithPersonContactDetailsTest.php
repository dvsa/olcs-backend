<?php

/**
 * WithPersonContactDetails Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryPartial;

use Dvsa\Olcs\Api\Domain\QueryPartial\WithPersonContactDetails;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryPartial\QueryPartialInterface;

/**
 * WithPersonContactDetails Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class WithPersonContactDetailsTest extends QueryPartialTestCase
{
    public function setUp()
    {
        $with = m::mock(QueryPartialInterface::class);

        $with->shouldReceive('modifyQuery')->times(5)->andReturnSelf();
        $this->sut = new WithPersonContactDetails($with);

        parent::setUp();
    }

    public function testModifyQuery()
    {
        $this->qb->shouldReceive('getRootAliases')
            ->andReturn([0 => 'entityAlias'])
            ->shouldReceive('setMaxResults')
            ->with(1)
            ->andReturnSelf();

        $this->sut->modifyQuery($this->qb, ['myContactDetailsColumn']);
    }
}
