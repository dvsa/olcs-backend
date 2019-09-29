<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtIssued;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateCandidatePermits as AllocateCandidatePermitsCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\AllocatePermits;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class AllocatePermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->sut = new AllocatePermits();
     
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::STATUS_VALID,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $ecmtPermitApplicationId = 236;
        $irhpPermitApplicationId = 712;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getId')
            ->andReturn($irhpPermitApplicationId);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($ecmtPermitApplicationId)
            ->once()
            ->globally()
            ->ordered()
            ->andReturn($ecmtPermitApplication);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('refresh')
            ->with($ecmtPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $ecmtPermitApplication->shouldReceive('proceedToValid')
            ->with($this->refData[EcmtPermitApplication::STATUS_VALID])
            ->once()
            ->globally()
            ->ordered();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $emailResult = new Result();
        $emailResult->addMessage('Issuing email sent');

        $this->expectedSideEffect(
            AllocateCandidatePermitsCmd::class,
            ['id' => $irhpPermitApplicationId],
            (new Result())->addMessage('IRHP permit records created')
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtIssued::class,
            ['id' => $ecmtPermitApplicationId],
            $ecmtPermitApplicationId,
            $emailResult
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'IRHP permit records created',
                'Issuing email sent',
                'Permit allocation complete for ECMT application'
            ],
            $result->getMessages()
        );

        $this->assertEquals(
            $ecmtPermitApplicationId,
            $result->getId('ecmtPermitApplication')
        );
    }
}
