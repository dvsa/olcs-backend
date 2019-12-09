<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtAppSubmitted as SendEcmtAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermAppSubmitted as SendEcmtShortTermAppSubmittedCmd;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\StoreSnapshot as IrhpApplicationSnapshotCmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\PostSubmitTasks as PostSubmitTasksCmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\StoreEcmtPermitApplicationSnapshot as SnapshotCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\PostSubmitTasks;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Dvsa\Olcs\Api\Service\Permits\Scoring\CandidatePermitsCreator as ScoringCandidatePermitsCreator;
use Mockery as m;

class PostSubmitTasksTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        $this->mockedSmServices = [
            'PermitsCandidatePermitsIrhpCandidatePermitsCreator' => m::mock(IrhpCandidatePermitsCreator::class),
            'PermitsScoringCandidatePermitsCreator' => m::mock(ScoringCandidatePermitsCreator::class),
        ];

        $this->sut = new PostSubmitTasks();

        parent::setUp();
    }

    /**
     * @dataProvider dpHandleCommandForIrhp
     */
    public function testHandleCommandForIrhpWithoutAppSubmittedEmail($irhpPermitTypeId)
    {
        $id = 100;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getAppSubmittedEmailCommand')
            ->andReturn(null);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->once()
            ->with($id)
            ->andReturn($irhpApplication);

        $this->mockedSmServices['PermitsCandidatePermitsIrhpCandidatePermitsCreator']->shouldReceive('createIfRequired')
            ->with($irhpApplication)
            ->once();

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

    /**
     * @dataProvider dpHandleCommandForIrhp
     */
    public function testHandleCommandForIrhpWithAppSubmittedEmail($irhpPermitTypeId)
    {
        $irhpApplicationId = 100;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getAppSubmittedEmailCommand')
            ->andReturn(SendEcmtShortTermAppSubmittedCmd::class);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->once()
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->mockedSmServices['PermitsCandidatePermitsIrhpCandidatePermitsCreator']->shouldReceive('createIfRequired')
            ->with($irhpApplication)
            ->once();

        $this->expectedSideEffect(
            IrhpApplicationSnapshotCmd::class,
            [
                'id' => $irhpApplicationId,
            ],
            (new Result())->addMessage('Snapshot created')
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtShortTermAppSubmittedCmd::class,
            ['id' => $irhpApplicationId],
            $irhpApplicationId,
            new Result()
        );

        $result = $this->sut->handleCommand(
            PostSubmitTasksCmd::create(
                [
                    'id' => $irhpApplicationId,
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
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_TRAILER],
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
        $ecmtPermitApplicationId = 129;
        $requiredEuro5 = 2;
        $requiredEuro6 = 3;

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getRequiredEuro5')
            ->withNoArgs()
            ->andReturn($requiredEuro5);
        $ecmtPermitApplication->shouldReceive('getRequiredEuro6')
            ->withNoArgs()
            ->andReturn($requiredEuro6);
        $ecmtPermitApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->withNoArgs()
            ->andReturn($irhpPermitApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->once()
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(
            SnapshotCmd::class,
            [
                'id' => $ecmtPermitApplicationId,
            ],
            $result1
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtAppSubmittedCmd::class,
            ['id' => $ecmtPermitApplicationId],
            $ecmtPermitApplicationId,
            new Result()
        );

        $this->mockedSmServices['PermitsScoringCandidatePermitsCreator']->shouldReceive('create')
            ->with($irhpPermitApplication, $requiredEuro5, $requiredEuro6)
            ->once();

        $result = $this->sut->handleCommand(
            PostSubmitTasksCmd::create(
                [
                    'id' => $ecmtPermitApplicationId,
                    'irhpPermitType' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
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
}
