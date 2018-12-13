<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Cli\Domain\Command\Permits\RefreshCandidatePermitValues as RefreshCandidatePermitValuesCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\RefreshCandidatePermitValues as RefreshCandidatePermitValuesHandler;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Refresh Candidate Permit Values test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RefreshCandidatePermitValuesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new RefreshCandidatePermitValuesHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $stockId = 8;

        $candidatePermit1 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit2 = m::mock(IrhpCandidatePermit::class);
        $candidatePermit3 = m::mock(IrhpCandidatePermit::class);

        $candidatePermit1->shouldReceive('refreshApplicationScoreAndIntensityOfUse')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('saveOnFlush')
            ->with($candidatePermit1)
            ->once()
            ->ordered()
            ->globally();

        $candidatePermit2->shouldReceive('refreshApplicationScoreAndIntensityOfUse')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('saveOnFlush')
            ->with($candidatePermit2)
            ->once()
            ->ordered()
            ->globally();

        $candidatePermit3->shouldReceive('refreshApplicationScoreAndIntensityOfUse')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('saveOnFlush')
            ->with($candidatePermit3)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('flushAll')
            ->once()
            ->ordered()
            ->globally();

        $candidatePermits = [
            $candidatePermit1,
            $candidatePermit2,
            $candidatePermit3
        ];

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getInStock')
            ->with($stockId)
            ->andReturn($candidatePermits);

        $result = $this->sut->handleCommand(
            RefreshCandidatePermitValuesCommand::create(['stockId' => $stockId])
        );

        $expectedMessages = [
            '3 candidate permits to be updated',
            'Candidate permits updated',
        ];

        $this->assertEquals(
            $expectedMessages,
            $result->getMessages()
        );
    }
}
