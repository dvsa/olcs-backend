<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Query\Permits\DeviationData as DeviationDataQry;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueRunScoringPermitted as QueueRunScoringPermittedQry;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueAcceptScoringPermitted as QueueAcceptScoringPermittedQry;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\StockOperationsPermitted as StockOperationsPermittedHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Scoring\ScoringQueryProxy;
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

        $this->mockedSmServices = [
            'PermitsScoringScoringQueryProxy' => m::mock(ScoringQueryProxy::class),
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $stockId = 7;
        $stockStatusId = IrhpPermitStock::STATUS_SCORING_NEVER_RUN;
        $stockStatusDescription = 'Stock scoring never run';

        $deviationSourceValues = [
            [
                'candidatePermitId' => 5,
                'applicationId' => 1,
                'licNo' => 123456,
                'permitsRequired' => 12
            ],
            [
                'candidatePermitId' => 8,
                'applicationId' => 2,
                'licNo' => 455123,
                'permitsRequired' => 6
            ]
        ];

        $this->mockedSmServices['PermitsScoringScoringQueryProxy']->shouldReceive('fetchDeviationSourceValues')
            ->with($stockId)
            ->andReturn($deviationSourceValues);

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
            ->with(m::type(QueueRunScoringPermittedQry::class))
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => 'scoringPermittedResult',
                    'message' => 'scoringPermittedMessage'
                ];
            });

        $queryHandler->shouldReceive('handleQuery')
            ->with(m::type(QueueAcceptScoringPermittedQry::class))
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => 'acceptPermittedResult',
                    'message' => 'acceptPermittedMessage'
                ];
            });

        $queryHandler->shouldReceive('handleQuery')
            ->with(m::type(DeviationDataQry::class))
            ->andReturnUsing(function ($query) use ($deviationSourceValues) {
                $this->assertEquals($deviationSourceValues, $query->getSourceValues());

                return [
                    'licenceData' => [],
                    'meanDeviation' => 1.5,
                ];
            });

        $this->sut->shouldReceive('getQueryHandler')
            ->andReturn($queryHandler);

        $expectedResult = [
            'stockStatusId' => $stockStatusId,
            'stockStatusMessage' => $stockStatusDescription,
            'scoringPermitted' => 'scoringPermittedResult',
            'scoringMessage' => 'scoringPermittedMessage',
            'acceptPermitted' => 'acceptPermittedResult',
            'acceptMessage' => 'acceptPermittedMessage',
            'meanDeviation' => 1.5
        ];

        $result = $this->sut->handleQuery(StockOperationsPermittedQry::create(['id' => $stockId]));
        $this->assertEquals($expectedResult, $result);
    }
}
