<?php

/**
 * Si Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Si\Applied;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Si\Si as SiHandler;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement as SiRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Si\Si as Qry;

/**
 * Si Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SiTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new SiHandler();
        $this->mockRepo('SeriousInfringement', SiRepo::class);

        parent::setUp();
    }

    /**
     * tests handleQuery
     */
    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $si = m::mock(SiEntity::class)->makePartial();
        $si->shouldReceive('serialize')
            ->andReturn(['foo']);

        $this->repoMap['SeriousInfringement']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($si);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(['foo'], $result->serialize());
    }
}
