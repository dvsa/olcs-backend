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
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Api\Domain\Command\Permits\StoreEcmtPermitApplicationSnapshot as SnapshotCmd;

use Mockery as m;

class EcmtSubmitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindow::class);
        $this->mockRepo('IrhpPermitStock', IrhpPermitStock::class);
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplication::class);
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermit::class);

        $this->sut = new EcmtSubmitApplication();

        $viewRendererService = m::mock(\Zend\View\Renderer\RendererInterface::class);
        $viewRendererService->shouldReceive('render')->with(m::type(ViewModel::class))->andReturn('HTML');

        $this->mockedSmServices = [
            'ViewRenderer' => $viewRendererService
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::STATUS_UNDER_CONSIDERATION,
            EcmtPermitApplication::PERMIT_TYPE,
            EcmtPermitApplication::INTER_JOURNEY_MORE_90
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

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('submit')
            ->with($this->mapRefData(EcmtPermitApplication::STATUS_UNDER_CONSIDERATION))
            ->once()
            ->globally()
            ->ordered();

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $irhpPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->andReturn(3);

        $irhpPermitApplication->shouldReceive('getPermitApplicationScore')
            ->andReturn(3);

        $ecmtPermitApplication->shouldReceive('getPermitsRequired')
            ->andReturn(3);

        $permitType = $this->refData[EcmtPermitApplication::PERMIT_TYPE];
        $ecmtPermitApplication->shouldReceive('getPermitType')
            ->andReturn($permitType);

        $ecmtPermitApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);

        $licence = m::mock(Licence::class);
        $organisation = m::mock(Organisation::class);

        $licence->shouldReceive('getOrganisation')
            ->andReturn($organisation);

        $licence->shouldReceive('getLicNo')
            ->andReturn('OB666666');


        $organisation->shouldReceive('getName')
            ->andReturn('Organisation Name');

        $ecmtPermitApplication->shouldReceive('getLicence')
            ->andReturn($licence);

        $ecmtPermitApplication->shouldReceive('getEmissions')
            ->andReturn(1);

        $ecmtPermitApplication->shouldReceive('getCabotage')
            ->andReturn(0);

        $ecmtPermitApplication->shouldReceive('getHasRestrictedCountries')
            ->andReturn(0);

        $ecmtPermitApplication->shouldReceive('getTrips')
            ->andReturn(12);

        $ecmtPermitApplication->shouldReceive('getInternationalJourneys')
            ->andReturn($this->refData[EcmtPermitApplication::INTER_JOURNEY_MORE_90]);

        $ecmtPermitApplication->shouldReceive('getApplicationRef')
            ->andReturn('OB666666 / 3');

        $sector = m::mock(Sectors::class);
        $sector->shouldReceive('getName')->andReturn('Metal');

        $ecmtPermitApplication->shouldReceive('getSectors')
            ->andReturn($sector);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')->times(3);

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(
            SnapshotCmd::class,
            [
                'id' => 129,
                'html' => 'HTML'
            ],
            $result1
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtAppSubmitted::class,
            ['id' => $ecmtPermitApplicationId],
            $ecmtPermitApplicationId,
            new Result()
        );

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            $ecmtPermitApplicationId,
            $result->getId('ecmtPermitApplication')
        );

        $this->assertEquals(
            [
                'Permit application updated',
                'Snapshot created'
            ],
            $result->getMessages()
        );
    }
}
