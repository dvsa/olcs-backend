<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\CheckAcceptScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites as CheckAcceptScoringPrerequisitesQry;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class CheckAcceptScoringPrerequisitesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CheckAcceptScoringPrerequisites();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);
        $this->mockRepo('IrhpPermitRange', IrhpPermitRangeRepo::class);
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);

        parent::setUp();
    }

    /**
     * @dataProvider scenariosProvider
     */
    public function testHandleQuery(
        $combinedRangeSize,
        $permitCount,
        $successfulCount,
        $expectedResult,
        $expectedMessage
    ) {
        $stockId = 25;

        $this->repoMap['IrhpPermitRange']->shouldReceive('getCombinedRangeSize')
            ->with($stockId)
            ->andReturn($combinedRangeSize);

        $this->repoMap['IrhpPermit']->shouldReceive('getPermitCount')
            ->with($stockId)
            ->andReturn($permitCount);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getSuccessfulCountInScope')
            ->with($stockId)
            ->andReturn($successfulCount);

        $result = $this->sut->handleQuery(
            CheckAcceptScoringPrerequisitesQry::create(['id' => $stockId])
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
            [null, 0, 0, false, 'No ranges available in this stock'],
            [40, 10, 29, true, 'Prerequisites passed'],
            [50, 15, 40, false, 'Insufficient permits available - 35 available, 40 required'],
        ];
    }
}
