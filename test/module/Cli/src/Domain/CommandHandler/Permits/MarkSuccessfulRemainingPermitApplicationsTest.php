<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidate as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepo;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulRemainingPermitApplications
    as MarkSuccessfulRemainingPermitApplicationsCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\MarkSuccessfulRemainingPermitApplications
    as MarkSuccessfulRemainingPermitApplicationsHandler;
use Mockery as m;

/**
 * Mark Successful Remaining Permit Applications test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class MarkSuccessfulRemainingPermitApplicationsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new MarkSuccessfulRemainingPermitApplicationsHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidateRepo::class);
        $this->mockRepo('IrhpPermit', IrhpPermit::class);
        $this->mockRepo('IrhpPermitRange', IrhpPermit::class);

        parent::setUp();
    }

    /**
     * @dataProvider scenariosProvider
     */
    public function testHandleCommand($permitCount, $successfulCount, $underConsiderationIds, $successfulIds)
    {
        $stockId = 8;

        $this->repoMap['IrhpPermitRange']->shouldReceive('getCombinedRangeSize')
            ->with($stockId)
            ->andReturn(150);

        $this->repoMap['IrhpPermit']->shouldReceive('getPermitCount')
            ->with($stockId)
            ->andReturn($permitCount);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getSuccessfulCountInScope')
            ->with($stockId)
            ->andReturn($successfulCount);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getUnsuccessfulScoreOrderedIdsInScope')
            ->with($stockId)
            ->andReturn($underConsiderationIds);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('markAsSuccessful')
            ->with($successfulIds)
            ->once();

        $this->sut->handleCommand(
            MarkSuccessfulRemainingPermitApplicationsCommand::create(['stockId' => $stockId])
        );
    }

    public function testHandleCommandZeroRemainingQuota()
    {
        $stockId = 8;

        $this->repoMap['IrhpPermitRange']->shouldReceive('getCombinedRangeSize')
            ->with($stockId)
            ->andReturn(150);

        $this->repoMap['IrhpPermit']->shouldReceive('getPermitCount')
            ->with($stockId)
            ->andReturn(75);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getSuccessfulCountInScope')
            ->with($stockId)
            ->andReturn(75);

        $this->sut->handleCommand(
            MarkSuccessfulRemainingPermitApplicationsCommand::create(['stockId' => $stockId])
        );
    }

    public function scenariosProvider()
    {
        return [
            [
                79,
                67,
                [13, 17, 41, 46, 55, 61, 80],
                [13, 17, 41, 46]
            ],
            [
                79,
                40,
                [23, 27, 51, 56, 85, 81, 90],
                [23, 27, 51, 56, 85, 81, 90]
            ]
        ];
    }
}
