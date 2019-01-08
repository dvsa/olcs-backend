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
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermit::class);

        $this->sut = new EcmtSubmitApplication();

        $viewRendererService = m::mock(\Zend\View\Renderer\RendererInterface::class);

        $this->mockedSmServices = [
            'ViewRenderer' => $viewRendererService
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::STATUS_UNDER_CONSIDERATION,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $ecmtPermitApplicationId = 129;
        $permitsRequired = 2;
        $intensityOfUse = 3;
        $applicationScore = 4;
        $viewData = ['data'];
        $renderedHtml = '<html>HTML</html>';

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('submit')
            ->with($this->mapRefData(EcmtPermitApplication::STATUS_UNDER_CONSIDERATION))
            ->once()
            ->globally()
            ->ordered();

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $irhpPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->once()
            ->withNoArgs()
            ->andReturn($intensityOfUse);

        $irhpPermitApplication->shouldReceive('getPermitApplicationScore')
            ->once()
            ->withNoArgs()
            ->andReturn($applicationScore);

        $ecmtPermitApplication->shouldReceive('getPermitsRequired')
            ->once()
            ->withNoArgs()
            ->andReturn($permitsRequired);

        $ecmtPermitApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->once()
            ->withNoArgs()
            ->andReturn($irhpPermitApplication);

        $ecmtPermitApplication->shouldReceive('returnSnapshotData')
            ->once()
            ->withNoArgs()
            ->andReturn($viewData);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->once()
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->times($permitsRequired)
            ->with(IrhpCandidatePermit::class)
            ->andReturnUsing(
                function (IrhpCandidatePermit $irhpCandidatePermit) use ($irhpPermitApplication, $intensityOfUse, $applicationScore) {
                    $this->assertEquals($irhpPermitApplication, $irhpCandidatePermit->getIrhpPermitApplication());
                    $this->assertEquals(floatval($intensityOfUse), $irhpCandidatePermit->getIntensityOfUse());
                    $this->assertEquals(floatval($applicationScore), $irhpCandidatePermit->getApplicationScore());

                    return $irhpCandidatePermit;
                }
            );

        $this->mockedSmServices['ViewRenderer']->shouldReceive('render')
            ->once()
            ->with(m::type(ViewModel::class))
            ->andReturnUsing(
                function (ViewModel $viewModel) use ($viewData, $renderedHtml) {
                    $expectedViewVariables = ['data' => $viewData];

                    $this->assertEquals('sections/ecmt-permit-application-snapshot', $viewModel->getTemplate());
                    $this->assertEquals($expectedViewVariables, $viewModel->getVariables()->getArrayCopy());

                    return $renderedHtml;
                }
            );

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(
            SnapshotCmd::class,
            [
                'id' => $ecmtPermitApplicationId,
                'html' => $renderedHtml
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
            ->once()
            ->withNoArgs()
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
