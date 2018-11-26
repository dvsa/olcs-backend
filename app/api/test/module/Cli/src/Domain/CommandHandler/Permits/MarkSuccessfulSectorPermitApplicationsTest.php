<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitSectorQuota as IrhpPermitSectorQuotaRepo;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulSectorPermitApplications
    as MarkSuccessfulSectorPermitApplicationsCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\MarkSuccessfulSectorPermitApplications
    as MarkSuccessfulSectorPermitApplicationsHandler;
use Mockery as m;

/**
 * Mark Successful Sector Permit Applications test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class MarkSuccessfulSectorPermitApplicationsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new MarkSuccessfulSectorPermitApplicationsHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);
        $this->mockRepo('IrhpPermitSectorQuota', IrhpPermitSectorQuotaRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $stockId = 8;

        $this->repoMap['IrhpPermitSectorQuota']->shouldReceive('fetchByNonZeroQuota')
            ->with($stockId)
            ->andReturn(
                [
                    ['sectorId' => 7, 'quotaNumber' => 4],
                    ['sectorId' => 3, 'quotaNumber' => 2],
                    ['sectorId' => 4, 'quotaNumber' => 0],
                    ['sectorId' => 2, 'quotaNumber' => 3]
                ]
            );

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getScoreOrderedIdsBySectorInScope')
            ->with($stockId, 7)
            ->andReturn([4, 5]);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getScoreOrderedIdsBySectorInScope')
            ->with($stockId, 3)
            ->andReturn([10, 13, 15, 16]);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getScoreOrderedIdsBySectorInScope')
            ->with($stockId, 4)
            ->andReturn([24, 25, 26]);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getScoreOrderedIdsBySectorInScope')
            ->with($stockId, 2)
            ->andReturn([]);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('markAsSuccessful')
            ->with([4, 5, 10, 13])
            ->once();

        $this->sut->handleCommand(
            MarkSuccessfulSectorPermitApplicationsCommand::create(['stockId' => $stockId])
        );
    }
}
