<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\CreateDefaultIrhpPermitApplications;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class CreateDefaultIrhpPermitApplicationsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplicationRepo::class);
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);
        $this->sut = new CreateDefaultIrhpPermitApplications();
     
        parent::setUp();
    }

    public function testHandleCommandMultilateral()
    {
        $irhpApplicationId = 5;
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn($irhpPermitTypeId);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $openWindow1 = m::mock(IrhpPermitWindow::class);
        $openWindow2 = m::mock(IrhpPermitWindow::class);

        $openWindows = [
            $openWindow1,
            $openWindow2
        ];

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindowsByType')
            ->once()
            ->andReturnUsing(
                function ($irhpPermitTypeIdParam, $now) use ($irhpPermitTypeId, $openWindows) {
                    $this->assertEquals($irhpPermitTypeId, $irhpPermitTypeIdParam);
                    $this->assertEquals(
                        date('Y-m-d'),
                        $now->format('Y-m-d')
                    );

                    return $openWindows;
                }
            );

        $windowsWithCreatedIrhpPermitApplications = [];

        $this->repoMap['IrhpPermitApplication']->shouldReceive('save')
            ->twice()
            ->andReturnUsing(
                function (
                    $irhpPermitApplication
                ) use (
                    $irhpApplication,
                    &$windowsWithCreatedIrhpPermitApplications
                ) {
                    $this->assertSame($irhpApplication, $irhpPermitApplication->getIrhpApplication());
                    $windowsWithCreatedIrhpPermitApplications[] = $irhpPermitApplication->getIrhpPermitWindow();
                }
            );

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertSame($openWindow1, $windowsWithCreatedIrhpPermitApplications[0]);
        $this->assertSame($openWindow2, $windowsWithCreatedIrhpPermitApplications[1]);

        $this->assertEquals(
            ['Created 2 irhp permit applications'],
            $result->getMessages()
        );
    }

    public function testHandleCommandNotMultilateral()
    {
        $irhpApplicationId = 5;
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn($irhpPermitTypeId);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['No default irhp permit applications need to be created'],
            $result->getMessages()
        );
    }
}
