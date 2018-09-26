<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Cli\Domain\Command\Permits\CalculateRandomAppScore
    as CalculateRandomApplicationScoreCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\CalculateRandomAppScore
    as CalculateRandomApplicationScoreHandler;
use Mockery as m;

/**
 * Calculate Random Application Score test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.co.uk>
 */
class CalculateRandomAppScoreTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CalculateRandomApplicationScoreHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $stockId = 7;
        $licenceNo = 'OB111111';

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getLicence->getLicNo')
            ->with()
            ->andReturn($licenceNo);
        $irhpPermitApplication->shouldReceive('getId')
            ->with()
            ->andReturn(1);
        $irhpPermitApplication->shouldReceive('getPermitsRequired')
            ->with()
            ->andReturn(12);

        $irhpCandidatePermit = m::mock(IrhpCandidatePermit::class);
        $irhpCandidatePermit->shouldReceive('calculateRandomFactor')
            ->with(
                [
                    'licenceData' => [$licenceNo => [1 => 12]],
                    'meanDeviation' => 1
                ]
            )
            ->andReturn(10.1);
        $irhpCandidatePermit->shouldReceive('getApplicationScore')
            ->with()
            ->andReturn(2);
        $irhpCandidatePermit->shouldReceive('getIrhpPermitApplication')
            ->with()
            ->andReturn($irhpPermitApplication);
        $irhpCandidatePermit->shouldReceive('setRandomizedScore')
            ->with(10.1 * 2)
            ->andReturn(null);
        $irhpCandidatePermit->shouldReceive('setRandomFactor')
            ->with(10.1)
            ->andReturn(null);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getIrhpCandidatePermitsForScoring')
            ->with($stockId)
            ->andReturn([$irhpCandidatePermit]);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->with($irhpCandidatePermit);

        $this->sut->handleCommand(
            CalculateRandomApplicationScoreCommand::create(['stockId' => $stockId])
        );
    }
}
