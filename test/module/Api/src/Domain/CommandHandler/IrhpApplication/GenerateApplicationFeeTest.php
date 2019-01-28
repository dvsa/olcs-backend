<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\GenerateApplicationFee;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class GenerateApplicationFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('FeeType', FeeTypeRepo::class);
        $this->sut = new GenerateApplicationFee();
     
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $irhpApplicationId = 56;
        $licenceId = 7;

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getId')
            ->andReturn($licenceId);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('getLicence')
            ->andReturn($licence);
        $irhpApplication->shouldReceive('canCreateApplicationFee')
            ->andReturn(true);
        $irhpApplication->shouldReceive('hasOutstandingApplicationFee')
            ->andReturn(false);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $feeTypeId = 56;
        $feeTypeDescription = 'fee type description';
        $feeTypeFixedValue = '8.00';

        $feeType = m::mock(FeeType::class);
        $feeType->shouldReceive('getId')
            ->andReturn($feeTypeId);
        $feeType->shouldReceive('getDescription')
            ->andReturn($feeTypeDescription);
        $feeType->shouldReceive('getFixedValue')
            ->andReturn($feeTypeFixedValue);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with(FeeType::FEE_TYPE_IRHP_APP_BILATERAL_PRODUCT_REF)
            ->andReturn($feeType);

        $feeData = [
            'licence' => $licenceId,
            'irhpApplication' => $irhpApplicationId,
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeTypeDescription,
            'feeType' => $feeTypeId,
            'feeStatus' => Fee::STATUS_OUTSTANDING,
            'amount' => $feeTypeFixedValue
        ];

        $result = new Result();
        $this->expectedSideEffect(CreateFee::class, $feeData, $result);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
        $this->assertEquals(
            ['Created application fee'],
            $result->getMessages()
        );
    }

    public function testHandleCommandAlreadyCreated()
    {
        $irhpApplicationId = 56;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('canCreateApplicationFee')
            ->andReturn(true);
        $irhpApplication->shouldReceive('hasOutstandingApplicationFee')
            ->andReturn(true);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
        $this->assertEquals(
            ['Application fee already exists'],
            $result->getMessages()
        );
    }

    public function testHandleCommandFailedPrerequisites()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            'IRHP application is not in the correct state to allow creation of application fee'
        );

        $irhpApplicationId = 15;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('canCreateApplicationFee')
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
