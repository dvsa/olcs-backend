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

        $candidatePermit1 = $this->createCandidatePermit(1, 77, 0.5, 0.5, 0.25);
        $candidatePermit2 = $this->createCandidatePermit(2, 70, 1.0, 0.8, 0.8);

        $candidatePermitsForScoring = [
            $candidatePermit1,
            $candidatePermit2
        ];

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getIrhpCandidatePermitsForScoring')
            ->with($stockId)
            ->andReturn($candidatePermitsForScoring);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getCountWithRandomisedScore')
            ->with($stockId)
            ->andReturn(0);

        $result = $this->sut->handleCommand(CalculateRandomApplicationScoreCommand::create(['stockId' => $stockId]));

        $expectedMessages = [
            'Updated the Randomised Score of Appropriate Candidate Permits.',
            '   - Number of Permits Updated: 2'
        ];

        $this->assertEquals(
            $expectedMessages,
            $result->getMessages()
        );
    }

    public function testHandleCommandScoresAlreadySet()
    {
        $stockId = 7;

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('getCountWithRandomisedScore')
            ->with($stockId)
            ->andReturn(5);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->never();

        $result = $this->sut->handleCommand(CalculateRandomApplicationScoreCommand::create(['stockId' => $stockId]));

        $expectedMessages = [
            'Stock has one or more randomised scores already assigned.',
            '    - No randomised scores will be set.'
        ];

        $this->assertEquals(
            $expectedMessages,
            $result->getMessages()
        );
    }

    private function createCandidatePermit(
        $applicationId,
        $applicationLicenceNo,
        $applicationScore,
        $randomFactor,
        $expectedRandomizedScore
    ) {
        $deviationData = [
            'licenceData' => [
                77 => [
                    1 => 1
                ],
                70 => [
                    2 => 1
                ]
            ],
            'meanDeviation' => 1
        ];

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')
            ->andReturn($applicationLicenceNo);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getLicence')
            ->andReturn($licence);
        $ecmtPermitApplication->shouldReceive('getPermitsRequired')
            ->andReturn(1);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($applicationId);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getEcmtPermitApplication')
            ->andReturn($ecmtPermitApplication);

        $irhpCandidatePermit = m::mock(IrhpCandidatePermit::class);
        $irhpCandidatePermit->shouldReceive('getIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);
        $irhpCandidatePermit->shouldReceive('getApplicationScore')
            ->andReturn($applicationScore);
        $irhpCandidatePermit->shouldReceive('setRandomizedScore')
            ->with($expectedRandomizedScore)
            ->once()
            ->ordered()
            ->globally();
        $irhpCandidatePermit->shouldReceive('setRandomFactor')
            ->with($randomFactor)
            ->once()
            ->ordered()
            ->globally();
        $irhpCandidatePermit->shouldReceive('calculateRandomFactor')
            ->with($deviationData)
            ->andReturn($randomFactor);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->with($irhpCandidatePermit)
            ->once()
            ->ordered()
            ->globally();

        return $irhpCandidatePermit;
    }
}
