<?php

/**
 * Annual Test History test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\AnnualTestHistory;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Transfer\Query\Bus\BusReg as Qry;

/**
 * Annual Test History Test
 */
class AnnualTestHistoryTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AnnualTestHistory();
        $this->mockRepo('Cases', CasesRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $this->assertEquals(['foo'], $this->sut->handleQuery($query));
    }
}
