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
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Service\Permits\Scoring\CandidatePermitsCreator;
use Mockery as m;

class PostSubmitTasksTest extends CommandHandlerTestCase
{
    private $requiredEuro5;

    private $requiredEuro6;

    private $irhpPermitApplication;

    public function setUp()
    {
        $this->requiredEuro5 = 2;
        $this->requiredEuro6 = 3;
        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        $this->mockedSmServices = [
            'PermitsScoringCandidatePermitsCreator' => m::mock(CandidatePermitsCreator::class),
        ];

        $this->sut = new PostSubmitTasks();

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
    public function testHandleCommandForIrhp($irhpPermitTypeId, $businessProcessId)
    {
        $id = 100;

        $this->mockedSmServices['PermitsScoringCandidatePermitsCreator']->shouldReceive('create')
            ->never();

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getBusinessProcess->getId')
            ->withNoArgs()
            ->andReturn($businessProcessId);
        $irhpApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->withNoArgs()
            ->andReturn($this->irhpPermitApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->once()
            ->with($id)
            ->andReturn($irhpApplication);

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
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, RefData::BUSINESS_PROCESS_APG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, RefData::BUSINESS_PROCESS_APG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, RefData::BUSINESS_PROCESS_APG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, RefData::BUSINESS_PROCESS_APG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, RefData::BUSINESS_PROCESS_APGG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, RefData::BUSINESS_PROCESS_APGG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, RefData::BUSINESS_PROCESS_APGG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, RefData::BUSINESS_PROCESS_APGG],
        ];
    }

    /**
     * @dataProvider dpHandleCommandForIrhpWithCreateCandidatePermits
     */
    public function testHandleCommandForIrhpWithCreateCandidatePermits($irhpPermitTypeId, $businessProcessId)
    {
        $id = 100;

        $this->mockedSmServices['PermitsScoringCandidatePermitsCreator']->shouldReceive('create')
            ->with($this->irhpPermitApplication, $this->requiredEuro5, $this->requiredEuro6)
            ->once();

        $this->irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->withNoArgs()
            ->andReturn($this->requiredEuro5);
        $this->irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->withNoArgs()
            ->andReturn($this->requiredEuro6);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getBusinessProcess->getId')
            ->withNoArgs()
            ->andReturn($businessProcessId);
        $irhpApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->withNoArgs()
            ->andReturn($this->irhpPermitApplication);
        $irhpApplication->shouldReceive('getIrhpPermitType->isEcmtShortTerm')
            ->withNoArgs()
            ->andReturn(false);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->once()
            ->with($id)
            ->andReturn($irhpApplication);

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

    public function dpHandleCommandForIrhpWithCreateCandidatePermits()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, RefData::BUSINESS_PROCESS_APSG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, RefData::BUSINESS_PROCESS_APSG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, RefData::BUSINESS_PROCESS_APSG],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, RefData::BUSINESS_PROCESS_APSG],
        ];
    }

    public function testHandleCommandForIrhpWithCreateCandidatePermitsAndAppSubmittedEmail()
    {
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM;
        $businessProcessId = RefData::BUSINESS_PROCESS_APSG;

        $id = 100;

        $this->mockedSmServices['PermitsScoringCandidatePermitsCreator']->shouldReceive('create')
            ->with($this->irhpPermitApplication, $this->requiredEuro5, $this->requiredEuro6)
            ->once();

        $this->irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->withNoArgs()
            ->andReturn($this->requiredEuro5);
        $this->irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->withNoArgs()
            ->andReturn($this->requiredEuro6);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getBusinessProcess->getId')
            ->withNoArgs()
            ->andReturn($businessProcessId);
        $irhpApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->withNoArgs()
            ->andReturn($this->irhpPermitApplication);
        $irhpApplication->shouldReceive('getIrhpPermitType->isEcmtShortTerm')
            ->withNoArgs()
            ->andReturn(true);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->once()
            ->with($id)
            ->andReturn($irhpApplication);

        $this->expectedSideEffect(
            IrhpApplicationSnapshotCmd::class,
            [
                'id' => $id,
            ],
            (new Result())->addMessage('Snapshot created')
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtShortTermAppSubmittedCmd::class,
            ['id' => $id],
            $id,
            new Result()
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

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getRequiredEuro5')
            ->withNoArgs()
            ->andReturn($this->requiredEuro5);
        $ecmtPermitApplication->shouldReceive('getRequiredEuro6')
            ->withNoArgs()
            ->andReturn($this->requiredEuro6);
        $ecmtPermitApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->withNoArgs()
            ->andReturn($this->irhpPermitApplication);
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
            ->with($this->irhpPermitApplication, $this->requiredEuro5, $this->requiredEuro6)
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
