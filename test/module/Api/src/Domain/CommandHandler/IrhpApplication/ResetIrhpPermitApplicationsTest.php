<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermitApplication\Delete as DeleteIrhpPermitApplicationCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\ResetIrhpPermitApplications;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use RuntimeException;

class ResetIrhpPermitApplicationsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplicationRepo::class);
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->sut = new ResetIrhpPermitApplications();
     
        parent::setUp();
    }

    public function testHandleCommandMultilateral()
    {
        $irhpApplicationId = 5;

        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('clearPermitsRequired')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('save')
            ->with($irhpPermitApplication1)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('clearPermitsRequired')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('save')
            ->with($irhpPermitApplication2)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplications = [
            $irhpPermitApplication1,
            $irhpPermitApplication2
        ];

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitApplications')
            ->andReturn($irhpPermitApplications);
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Reset 2 irhp permit applications to initial state'],
            $result->getMessages()
        );
    }

    public function testHandleCommandBilateral()
    {
        $irhpApplicationId = 7;

        $irhpPermitApplication1Id = 45;
        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('getId')
            ->andReturn($irhpPermitApplication1Id);

        $irhpPermitApplication2Id = 47;
        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('getId')
            ->andReturn($irhpPermitApplication2Id);
 
        $irhpPermitApplications = [
            $irhpPermitApplication1,
            $irhpPermitApplication2
        ];

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitApplications')
            ->andReturn($irhpPermitApplications);
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $this->expectedSideEffect(
            DeleteIrhpPermitApplicationCmd::class,
            ['id' => $irhpPermitApplication1Id],
            (new Result())->addMessage('Deleted irhp permit application 45')
        );

        $this->expectedSideEffect(
            DeleteIrhpPermitApplicationCmd::class,
            ['id' => $irhpPermitApplication2Id],
            (new Result())->addMessage('Deleted irhp permit application 47')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'Deleted irhp permit application 45',
                'Deleted irhp permit application 47',
                'Deleted 2 irhp permit applications'
            ],
            $result->getMessages()
        );
    }

    public function testHandleCommandPermitTypeUnsupported()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'ResetIrhpPermitApplications command does not support permit type 14'
        );

        $irhpApplicationId = 7;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(14);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $this->sut->handleCommand($command);
    }
}
