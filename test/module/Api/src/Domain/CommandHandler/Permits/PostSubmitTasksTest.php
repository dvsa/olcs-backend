<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\StoreSnapshot as IrhpApplicationSnapshotCmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\PostSubmitTasks as PostSubmitTasksCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\PostSubmitTasks;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepo;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Command\Permits\StoreEcmtPermitApplicationSnapshot as SnapshotCmd;
use Mockery as m;
use RuntimeException;

class PostSubmitTasksTest extends CommandHandlerTestCase
{
    private $requiredEuro5;

    private $requiredEuro6;

    private $irhpPermitApplication;

    private $ecmtPermitApplicationId;

    private $ecmtPermitApplication;

    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);
        $this->mockRepo('SystemParameter', SystemParameterRepo::class);

        $this->sut = new PostSubmitTasks();

        $this->requiredEuro5 = 1;
        $this->requiredEuro6 = 2;

        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $this->ecmtPermitApplicationId = 129;

        $this->ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $this->ecmtPermitApplication->shouldReceive('getRequiredEuro5')
            ->withNoArgs()
            ->andReturn($this->requiredEuro5);
        $this->ecmtPermitApplication->shouldReceive('getRequiredEuro6')
            ->withNoArgs()
            ->andReturn($this->requiredEuro6);
        $this->ecmtPermitApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->withNoArgs()
            ->andReturn($this->irhpPermitApplication);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            RefData::EMISSIONS_CATEGORY_EURO5_REF,
            RefData::EMISSIONS_CATEGORY_EURO6_REF,
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider dpHandleCommandForIrhp
     */
    public function testHandleCommandForIrhp($irhpPermitTypeId)
    {
        $id = 100;

        $this->expectedSideEffect(
            IrhpApplicationSnapshotCmd::class,
            [
                'id' => $id,
            ],
            (new Result())->addMessage('Snapshot created')
        );

        $result = $this->sut->handleCommand(
            PostSubmitTasksCmd::create(
                [
                    'id' => $id,
                    'irhpPermitType' => $irhpPermitTypeId,
                ]
            )
        );

        $this->assertEquals(
            [
                'Snapshot created'
            ],
            $result->getMessages()
        );
    }

    public function dpHandleCommandForIrhp()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
        ];
    }

    public function testHandleCommandForUnsupported()
    {
        $id = 100;
        $irhpPermitTypeId = 1000;

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand(
            PostSubmitTasksCmd::create(
                [
                    'id' => $id,
                    'irhpPermitType' => $irhpPermitTypeId,
                ]
            )
        );
    }

    public function testHandleCommandForEcmtAnnual()
    {
        $intensityOfUse = 3;
        $applicationScore = 4;

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->once()
            ->with($this->ecmtPermitApplicationId)
            ->andReturn($this->ecmtPermitApplication);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(SystemParameter::USE_ALT_ECMT_IOU_ALGORITHM)
            ->andReturn(0);

        $this->irhpPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->with(null)
            ->andReturn($intensityOfUse);
        $this->irhpPermitApplication->shouldReceive('getPermitApplicationScore')
            ->with(null)
            ->andReturn($applicationScore);

        $refData = $this->refData;
        $savedEuro5 = 0;
        $savedEuro6 = 0;

        $irhpPermitApplication = $this->irhpPermitApplication;
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->with(m::type(IrhpCandidatePermit::class))
            ->andReturnUsing(
                function (
                    $irhpCandidatePermit
                ) use (
                    $irhpPermitApplication,
                    $intensityOfUse,
                    $applicationScore,
                    $refData,
                    &$savedEuro5,
                    &$savedEuro6
                ) {
                    $this->assertEquals($irhpPermitApplication, $irhpCandidatePermit->getIrhpPermitApplication());
                    $this->assertEquals($intensityOfUse, $irhpCandidatePermit->getIntensityOfUse());
                    $this->assertEquals($applicationScore, $irhpCandidatePermit->getApplicationScore());

                    $requestedEmissionsCategory = $irhpCandidatePermit->getRequestedEmissionsCategory();
                    if ($requestedEmissionsCategory === $refData[RefData::EMISSIONS_CATEGORY_EURO5_REF]) {
                        $savedEuro5++;
                    } elseif ($requestedEmissionsCategory === $refData[RefData::EMISSIONS_CATEGORY_EURO6_REF]) {
                        $savedEuro6++;
                    } else {
                        throw new RuntimeException('Unexpected emissions category parameter');
                    }
                }
            );

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(
            SnapshotCmd::class,
            [
                'id' => $this->ecmtPermitApplicationId,
            ],
            $result1
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtAppSubmitted::class,
            ['id' => $this->ecmtPermitApplicationId],
            $this->ecmtPermitApplicationId,
            new Result()
        );

        $result = $this->sut->handleCommand(
            PostSubmitTasksCmd::create(
                [
                    'id' => $this->ecmtPermitApplicationId,
                    'irhpPermitType' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                ]
            )
        );

        $this->assertEquals($this->requiredEuro5, $savedEuro5);
        $this->assertEquals($this->requiredEuro6, $savedEuro6);

        $this->assertEquals(
            [
                'Snapshot created'
            ],
            $result->getMessages()
        );
    }

    public function testHandleCommandForEcmtAnnualWithAltEcmtIouAlgorithm()
    {
        $euro5IntensityOfUse = 3;
        $euro5ApplicationScore = 4;
        $euro6IntensityOfUse = 5;
        $euro6ApplicationScore = 6;

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->once()
            ->with($this->ecmtPermitApplicationId)
            ->andReturn($this->ecmtPermitApplication);

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(SystemParameter::USE_ALT_ECMT_IOU_ALGORITHM)
            ->andReturn(1);

        $this->irhpPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->with(RefData::EMISSIONS_CATEGORY_EURO5_REF)
            ->andReturn($euro5IntensityOfUse);
        $this->irhpPermitApplication->shouldReceive('getPermitIntensityOfUse')
            ->with(RefData::EMISSIONS_CATEGORY_EURO6_REF)
            ->andReturn($euro6IntensityOfUse);
        $this->irhpPermitApplication->shouldReceive('getPermitApplicationScore')
            ->with(RefData::EMISSIONS_CATEGORY_EURO5_REF)
            ->andReturn($euro5ApplicationScore);
        $this->irhpPermitApplication->shouldReceive('getPermitApplicationScore')
            ->with(RefData::EMISSIONS_CATEGORY_EURO6_REF)
            ->andReturn($euro6ApplicationScore);

        $refData = $this->refData;
        $savedEuro5 = 0;
        $savedEuro6 = 0;

        $irhpPermitApplication = $this->irhpPermitApplication;
        $this->repoMap['IrhpCandidatePermit']->shouldReceive('save')
            ->with(m::type(IrhpCandidatePermit::class))
            ->andReturnUsing(
                function (
                    $irhpCandidatePermit
                ) use (
                    $irhpPermitApplication,
                    $euro5IntensityOfUse,
                    $euro6IntensityOfUse,
                    $euro5ApplicationScore,
                    $euro6ApplicationScore,
                    $refData,
                    &$savedEuro5,
                    &$savedEuro6
                ) {
                    $this->assertEquals($irhpPermitApplication, $irhpCandidatePermit->getIrhpPermitApplication());

                    $savedIntensityOfUse = $irhpCandidatePermit->getIntensityOfUse();
                    $savedApplicationScore = $irhpCandidatePermit->getApplicationScore();

                    $requestedEmissionsCategory = $irhpCandidatePermit->getRequestedEmissionsCategory();
                    if ($requestedEmissionsCategory === $refData[RefData::EMISSIONS_CATEGORY_EURO5_REF]) {
                        $this->assertEquals($euro5IntensityOfUse, $savedIntensityOfUse);
                        $this->assertEquals($euro5ApplicationScore, $savedApplicationScore);
                        $savedEuro5++;
                    } elseif ($requestedEmissionsCategory === $refData[RefData::EMISSIONS_CATEGORY_EURO6_REF]) {
                        $this->assertEquals($euro6IntensityOfUse, $savedIntensityOfUse);
                        $this->assertEquals($euro6ApplicationScore, $savedApplicationScore);
                        $savedEuro6++;
                    } else {
                        throw new RuntimeException('Unexpected emissions category parameter');
                    }
                }
            );

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(
            SnapshotCmd::class,
            [
                'id' => $this->ecmtPermitApplicationId,
            ],
            $result1
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtAppSubmitted::class,
            ['id' => $this->ecmtPermitApplicationId],
            $this->ecmtPermitApplicationId,
            new Result()
        );

        $result = $this->sut->handleCommand(
            PostSubmitTasksCmd::create(
                [
                    'id' => $this->ecmtPermitApplicationId,
                    'irhpPermitType' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                ]
            )
        );

        $this->assertEquals($this->requiredEuro5, $savedEuro5);
        $this->assertEquals($this->requiredEuro6, $savedEuro6);

        $this->assertEquals(
            [
                'Snapshot created'
            ],
            $result->getMessages()
        );
    }
}
