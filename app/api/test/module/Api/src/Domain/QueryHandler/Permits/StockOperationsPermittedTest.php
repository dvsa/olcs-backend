<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueRunScoringPermitted as QueueRunScoringPermittedQry;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueAcceptScoringPermitted as QueueAcceptScoringPermittedQry;
use Dvsa\Olcs\Transfer\Query\Permits\StockOperationsPermitted as StockOperationsPermittedQry;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\StockOperationsPermitted as StockOperationsPermittedHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class StockOperationsPermittedTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = m::mock(StockOperationsPermittedHandler::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    /**
     * @dataProvider scenariosProvider
     */
    public function testHandleQuery($scoringPermittedResult, $acceptPermittedResult, $expectedResult)
    {
        $stockId = 7;

        $queryHandler = m::mock(AbstractQueryHandler::class);
        $queryHandler->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($scoringPermittedResult, $acceptPermittedResult, $stockId) {
                $this->assertEquals($stockId, $query->getId());
                if ($query instanceof QueueRunScoringPermittedQry) {
                    return ['result' => $scoringPermittedResult];
                } elseif ($query instanceof QueueAcceptScoringPermittedQry) {
                    return ['result' => $acceptPermittedResult];
                }
            });

        $this->sut->shouldReceive('getQueryHandler')
            ->andReturn($queryHandler);

        $result = $this->sut->handleQuery(StockOperationsPermittedQry::create(['id' => $stockId]));
        $this->assertEquals($expectedResult, $result);
    }

    public function scenariosProvider()
    {
        return [
            [
                true,
                true,
                ['scoringPermitted' => true, 'acceptPermitted' => true]
            ],
            [
                true,
                false,
                ['scoringPermitted' => true, 'acceptPermitted' => false]
            ],
            [
                false,
                true,
                ['scoringPermitted' => false, 'acceptPermitted' => true]
            ],
            [
                false,
                false,
                ['scoringPermitted' => false, 'acceptPermitted' => false]
            ],
        ];
    }
}
