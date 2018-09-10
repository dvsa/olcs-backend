<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\EcmtSubmitApplication;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class EcmtSubmitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplication::class);
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermit::class);

        $this->sut = new EcmtSubmitApplication();

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::STATUS_UNDER_CONSIDERATION
        ];

        $this->references = [
            IrhpPermitWindow::class => [
                1 => m::mock(IrhpPermitWindow::class),
            ],
            IrhpPermitRange::class => [
                2 => m::mock(IrhpPermitRange::class),
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $ecmtPermitApplicationId = 129;

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpCandidatePermit = m::mock(IrhpCandidatePermit ::class);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('submit')
            ->with($this->mapRefData(EcmtPermitApplication::STATUS_UNDER_CONSIDERATION))
            ->once()
            ->globally()
            ->ordered();

        $ecmtPermitApplication->shouldReceive('getPermitsRequired')
            ->andReturn(3);

        $ecmtPermitApplication->shouldReceive('getLicence')
            ->andReturn(m::mock(Licence::class));

        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);

        $ecmtPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->andReturn(3);

        $ecmtPermitApplication->shouldReceive('getPermitApplicationScore')
            ->andReturn(3);

        $irhpPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->andReturn(3);

        $irhpPermitApplication->shouldReceive('getPermitApplicationScore')
            ->andReturn(3);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('save');

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save');

        $this->repoMap['EcmtPermitApplication']->shouldReceive('getReference')
            ->with(IrhpPermitWindow::class, 1)
            ->andReturn(m::mock(IrhpPermitWindow::class));

        $this->expectedEmailQueueSideEffect(
            SendEcmtAppSubmitted::class,
            ['id' => $ecmtPermitApplicationId],
            $ecmtPermitApplicationId,
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            $ecmtPermitApplicationId,
            $result->getId('ecmtPermitApplication')
        );

        $this->assertEquals(
            [
                'Permit application updated'
            ],
            $result->getMessages()
        );
    }
}
