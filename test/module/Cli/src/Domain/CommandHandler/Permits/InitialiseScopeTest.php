<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Cli\Domain\Command\Permits\InitialiseScope as InitialiseScopeCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\InitialiseScope as InitialiseScopeHandler;
use Mockery as m;

/**
 * Initialise Scope test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class InitialiseScopeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new InitialiseScopeHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $stockId = 7;

        $candidatePermitId1 = 5;
        $candidatePermitId2 = 8;
        $candidatePermitId3 = 11;

        $candidatePermit1LicNo = 123456;
        $candidatePermit3LicNo = 123456;

        $deviationSourceValues = [
            [
                'candidatePermitId' => $candidatePermitId1,
                'applicationId' => 1,
                'licNo' => $candidatePermit1LicNo,
                'permitsRequired' => 12
            ],
            [
                'candidatePermitId' => $candidatePermitId2,
                'applicationId' => 2,
                'licNo' => 455123,
                'permitsRequired' => 6
            ],
            [
                'candidatePermitId' => $candidatePermitId3,
                'applicationId' => 1,
                'licNo' => $candidatePermit3LicNo,
                'permitsRequired' => 12
            ],
        ];

        $deviationData = [
            'licenceData' => [
                '123456' => [1 => '12'],
                '455123' => [2 => '6']
            ],
            'meanDeviation' => 1.5
        ];

        $this->repoMap['EcmtPermitApplication']->shouldReceive('clearScope')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('applyScope')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchDeviationSourceValues')
            ->with($stockId)
            ->andReturn($deviationSourceValues);

        $candidatePermit1 = m::mock(IrhpCandidatePermit::class);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchById')
            ->with($candidatePermitId1)
            ->andReturn($candidatePermit1);
        $candidatePermit1->shouldReceive('prepareForScoring')
            ->once();
        $candidatePermit1->shouldReceive('hasRandomizedScore')
            ->andReturn(false);
        $candidatePermit1->shouldReceive('applyRandomizedScore')
            ->with($deviationData, $candidatePermit1LicNo)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('saveOnFlush')
            ->with($candidatePermit1)
            ->once()
            ->ordered()
            ->globally();

        $candidatePermit2 = m::mock(IrhpCandidatePermit::class);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchById')
            ->with($candidatePermitId2)
            ->andReturn($candidatePermit2);
        $candidatePermit2->shouldReceive('prepareForScoring')
            ->once();
        $candidatePermit2->shouldReceive('hasRandomizedScore')
            ->andReturn(true);

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('saveOnFlush')
            ->with($candidatePermit2)
            ->once()
            ->ordered()
            ->globally();

        $candidatePermit3 = m::mock(IrhpCandidatePermit::class);
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('fetchById')
            ->with($candidatePermitId3)
            ->andReturn($candidatePermit3);
        $candidatePermit3->shouldReceive('prepareForScoring')
            ->once();
        $candidatePermit3->shouldReceive('hasRandomizedScore')
            ->andReturn(false);
        $candidatePermit3->shouldReceive('applyRandomizedScore')
            ->with($deviationData, $candidatePermit3LicNo)
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

        $result = $this->sut->handleCommand(
            InitialiseScopeCommand::create(['stockId' => $stockId])
        );

        $expectedMessages = [
            'Established scope of candidate permits',
            '    - Candidate permits in scope: 3',
            '    - Randomised scores set: 2'
        ];

        $this->assertEquals(
            $expectedMessages,
            $result->getMessages()
        );
    }
}
