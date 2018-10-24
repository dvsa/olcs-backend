<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\QueueAcceptScoringPermitted;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueAcceptScoringPermitted as QueueAcceptScoringPermittedQry;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class QueueAcceptScoringPermittedTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        $this->sut = m::mock(QueueAcceptScoringPermitted::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    /**
     * @dataProvider permittedScenariosProvider
     */
    public function testHandleQuery($prerequisiteResult, $prerequisiteMessage)
    {
        $stockId = 28;

        $stock = m::mock(IrhpPermitStock::class);
        $stock->shouldReceive('getStatus->getId')
            ->andReturn(IrhpPermitStock::STATUS_SCORING_SUCCESSFUL);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($stock);

        $queryHandler = m::mock(AbstractQueryHandler::class);
        $queryHandler->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($stockId, $prerequisiteResult, $prerequisiteMessage) {
                $this->assertInstanceOf(CheckAcceptScoringPrerequisites::class, $query);
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => $prerequisiteResult,
                    'message' => $prerequisiteMessage
                ];
            });

        $this->sut->shouldReceive('getQueryHandler')
            ->andReturn($queryHandler);

        $result = $this->sut->handleQuery(
            CheckAcceptScoringPrerequisites::create(['id' => $stockId])
        );

        $this->assertEquals(
            [
                'result' => $prerequisiteResult,
                'message' => $prerequisiteMessage
            ],
            $result
        );
    }

    /**
     * @dataProvider unpermittedStatusesProvider
     */
    public function testHandleQueryUnpermittedStatus($statusId)
    {
        $stockId = 28;

        $stock = m::mock(IrhpPermitStock::class);
        $stock->shouldReceive('getStatus->getId')
            ->andReturn($statusId);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($stock);

        $result = $this->sut->handleQuery(
            CheckAcceptScoringPrerequisites::create(['id' => $stockId])
        );

        $this->assertEquals(
            [
                'result' => false,
                'message' => 'Stock status needs to be stock_scoring_successful, but is currently ' . $statusId
            ],
            $result
        );
    }

    public function permittedScenariosProvider()
    {
        return [
            [true, 'Prerequisites ok'],
            [false, 'Prerequisites fail']
        ];
    }

    public function unpermittedStatusesProvider()
    {
        return [
            [IrhpPermitStock::STATUS_SCORING_NEVER_RUN],
            [IrhpPermitStock::STATUS_SCORING_PENDING],
            [IrhpPermitStock::STATUS_SCORING_IN_PROGRESS],
            [IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL],
            [IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL],
            [IrhpPermitStock::STATUS_ACCEPT_PENDING],
            [IrhpPermitStock::STATUS_ACCEPT_IN_PROGRESS],
            [IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL],
            [IrhpPermitStock::STATUS_ACCEPT_PREREQUISITE_FAIL],
            [IrhpPermitStock::STATUS_ACCEPT_UNEXPECTED_FAIL],
        ];
    }
}
