<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitJurisdictionQuota as IrhpPermitJurisdictionQuotaRepo;
use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkSuccessfulDaPermitApplications
    as MarkSuccessfulDaPermitApplicationsCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\MarkSuccessfulDaPermitApplications
    as MarkSuccessfulDaPermitApplicationsHandler;
use Mockery as m;

/**
 * Mark Successful DA Permit Applications test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class MarkSuccessfulDaPermitApplicationsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new MarkSuccessfulDaPermitApplicationsHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);
        $this->mockRepo('IrhpPermitJurisdictionQuota', IrhpPermitJurisdictionQuotaRepo::class);

        parent::setUp();
    }

    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $stockId = 8;

        $this->repoMap['IrhpPermitJurisdictionQuota']->shouldReceive('fetchByNonZeroQuota')
            ->with($stockId)
            ->andReturn(
                [
                    ['jurisdictionId' => 4, 'quotaNumber' => 10],
                    ['jurisdictionId' => 6, 'quotaNumber' => 4],
                    ['jurisdictionId' => 8, 'quotaNumber' => 15]
                ]
            );

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getSuccessfulDaCountInScope')
            ->with($stockId, 4)
            ->andReturn(9);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getSuccessfulDaCountInScope')
            ->with($stockId, 6)
            ->andReturn(6);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getSuccessfulDaCountInScope')
            ->with($stockId, 8)
            ->andReturn(8);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getUnsuccessfulScoreOrderedIdsInScope')
            ->with($stockId, 4)
            ->andReturn([4, 5, 6]);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getUnsuccessfulScoreOrderedIdsInScope')
            ->with($stockId, 8)
            ->andReturn([12, 14, 20, 25]);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('markAsSuccessful')
            ->with([4, 12, 14, 20, 25])
            ->once();

        $this->sut->handleCommand(
            MarkSuccessfulDaPermitApplicationsCommand::create(['stockId' => $stockId])
        );
    }
}
