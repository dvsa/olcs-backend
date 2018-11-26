<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\CheckRunScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckRunScoringPrerequisites as CheckRunScoringPrerequisitesQry;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class CheckRunScoringPrerequisitesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CheckRunScoringPrerequisites();
        $this->mockRepo('IrhpPermitRange', IrhpPermitRangeRepo::class);
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        parent::setUp();
    }

    /**
     * @dataProvider scenariosProvider
     */
    public function testHandleQuery(
        $lastOpenWindow,
        $applicationIds,
        $combinedRangeSize,
        $permitCount,
        $expectedResult,
        $expectedMessage
    ) {
        $stockId = 25;

        if (is_null($lastOpenWindow)) {
            $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchLastOpenWindowByStockId')
                ->with($stockId)
                ->andThrow(NotFoundException::class);
        } else {
            $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchLastOpenWindowByStockId')
                ->with($stockId)
                ->andReturn($lastOpenWindow);
        }

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchApplicationIdsAwaitingScoring')
            ->with($stockId)
            ->andReturn($applicationIds);

        $this->repoMap['IrhpPermitRange']->shouldReceive('getCombinedRangeSize')
            ->with($stockId)
            ->andReturn($combinedRangeSize);

        $this->repoMap['IrhpPermit']->shouldReceive('getPermitCount')
            ->with($stockId)
            ->andReturn($permitCount);

        $result = $this->sut->handleQuery(
            CheckRunScoringPrerequisitesQry::create(['id' => $stockId])
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
                null,
                [1, 2, 3],
                50,
                25,
                true,
                'Prerequisites passed'
            ],
            [
                m::mock(IrhpPermitWindow::class),
                [1, 2, 3],
                50,
                25,
                false,
                'A window is currently open within the stock'
            ],
            [
                null,
                [],
                50,
                25,
                false,
                'No under consideration applications available'
            ],
            [
                null,
                [1, 2, 3],
                null,
                0,
                false,
                'No ranges available in this stock'
            ],
            [
                null,
                [1, 2, 3],
                25,
                25,
                false,
                'No free permits available within the stock'
            ],
        ];
    }
}
