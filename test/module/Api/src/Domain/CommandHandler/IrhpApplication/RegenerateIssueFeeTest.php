<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\RegenerateIssueFee;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class RegenerateIssueFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('FeeType', FeeTypeRepo::class);
        $this->sut = new RegenerateIssueFee();
     
        parent::setUp();
    }

    public function testFeeAlreadyExists()
    {
        $irhpApplicationId = 47;
        $existingFeeId = 78;
        $licenceId = 32;
        $permitsRequired = 14;
        $feeDescription = 'Bilateral permits - 14 permits';
        $feeTypeId = 103;
        $feeTypeDescription = 'Bilateral permits';
        $feeTypeFixedValue = 150;
        $feeAmount = 2100;

        $existingFee = m::mock(Fee::class);
        $existingFee->shouldReceive('getId')
            ->andReturn($existingFeeId);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('canCreateOrReplaceIssueFee')
            ->andReturn(true);
        $irhpApplication->shouldReceive('getLatestOutstandingIssueFee')
            ->andReturn($existingFee);
        $irhpApplication->shouldReceive('getLicence->getId')
            ->andReturn($licenceId);
        $irhpApplication->shouldReceive('getPermitsRequired')
            ->andReturn($permitsRequired);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $feeType = m::mock(FeeType::class);
        $feeType->shouldReceive('getId')
            ->andReturn($feeTypeId);
        $feeType->shouldReceive('getDescription')
            ->andReturn($feeTypeDescription);
        $feeType->shouldReceive('getFixedValue')
            ->andReturn($feeTypeFixedValue);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with('IRHP_GV_PERMIT_BILATERAL_ANN')
            ->andReturn($feeType);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $cancelFeeData = [
            'id' => $existingFeeId
        ];
        $this->expectedSideEffect(CancelFee::class, $cancelFeeData, new Result());

        $createFeeData = [
            'licence' => $licenceId,
            'irhpApplication' => $irhpApplicationId,
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeDescription,
            'feeType' => $feeTypeId,
            'feeStatus' => Fee::STATUS_OUTSTANDING,
            'amount' => $feeAmount
        ];
        $this->expectedSideEffect(CreateFee::class, $createFeeData, new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
        $this->assertEquals(
            ['Cancelled existing issue fee', 'Created new issue fee'],
            $result->getMessages()
        );
    }

    public function testFeeDoesNotAlreadyExist()
    {
        $irhpApplicationId = 47;
        $licenceId = 32;
        $permitsRequired = 14;
        $feeDescription = 'Bilateral permits - 14 permits';
        $feeTypeId = 103;
        $feeTypeDescription = 'Bilateral permits';
        $feeTypeFixedValue = 150;
        $feeAmount = 2100;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('canCreateOrReplaceIssueFee')
            ->andReturn(true);
        $irhpApplication->shouldReceive('getLatestOutstandingIssueFee')
            ->andReturn(null);
        $irhpApplication->shouldReceive('getLicence->getId')
            ->andReturn($licenceId);
        $irhpApplication->shouldReceive('getPermitsRequired')
            ->andReturn($permitsRequired);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $feeType = m::mock(FeeType::class);
        $feeType->shouldReceive('getId')
            ->andReturn($feeTypeId);
        $feeType->shouldReceive('getDescription')
            ->andReturn($feeTypeDescription);
        $feeType->shouldReceive('getFixedValue')
            ->andReturn($feeTypeFixedValue);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with('IRHP_GV_PERMIT_BILATERAL_ANN')
            ->andReturn($feeType);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $createFeeData = [
            'licence' => $licenceId,
            'irhpApplication' => $irhpApplicationId,
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeDescription,
            'feeType' => $feeTypeId,
            'feeStatus' => Fee::STATUS_OUTSTANDING,
            'amount' => $feeAmount
        ];
        $this->expectedSideEffect(CreateFee::class, $createFeeData, new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
        $this->assertEquals(
            ['Created new issue fee'],
            $result->getMessages()
        );
    }

    public function testForbidden()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            'IRHP application is not in the correct state to allow create/replace of issue fee'
        );

        $irhpApplicationId = 47;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('canCreateOrReplaceIssueFee')
            ->andReturn(false);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $this->sut->handleCommand($command);
    }
}
