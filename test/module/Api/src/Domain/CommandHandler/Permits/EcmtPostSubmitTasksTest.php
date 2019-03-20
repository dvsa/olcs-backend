<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\EcmtPostSubmitTasks;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Api\Domain\Command\Permits\StoreEcmtPermitApplicationSnapshot as SnapshotCmd;
use Mockery as m;

class EcmtPostSubmitTasksTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        $this->sut = new EcmtPostSubmitTasks();

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

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $irhpPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->once()
            ->withNoArgs()
            ->andReturn($intensityOfUse);

        $irhpPermitApplication->shouldReceive('getPermitApplicationScore')
            ->once()
            ->withNoArgs()
            ->andReturn($applicationScore);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->once()
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);

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

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->times($permitsRequired)
            ->with(m::type(IrhpCandidatePermit::class))
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

                    $this->assertEquals('ecmt-permit-application-snapshot', $viewModel->getTemplate());
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
            [
                'Snapshot created'
            ],
            $result->getMessages()
        );
    }
}
