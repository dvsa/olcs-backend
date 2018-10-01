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
    private $irhpPermitApplication;

    private $irhpCandidatePermit;

    private $stockId;

    public function setUp()
    {
        $this->sut = new CalculateRandomApplicationScoreHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        $this->stockId = 7;
        $licenceNo = 'OB111111';

        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $this->irhpPermitApplication->shouldReceive('getLicence->getLicNo')
            ->with()
            ->andReturn($licenceNo);
        $this->irhpPermitApplication->shouldReceive('getId')
            ->with()
            ->andReturn(1);
        $this->irhpPermitApplication->shouldReceive('getPermitsRequired')
            ->with()
            ->andReturn(12);

        $this->irhpCandidatePermit = m::mock(IrhpCandidatePermit::class);

        $this->irhpCandidatePermit->shouldReceive('calculateRandomFactor')
            ->with(
                [
                    'licenceData' => [$licenceNo => [1 => 12]],
                    'meanDeviation' => 1
                ]
            )
            ->andReturn(10.1);
        $this->irhpCandidatePermit->shouldReceive('getApplicationScore')
            ->with()
            ->andReturn(2);
        $this->irhpCandidatePermit->shouldReceive('getIrhpPermitApplication')
            ->with()
            ->andReturn($this->irhpPermitApplication);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getIrhpCandidatePermitsForScoring')
            ->with($this->stockId)
            ->andReturn([$this->irhpCandidatePermit]);

        parent::setUp();
    }


    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $this->irhpCandidatePermit->shouldReceive('getRandomizedScore')
            ->andReturn(null);

        $this->irhpCandidatePermit->shouldReceive('setRandomizedScore')
            ->with(10.1 * 2)
            ->once();

        $this->irhpCandidatePermit->shouldReceive('setRandomFactor')
            ->with(10.1)
            ->once();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->with($this->irhpCandidatePermit);

        $this->sut->handleCommand(
            CalculateRandomApplicationScoreCommand::create(['stockId' => $this->stockId])
        );
    }

    /**
     * In this scenario, the randomised score is already set.
     * Therefore, the command should skip some unnecessary operations.
     */
    public function testHandleCommandRandomizedScoreAlreadySet()
    {
        $this->irhpCandidatePermit->shouldReceive('getRandomizedScore')
            ->andReturn(1); //this time randomised score is already set so shouldn't do some operations

        $this->irhpCandidatePermit->shouldReceive('setRandomizedScore')
            ->with(10.1 * 2)
            ->never();

        $this->irhpCandidatePermit->shouldReceive('setRandomFactor')
            ->with(10.1)
            ->never();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->never();

        $this->sut->handleCommand(
            CalculateRandomApplicationScoreCommand::create(['stockId' => $this->stockId])
        );
    }
}
