<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\QueueRunScoringPermitted;
use Dvsa\Olcs\Api\Domain\Query\Permits\QueueRunScoringPermitted as QueueRunScoringPermittedQry;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckRunScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class QueueRunScoringPermittedTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        $this->sut = m::mock(QueueRunScoringPermitted::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    /**
     * @dataProvider scenariosProvider
     */
    public function testHandleQuery(
        $statusId,
        $prerequisiteResult,
        $prerequisiteMessage,
        $expectedResult,
        $expectedMessage
    ) {
        $stockId = 28;

        $stock = m::mock(IrhpPermitStock::class);
        $stock->shouldReceive('getStatus->getId')
            ->andReturn($statusId);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($stock);

        $queryHandler = m::mock(AbstractQueryHandler::class);
        $queryHandler->shouldReceive('handleQuery')
            ->andReturnUsing(function ($query) use ($stockId, $prerequisiteResult, $prerequisiteMessage) {
                $this->assertInstanceOf(CheckRunScoringPrerequisites::class, $query);
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => $prerequisiteResult,
                    'message' => $prerequisiteMessage
                ];
            });

        $this->sut->shouldReceive('getQueryHandler')
            ->andReturn($queryHandler);

        $result = $this->sut->handleQuery(
            CheckRunScoringPrerequisites::create(['id' => $stockId])
        );

        $this->assertEquals(
            [
                'result' => $expectedResult,
                'message' => $expectedMessage
            ],
            $result
        );
    }

    public function scenariosProvider()
    {
        return [
            [
                IrhpPermitStock::STATUS_SCORING_NEVER_RUN,
                true,
                'Prerequisites passed',
                true,
                'Prerequisites passed',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_PENDING,
                true,
                'Prerequisites passed',
                false,
                'Scoring is not permitted when stock status is stock_scoring_pending',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_IN_PROGRESS,
                true,
                'Prerequisites passed',
                false,
                'Scoring is not permitted when stock status is stock_scoring_in_progress',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_SUCCESSFUL,
                true,
                'Prerequisites passed',
                true,
                'Prerequisites passed',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL,
                true,
                'Prerequisites passed',
                true,
                'Prerequisites passed',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL,
                true,
                'Prerequisites passed',
                true,
                'Prerequisites passed',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_PENDING,
                true,
                'Prerequisites passed',
                false,
                'Scoring is not permitted when stock status is stock_accept_pending',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_IN_PROGRESS,
                true,
                'Prerequisites passed',
                false,
                'Scoring is not permitted when stock status is stock_accept_in_progress',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL,
                true,
                'Prerequisites passed',
                false,
                'Scoring is not permitted when stock status is stock_accept_successful',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_PREREQUISITE_FAIL,
                true,
                'Prerequisites passed',
                false,
                'Scoring is not permitted when stock status is stock_accept_prereq_fail',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_UNEXPECTED_FAIL,
                true,
                'Prerequisites passed',
                false,
                'Scoring is not permitted when stock status is stock_accept_unexpected_fail',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_NEVER_RUN,
                false,
                'Prerequisites failed',
                false,
                'Prerequisites failed',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_PENDING,
                false,
                'Prerequisites failed',
                false,
                'Scoring is not permitted when stock status is stock_scoring_pending',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_IN_PROGRESS,
                false,
                'Prerequisites failed',
                false,
                'Scoring is not permitted when stock status is stock_scoring_in_progress',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_SUCCESSFUL,
                false,
                'Prerequisites failed',
                false,
                'Prerequisites failed',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_PREREQUISITE_FAIL,
                false,
                'Prerequisites failed',
                false,
                'Prerequisites failed',
            ],
            [
                IrhpPermitStock::STATUS_SCORING_UNEXPECTED_FAIL,
                false,
                'Prerequisites failed',
                false,
                'Prerequisites failed',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_PENDING,
                false,
                'Prerequisites failed',
                false,
                'Scoring is not permitted when stock status is stock_accept_pending',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_IN_PROGRESS,
                false,
                'Prerequisites failed',
                false,
                'Scoring is not permitted when stock status is stock_accept_in_progress',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_SUCCESSFUL,
                false,
                'Prerequisites failed',
                false,
                'Scoring is not permitted when stock status is stock_accept_successful',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_PREREQUISITE_FAIL,
                false,
                'Prerequisites failed',
                false,
                'Scoring is not permitted when stock status is stock_accept_prereq_fail',
            ],
            [
                IrhpPermitStock::STATUS_ACCEPT_UNEXPECTED_FAIL,
                true,
                'Prerequisites passed',
                false,
                'Scoring is not permitted when stock status is stock_accept_unexpected_fail',
            ],
        ];
    }
}
