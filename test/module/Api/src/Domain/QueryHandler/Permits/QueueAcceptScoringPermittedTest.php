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
    public function setUp(): void
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
        $stock->shouldReceive('statusAllowsQueueAcceptScoring')
            ->andReturn(true);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($stock);

        $queryHandler = m::mock(AbstractQueryHandler::class);

        $queryHandler->shouldReceive('handleQuery')
            ->with(m::type(CheckAcceptScoringPrerequisites::class))
            ->andReturnUsing(function ($query) use ($stockId, $prerequisiteResult, $prerequisiteMessage) {
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

    public function testHandleQueryUnpermittedStatus()
    {
        $stockId = 28;

        $stock = m::mock(IrhpPermitStock::class);
        $stock->shouldReceive('statusAllowsQueueAcceptScoring')
            ->andReturn(false);
        $stock->shouldReceive('getStatusDescription')
            ->andReturn('stock status description');

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($stock);

        $result = $this->sut->handleQuery(
            CheckAcceptScoringPrerequisites::create(['id' => $stockId])
        );

        $this->assertEquals(
            [
                'result' => false,
                'message' => 'Acceptance is not permitted when stock status is \'stock status description\''
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
}
