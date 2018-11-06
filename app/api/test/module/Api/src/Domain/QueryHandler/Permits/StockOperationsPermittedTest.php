<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueRunScoringPermitted as QueueRunScoringPermittedQry;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueAcceptScoringPermitted as QueueAcceptScoringPermittedQry;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\StockOperationsPermitted as StockOperationsPermittedHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Query\Permits\StockOperationsPermitted as StockOperationsPermittedQry;
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

        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $stockId = 7;
        $stockStatusId = IrhpPermitStock::STATUS_SCORING_NEVER_RUN;
        $stockStatusDescription = 'Stock scoring never run';

        $stockStatus = m::mock(RefData::class);
        $stockStatus->shouldReceive('getId')
            ->andReturn($stockStatusId);
        $stockStatus->shouldReceive('getDescription')
            ->andReturn($stockStatusDescription);

        $stock = m::mock(IrhpPermitStock::class);
        $stock->shouldReceive('getStatus')
            ->andReturn($stockStatus);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($stock);

        $queryHandler = m::mock(AbstractQueryHandler::class);
        $queryHandler->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertEquals($stockId, $query->getId());
                if ($query instanceof QueueRunScoringPermittedQry) {
                    return [
                        'result' => 'scoringPermittedResult',
                        'message' => 'scoringPermittedMessage'
                    ];
                } elseif ($query instanceof QueueAcceptScoringPermittedQry) {
                    return [
                        'result' => 'acceptPermittedResult',
                        'message' => 'acceptPermittedMessage'
                    ];
                }
            });

        $this->sut->shouldReceive('getQueryHandler')
            ->andReturn($queryHandler);

        $expectedResult = [
            'stockStatusId' => $stockStatusId,
            'stockStatusMessage' => $stockStatusDescription,
            'scoringPermitted' => 'scoringPermittedResult',
            'scoringMessage' => 'scoringPermittedMessage',
            'acceptPermitted' => 'acceptPermittedResult',
            'acceptMessage' => 'acceptPermittedMessage'
        ];

        $result = $this->sut->handleQuery(StockOperationsPermittedQry::create(['id' => $stockId]));
        $this->assertEquals($expectedResult, $result);
    }
}
