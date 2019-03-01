<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\FeeType;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\AcceptScoring;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\SystemParameter as SystemParameterEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class AcceptScoringTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->mockRepo('FeeType', FeeType::class);
        $this->mockRepo('IrhpPermitStock', IrhpPermitStock::class);
        $this->mockRepo('SystemParameter', SystemParameter::class);

        $this->sut = m::mock(AcceptScoring::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplicationEntity::STATUS_AWAITING_FEE,
            EcmtPermitApplicationEntity::STATUS_UNSUCCESSFUL,
            IrhpPermitStockEntity::STATUS_ACCEPT_PREREQUISITE_FAIL,
            IrhpPermitStockEntity::STATUS_ACCEPT_IN_PROGRESS,
            IrhpPermitStockEntity::STATUS_ACCEPT_SUCCESSFUL,
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider dpHandleCommand
     */
    public function testHandleCommand($disableEcmtAllocEmailNone, $expected)
    {
        $stockId = 47;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $feeTypeId = 4;
        $feeTypeDescription = 'Issue Fee';
        $feeTypeFixedValue = 4;

        $this->repoMap['SystemParameter']->shouldReceive('fetchValue')
            ->with(SystemParameterEntity::DISABLE_ECMT_ALLOC_EMAIL_NONE)
            ->andReturn($disableEcmtAllocEmailNone);

        $feeType = m::mock(FeeTypeEntity::class);
        $feeType->shouldReceive('getId')
            ->andReturn($feeTypeId);
        $feeType->shouldReceive('getDescription')
            ->andReturn($feeTypeDescription);
        $feeType->shouldReceive('getFixedValue')
            ->andReturn($feeTypeFixedValue);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with('IRHP_GV_ECMT_100_PERMIT_FEE')
            ->andReturn($feeType);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->once()
            ->ordered()
            ->globally()
            ->andReturn($irhpPermitStock);

        $this->repoMap['IrhpPermitStock']->shouldReceive('refresh')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitStock->shouldReceive('statusAllowsAcceptScoring')
            ->andReturn(true);

        $irhpPermitStock->shouldReceive('proceedToAcceptInProgress')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_ACCEPT_IN_PROGRESS])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $manualNotificationApplicationId = 1;
        $manualNotificationApplicationLicenceId = 2;
        $manualNotificationApplicationPermitsAwarded = 3;
        $manualNotificationApplication = $this->getEcmtPermitApplicationMock(
            $manualNotificationApplicationId,
            $manualNotificationApplicationLicenceId,
            $manualNotificationApplicationPermitsAwarded,
            EcmtPermitApplicationEntity::SUCCESS_LEVEL_PARTIAL,
            EcmtPermitApplicationEntity::NOTIFICATION_TYPE_MANUAL
        );

        $manualNotificationApplication->shouldReceive('proceedToAwaitingFee')
            ->with($this->refData[EcmtPermitApplicationEntity::STATUS_AWAITING_FEE])
            ->once()
            ->ordered()
            ->globally();
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($manualNotificationApplication)
            ->once()
            ->ordered()
            ->globally();

        $successfulApplicationId = 2;
        $successfulApplicationLicenceId = 30;
        $successfulApplicationPermitsAwarded = 10;
        $successfulApplication = $this->getEcmtPermitApplicationMock(
            $successfulApplicationId,
            $successfulApplicationLicenceId,
            $successfulApplicationPermitsAwarded,
            EcmtPermitApplicationEntity::SUCCESS_LEVEL_FULL,
            EcmtPermitApplicationEntity::NOTIFICATION_TYPE_EMAIL
        );

        $this->sut->shouldReceive('handleQuery')->once()
            ->with(m::type(CheckAcceptScoringPrerequisites::class))
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => true,
                    'message' => 'Accept scoring permitted'
                ];
            });

        $this->sut->shouldReceive('handleQuery')->once()
            ->with(m::type(GetScoredPermitList::class))
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertEquals($stockId, $query->getStockId());

                return [
                    'result' => [],
                ];
            });


        $successfulApplication->shouldReceive('proceedToAwaitingFee')
            ->with($this->refData[EcmtPermitApplicationEntity::STATUS_AWAITING_FEE])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($successfulApplication)
            ->once()
            ->ordered()
            ->globally();

        $partSuccessfulApplicationId = 3;
        $partSuccessfulApplicationLicenceId = 71;
        $partSuccessfulApplicationPermitsAwarded = 5;
        $partSuccessfulApplication = $this->getEcmtPermitApplicationMock(
            $partSuccessfulApplicationId,
            $partSuccessfulApplicationLicenceId,
            $partSuccessfulApplicationPermitsAwarded,
            EcmtPermitApplicationEntity::SUCCESS_LEVEL_PARTIAL,
            EcmtPermitApplicationEntity::NOTIFICATION_TYPE_EMAIL
        );

        $partSuccessfulApplication->shouldReceive('proceedToAwaitingFee')
            ->with($this->refData[EcmtPermitApplicationEntity::STATUS_AWAITING_FEE])
            ->once()
            ->ordered()
            ->globally();
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($partSuccessfulApplication)
            ->once()
            ->ordered()
            ->globally();

        $unsuccessfulApplicationId = 4;
        $unsuccessfulApplication = $this->getEcmtPermitApplicationMock(
            $unsuccessfulApplicationId,
            46,
            7,
            EcmtPermitApplicationEntity::SUCCESS_LEVEL_NONE,
            EcmtPermitApplicationEntity::NOTIFICATION_TYPE_EMAIL
        );

        $unsuccessfulApplication->shouldReceive('proceedToUnsuccessful')
            ->with($this->refData[EcmtPermitApplicationEntity::STATUS_UNSUCCESSFUL])
            ->once()
            ->ordered()
            ->globally();
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($unsuccessfulApplication)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitStock->shouldReceive('proceedToAcceptSuccessful')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_ACCEPT_SUCCESSFUL])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($manualNotificationApplicationId)
            ->andReturn($manualNotificationApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($successfulApplicationId)
            ->andReturn($successfulApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($partSuccessfulApplicationId)
            ->andReturn($partSuccessfulApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($unsuccessfulApplicationId)
            ->andReturn($unsuccessfulApplication);

        $taskResult = new Result();

        $this->expectedSideEffect(
            CreateFee::class,
            [
                'licence' => $manualNotificationApplicationLicenceId,
                'ecmtPermitApplication' => $manualNotificationApplicationId,
                'invoicedDate' => date('Y-m-d'),
                'description' => 'Issue Fee - 3 permits',
                'feeType' => $feeTypeId,
                'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
                'amount' => 12,
            ],
            $taskResult
        );

        $this->expectedSideEffect(
            CreateFee::class,
            [
                'licence' => $successfulApplicationLicenceId,
                'ecmtPermitApplication' => $successfulApplicationId,
                'invoicedDate' => date('Y-m-d'),
                'description' => 'Issue Fee - 10 permits',
                'feeType' => $feeTypeId,
                'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
                'amount' => 40,
            ],
            $taskResult
        );

        $this->expectedSideEffect(
            CreateFee::class,
            [
                'licence' => $partSuccessfulApplicationLicenceId,
                'ecmtPermitApplication' => $partSuccessfulApplicationId,
                'invoicedDate' => date('Y-m-d'),
                'description' => 'Issue Fee - 5 permits',
                'feeType' => $feeTypeId,
                'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
                'amount' => 20,
            ],
            $taskResult
        );

        $this->expectedSideEffect(
            UploadScoringResult::class,
            [
                'csvContent' => [],
                'fileDescription' => 'Accepted Scoring Results'
            ],
            $taskResult
        );

        $taskParams = [
            'category' => TaskEntity::CATEGORY_PERMITS,
            'subCategory' => TaskEntity::SUBCATEGORY_PERMITS_APPLICATION_OUTCOME,
            'description' => 'Send outcome letter',
            'ecmtPermitApplication' => $manualNotificationApplicationId,
            'licence' => $manualNotificationApplicationLicenceId
        ];
        $this->expectedSideEffect(CreateTask::class, $taskParams, $taskResult);

        $this->expectedEmailQueueSideEffect(
            SendEcmtSuccessful::class,
            ['id' => $successfulApplicationId],
            $successfulApplicationId,
            $taskResult
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtPartSuccessful::class,
            ['id' => $partSuccessfulApplicationId],
            $partSuccessfulApplicationId,
            $taskResult
        );

        if (!$disableEcmtAllocEmailNone) {
            $this->expectedEmailQueueSideEffect(
                SendEcmtUnsuccessful::class,
                ['id' => $unsuccessfulApplicationId],
                $unsuccessfulApplicationId,
                $taskResult
            );
        }

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchInScopeApplicationIds')
            ->with($stockId)
            ->andReturn(
                [
                    $manualNotificationApplicationId,
                    $successfulApplicationId,
                    $partSuccessfulApplicationId,
                    $unsuccessfulApplicationId
                ]
            );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expected, $result->toArray());
    }

    public function dpHandleCommand()
    {
        return [
            'unsuccessful email enabled' => [
                'disableEcmtAllocEmailNone' => 0,
                'expected' => [
                    'id' => [],
                    'messages' => [
                        '4 under consideration applications found',
                        'processing ecmt application with id 1:',
                        '- create fee and set application to awaiting fee',
                        'processing ecmt application with id 2:',
                        '- sending email using command '.SendEcmtSuccessful::class,
                        '- create fee and set application to awaiting fee',
                        'processing ecmt application with id 3:',
                        '- sending email using command '.SendEcmtPartSuccessful::class,
                        '- create fee and set application to awaiting fee',
                        'processing ecmt application with id 4:',
                        '- sending email using command '.SendEcmtUnsuccessful::class,
                        '- no fee applicable, set application to unsuccessful',
                        'Acceptance process completed successfully.',
                    ],
                ],
            ],
            'unsuccessful email disabled' => [
                'disableEcmtAllocEmailNone' => 1,
                'expected' => [
                    'id' => [],
                    'messages' => [
                        '4 under consideration applications found',
                        'processing ecmt application with id 1:',
                        '- create fee and set application to awaiting fee',
                        'processing ecmt application with id 2:',
                        '- sending email using command '.SendEcmtSuccessful::class,
                        '- create fee and set application to awaiting fee',
                        'processing ecmt application with id 3:',
                        '- sending email using command '.SendEcmtPartSuccessful::class,
                        '- create fee and set application to awaiting fee',
                        'processing ecmt application with id 4:',
                        '- email sending disabled for '.EcmtPermitApplicationEntity::SUCCESS_LEVEL_NONE,
                        '- no fee applicable, set application to unsuccessful',
                        'Acceptance process completed successfully.',
                    ],
                ],
            ],
        ];
    }

    public function testIncorrectStockStatus()
    {
        $stockId = 35;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitStock->shouldReceive('statusAllowsAcceptScoring')
            ->andReturn(false);
        $irhpPermitStock->shouldReceive('getStatusDescription')
            ->andReturn('Stock scoring never run');

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->once()
            ->ordered()
            ->globally()
            ->andReturn($irhpPermitStock);

        $this->repoMap['IrhpPermitStock']->shouldReceive('refresh')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitStock->shouldReceive('proceedToAcceptPrerequisiteFail')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_ACCEPT_PREREQUISITE_FAIL])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Prerequisite failed: Accept scoring is not permitted when stock status is \'Stock scoring never run\''],
            $result->getMessages()
        );
    }

    public function testPrerequisitesFail()
    {
        $stockId = 35;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitStock->shouldReceive('statusAllowsAcceptScoring')
            ->andReturn(true);

        $this->repoMap['IrhpPermitStock']->shouldReceive('fetchById')
            ->with($stockId)
            ->once()
            ->ordered()
            ->globally()
            ->andReturn($irhpPermitStock);

        $this->repoMap['IrhpPermitStock']->shouldReceive('refresh')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitStock->shouldReceive('proceedToAcceptPrerequisiteFail')
            ->with($this->refData[IrhpPermitStockEntity::STATUS_ACCEPT_PREREQUISITE_FAIL])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitStock']->shouldReceive('save')
            ->with($irhpPermitStock)
            ->once()
            ->ordered()
            ->globally();

        $this->sut->shouldReceive('handleQuery')
            ->with(m::type(CheckAcceptScoringPrerequisites::class))
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => false,
                    'message' => 'Accept scoring not permitted'
                ];
            });

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Prerequisite failed: Accept scoring not permitted'],
            $result->getMessages()
        );
    }

    private function getEcmtPermitApplicationMock(
        $id,
        $licenceId,
        $permitsAwarded,
        $successLevel,
        $outcomeNotificationType
    ) {
        $licence = m::mock(LicenceEntity::class);
        $licence->shouldReceive('getId')
            ->andReturn($licenceId);

        $ecmtPermitApplication = m::mock(EcmtPermitApplicationEntity::class);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($id);
        $ecmtPermitApplication->shouldReceive('getLicence')
            ->andReturn($licence);
        $ecmtPermitApplication->shouldReceive('getPermitsAwarded')
            ->andReturn($permitsAwarded);
        $ecmtPermitApplication->shouldReceive('getSuccessLevel')
            ->andReturn($successLevel);
        $ecmtPermitApplication->shouldReceive('getOutcomeNotificationType')
            ->andReturn($outcomeNotificationType);

        return $ecmtPermitApplication;
    }
}
