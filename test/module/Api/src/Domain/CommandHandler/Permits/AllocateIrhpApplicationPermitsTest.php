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
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Mockery as m;

class AllocateIrhpApplicationPermitsTest extends CommandHandlerTestCase
{
    private $irhpApplicationId;

    private $command;

    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->sut = new AllocateIrhpApplicationPermits();

        $this->irhpApplicationId = 110;

        $this->command = m::mock(Cmd::class);
        $this->command->shouldReceive('getId')
            ->andReturn($this->irhpApplicationId);
     
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpInterface::STATUS_VALID
        ];

        parent::initReferences();
    }

    public function testHandleCommandStandard()
    {
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
        $irhpApplication->shouldReceive('getIrhpPermitType->getAllocationMode')
            ->andReturn(IrhpPermitType::ALLOCATION_MODE_STANDARD);
        $this->repoMap['IrhpApplication']->shouldReceive('refresh')
            ->with($irhpApplication)
            ->once()
            ->globally()
            ->ordered();
        $irhpApplication->shouldReceive('proceedToValid')
            ->with($this->refData[IrhpInterface::STATUS_VALID])
            ->once()
            ->globally()
            ->ordered();
        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($this->irhpApplicationId)
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

        $result = $this->sut->handleCommand($this->command);

        $this->assertEquals(
            $this->irhpApplicationId,
            $result->getId('irhpApplication')
        );
    }

    public function testHandleCommandEmissionsCategories()
    {
        $irhpPermitApplication1Id = 57;
        $irhpPermitApplication1RequiredEuro5 = 0;
        $irhpPermitApplication1RequiredEuro6 = 3;
        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('getId')
            ->andReturn($irhpPermitApplication1Id);
        $irhpPermitApplication1->shouldReceive('getRequiredEuro5')
            ->andReturn($irhpPermitApplication1RequiredEuro5);
        $irhpPermitApplication1->shouldReceive('getRequiredEuro6')
            ->andReturn($irhpPermitApplication1RequiredEuro6);

        $irhpPermitApplication2Id = 22;
        $irhpPermitApplication2RequiredEuro5 = 7;
        $irhpPermitApplication2RequiredEuro6 = 8;
        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('getId')
            ->andReturn($irhpPermitApplication2Id);
        $irhpPermitApplication2->shouldReceive('getRequiredEuro5')
            ->andReturn($irhpPermitApplication2RequiredEuro5);
        $irhpPermitApplication2->shouldReceive('getRequiredEuro6')
            ->andReturn($irhpPermitApplication2RequiredEuro6);

        $irhpPermitApplication3Id = 81;
        $irhpPermitApplication3RequiredEuro5 = 5;
        $irhpPermitApplication3RequiredEuro6 = 0;
        $irhpPermitApplication3 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication3->shouldReceive('getId')
            ->andReturn($irhpPermitApplication3Id);
        $irhpPermitApplication3->shouldReceive('getRequiredEuro5')
            ->andReturn($irhpPermitApplication3RequiredEuro5);
        $irhpPermitApplication3->shouldReceive('getRequiredEuro6')
            ->andReturn($irhpPermitApplication3RequiredEuro6);

        $irhpPermitApplications = [$irhpPermitApplication1, $irhpPermitApplication2, $irhpPermitApplication3];

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitApplications')
            ->andReturn($irhpPermitApplications);
        $irhpApplication->shouldReceive('getIrhpPermitType->getAllocationMode')
            ->andReturn(IrhpPermitType::ALLOCATION_MODE_EMISSIONS_CATEGORIES);
        $this->repoMap['IrhpApplication']->shouldReceive('refresh')
            ->with($irhpApplication)
            ->once()
            ->globally()
            ->ordered();
        $irhpApplication->shouldReceive('proceedToValid')
            ->with($this->refData[IrhpInterface::STATUS_VALID])
            ->once()
            ->globally()
            ->ordered();
        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($this->irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->expectedSideEffect(
            AllocateIrhpPermitApplicationPermit::class,
            [
                'id' => $irhpPermitApplication1Id,
                'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO6_REF
            ],
            new Result(),
            $irhpPermitApplication1RequiredEuro6
        );

        $this->expectedSideEffect(
            AllocateIrhpPermitApplicationPermit::class,
            [
                'id' => $irhpPermitApplication2Id,
                'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO5_REF
            ],
            new Result(),
            $irhpPermitApplication2RequiredEuro5
        );

        $this->expectedSideEffect(
            AllocateIrhpPermitApplicationPermit::class,
            [
                'id' => $irhpPermitApplication2Id,
                'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO6_REF
            ],
            new Result(),
            $irhpPermitApplication2RequiredEuro6
        );

        $this->expectedSideEffect(
            AllocateIrhpPermitApplicationPermit::class,
            [
                'id' => $irhpPermitApplication3Id,
                'emissionsCategory' => RefData::EMISSIONS_CATEGORY_EURO5_REF
            ],
            new Result(),
            $irhpPermitApplication3RequiredEuro5
        );

        $result = $this->sut->handleCommand($this->command);

        $this->assertEquals(
            $this->irhpApplicationId,
            $result->getId('irhpApplication')
        );
    }
}
