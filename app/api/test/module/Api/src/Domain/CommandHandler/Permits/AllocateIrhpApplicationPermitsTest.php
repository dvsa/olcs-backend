<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\AllocateIrhpApplicationPermits;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpApplicationPermits as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpPermitApplicationPermit;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Mockery as m;

class AllocateIrhpApplicationPermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->sut = new AllocateIrhpApplicationPermits();
     
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpInterface::STATUS_VALID
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $irhpApplicationId = 49;

        $command = m::mock(Cmd::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $irhpPermitApplication1Id = 57;
        $irhpPermitApplication1PermitsRequired = 3;
        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('getId')
            ->andReturn($irhpPermitApplication1Id);
        $irhpPermitApplication1->shouldReceive('getPermitsRequired')
            ->andReturn($irhpPermitApplication1PermitsRequired);

        $irhpPermitApplication2Id = 64;
        $irhpPermitApplication2PermitsRequired = 5;
        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('getId')
            ->andReturn($irhpPermitApplication2Id);
        $irhpPermitApplication2->shouldReceive('getPermitsRequired')
            ->andReturn($irhpPermitApplication2PermitsRequired);

        $irhpPermitApplication3Id = 41;
        $irhpPermitApplication3PermitsRequired = 0;
        $irhpPermitApplication3 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication3->shouldReceive('getId')
            ->andReturn($irhpPermitApplication3Id);
        $irhpPermitApplication3->shouldReceive('getPermitsRequired')
            ->andReturn($irhpPermitApplication3PermitsRequired);

        $irhpPermitApplications = [$irhpPermitApplication1, $irhpPermitApplication2, $irhpPermitApplication3];

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitApplications')
            ->andReturn($irhpPermitApplications);
        $irhpApplication->shouldReceive('proceedToValid')
            ->with($this->refData[IrhpInterface::STATUS_VALID])
            ->once()
            ->ordered()
            ->globally();
        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->expectedSideEffect(
            AllocateIrhpPermitApplicationPermit::class,
            ['id' => $irhpPermitApplication1Id],
            new Result(),
            $irhpPermitApplication1PermitsRequired
        );

        $this->expectedSideEffect(
            AllocateIrhpPermitApplicationPermit::class,
            ['id' => $irhpPermitApplication2Id],
            new Result(),
            $irhpPermitApplication2PermitsRequired
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            $irhpApplicationId,
            $result->getId('irhpApplication')
        );
    }
}
