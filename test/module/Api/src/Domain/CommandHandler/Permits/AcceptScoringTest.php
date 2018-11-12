<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\Fee;
use Dvsa\Olcs\Api\Domain\Repository\FeeType;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\AcceptScoring;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful;
use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites;
use Dvsa\Olcs\Api\Domain\Query\Permits\GetScoredPermitList;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class AcceptScoringTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->mockRepo('Fee', Fee::class);
        $this->mockRepo('FeeType', FeeType::class);
        $this->mockRepo('IrhpPermitRange', IrhpPermitRange::class);
        $this->mockRepo('IrhpPermit', IrhpPermit::class);
        $this->mockRepo('IrhpPermitStock', IrhpPermitStock::class);

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

    public function testHandleCommand()
    {
        $stockId = 47;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($stockId);

        $feeTypeId = 4;
        $feeTypeDescription = 'Issue Fee';
        $feeTypeFixedValue = 4;

        $feeType = m::mock(FeeTypeEntity::class);
        $feeType->shouldReceive('getId')
            ->andReturn($feeTypeId);
        $feeType->shouldReceive('getDescription')
            ->andReturn($feeTypeDescription);
        $feeType->shouldReceive('getFixedValue')
            ->andReturn($feeTypeFixedValue);

        $this->repoMap['FeeType']->shouldReceive('getLatestForEcmtPermit')
            ->with('IRHP_GV_ECMT_100_PERMIT_FEE')
            ->andReturn($feeType);

        $this->repoMap['IrhpPermitRange']->shouldReceive('getCombinedRangeSize')
            ->with($stockId)
            ->andReturn(54);

        $this->repoMap['IrhpPermit']->shouldReceive('getPermitCount')
            ->with($stockId)
            ->andReturn(33);

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

        $successfulApplicationId = 2;
        $successfulApplicationLicenceId = 30;
        $successfulApplicationPermitsAwarded = 10;
        $successfulApplication = $this->getEcmtPermitApplicationMock(
            $successfulApplicationId,
            $successfulApplicationLicenceId,
            10,
            $successfulApplicationPermitsAwarded
        );

        $this->sut->shouldReceive('handleQuery')->once()
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertInstanceOf(GetScoredPermitList::class, $query);
                $this->assertEquals($stockId, $query->getStockId());

                return [
                    'result' => [],
                ];
            });

        $this->sut->shouldReceive('handleQuery')->once()
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertInstanceOf(CheckAcceptScoringPrerequisites::class, $query);
                $this->assertEquals($stockId, $query->getId());

                return [
                    'result' => true,
                    'message' => 'Accept scoring permitted'
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
            8,
            $partSuccessfulApplicationPermitsAwarded
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
        $unsuccessfulApplication = $this->getEcmtPermitApplicationMock($unsuccessfulApplicationId, 46, 7, 0);
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

        $this->expectedEmailQueueSideEffect(
            SendEcmtUnsuccessful::class,
            ['id' => $unsuccessfulApplicationId],
            $unsuccessfulApplicationId,
            $taskResult
        );

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUnderConsiderationApplicationIds')
            ->with($stockId)
            ->andReturn([$successfulApplicationId, $partSuccessfulApplicationId, $unsuccessfulApplicationId]);

        $this->sut->handleCommand($command);
    }

    public function testIncorrectStockStatus()
    {
        $stockId = 35;

        $this->sut->shouldReceive('handleQuery')->once()
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertInstanceOf(GetScoredPermitList::class, $query);
                $this->assertEquals($stockId, $query->getStockId());

                return [
                    'result' => [],
                ];
            });

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

        $this->sut->shouldReceive('handleQuery')->once()
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertInstanceOf(GetScoredPermitList::class, $query);
                $this->assertEquals($stockId, $query->getStockId());

                return [
                    'result' => [],
                ];
            });

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
            ->andReturnUsing(function ($query) use ($stockId) {
                $this->assertInstanceOf(CheckAcceptScoringPrerequisites::class, $query);
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

    private function getEcmtPermitApplicationMock($id, $licenceId, $permitsRequired, $permitsAwarded)
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplicationEntity::class);
        $irhpPermitApplication->shouldReceive('countPermitsAwarded')
            ->andReturn($permitsAwarded);

        $licence = m::mock(LicenceEntity::class);
        $licence->shouldReceive('getId')
            ->andReturn($licenceId);

        $ecmtPermitApplication = m::mock(EcmtPermitApplicationEntity::class);
        $ecmtPermitApplication->shouldReceive('getPermitsRequired')
            ->andReturn($permitsRequired);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($id);
        $ecmtPermitApplication->shouldReceive('getIrhpPermitApplications')
            ->andReturn([$irhpPermitApplication]);
        $ecmtPermitApplication->shouldReceive('getLicence')
            ->andReturn($licence);

        return $ecmtPermitApplication;
    }
}