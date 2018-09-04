<?php

namespace Dvsa\OlcsTest\Cli\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Cli\Domain\QueryHandler\Permits\StockLackingRandomisedScore;
use Dvsa\Olcs\Cli\Domain\Query\Permits\StockLackingRandomisedScore as StockLackingRandomisedScoreQuery;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Stock Lacking Randomised Score test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockLackingRandomisedScoreTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new StockLackingRandomisedScore();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    /**
     * @dataProvider scenariosProvider
     */
    public function testHandleQuery($permitCount, $result)
    {
        $stockId = 10;

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getCountLackingRandomisedScore')
            ->with($stockId)
            ->andReturn($permitCount);

        $query = m::mock(StockLackingRandomisedScoreQuery::class);
        $query->shouldReceive('getStockId')
            ->andReturn($stockId);

        $this->assertEquals(
            ['result' => $result],
            $this->sut->handleQuery($query)
        );
    }

    public function scenariosProvider()
    {
        return [
            [100, true],
            [0, false],
            [-3, false]
        ];
    }
}
