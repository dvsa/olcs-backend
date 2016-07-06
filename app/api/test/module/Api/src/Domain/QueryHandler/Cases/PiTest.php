<?php

/**
 * Pi Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Pi;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Pi as PiRepo;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Transfer\Query\Cases\Pi as Qry;
use Mockery as m;

/**
 * Pi Test
 */
class PiTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Pi();
        $this->mockRepo('Pi', PiRepo::class);

        parent::setUp();
    }

    /**
     * Tests an empty result is correctly dealt with
     */
    public function testHandleQueryEmptyResult()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Pi']
            ->shouldReceive('disableSoftDeleteable')
            ->with(
                [
                    \Dvsa\Olcs\Api\Entity\Pi\Reason::class
                ]
            )
            ->once()
            ->shouldReceive('fetchUsingCase')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn(null);

        $this->assertEquals([], $this->sut->handleQuery($query));
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $pi = m::mock(PiEntity::class);
        $pi->shouldReceive('flattenSlaTargetDates')->once()->andReturn([]);

        $this->repoMap['Pi']
            ->shouldReceive('disableSoftDeleteable')
            ->with(
                [
                    \Dvsa\Olcs\Api\Entity\Pi\Reason::class
                ]
            )
            ->once()
            ->shouldReceive('fetchUsingCase')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($pi);

        $this->assertInstanceOf(Result::class, $this->sut->handleQuery($query));
    }
}
