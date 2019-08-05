<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\Grant;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Service\Permits\GrantabilityChecker;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class GrantTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('FeeType', FeeTypeRepo::class);
        $this->sut = new Grant();

        $this->mockedSmServices = [
            'PermitsGrantabilityChecker' => m::mock(GrantabilityChecker::class),
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpInterface::STATUS_AWAITING_FEE
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $irhpApplicationId = 55;

        $irhpApplication = m::mock(IrhpApplication::class);
        $feeType = m::mock(FeeType::class);

        $this->mockedSmServices['PermitsGrantabilityChecker']->shouldReceive('isGrantable')
            ->with($irhpApplication)
            ->andReturn(true);

        $irhpApplication->shouldReceive('canBeGranted')
            ->once()
            ->withNoArgs()
            ->andReturn(true);

        $irhpApplication->shouldReceive('grant')
            ->once()
            ->with($this->refData[IrhpInterface::STATUS_AWAITING_FEE])
            ->ordered()
            ->globally();

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $irhpApplication->shouldReceive('getFirstIrhpPermitApplication->getTotalEmissionsCategoryPermitsRequired')
            ->once()
            ->withNoArgs()
            ->andReturn(10);

        $irhpApplication->shouldReceive('getIssueFeeProductReference')
            ->once()
            ->withNoArgs()
            ->andReturn(FeeType::FEE_TYPE_ECMT_SHORT_TERM_ISSUE_PRODUCT_REF);

        $irhpApplication->shouldReceive('getLicence->getId')
            ->once()
            ->withNoArgs()
            ->andReturn(7);

        $irhpApplication->shouldReceive('getId')
            ->times(3)
            ->withNoArgs()
            ->andReturn(55);

        $this->repoMap['FeeType']->shouldReceive('getLatestByProductReference')
            ->with(FeeType::FEE_TYPE_ECMT_SHORT_TERM_ISSUE_PRODUCT_REF)
            ->andReturn($feeType);

        $feeType->shouldReceive('getDescription')
            ->withNoArgs()
            ->once()
            ->andReturn('IRHP GV ECMT permit monthly');

        $feeType->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn(40084);


        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $this->expectedSideEffect(
            CreateFee::class,
            [
                'licence' => 7,
                'irhpApplication' => $irhpApplicationId,
                'invoicedDate' => date("Y-m-d"),
                'description' => 'IRHP GV ECMT permit monthly',
                'feeType' => 40084,
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'quantity' => 10,
            ],
            new Result()
        );

        $this->expectedEmailQueueSideEffect(
            SendEcmtShortTermSuccessful::class,
            ['id' => $irhpApplicationId],
            $irhpApplicationId,
            new Result()
        );

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['IRHP application granted'],
            $result->getMessages()
        );

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
    }

    public function testHandleCommandCantGrantFromEntity()
    {
        $irhpApplicationId = 66;
        $irhpApplication = m::mock(IrhpApplication::class);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->mockedSmServices['PermitsGrantabilityChecker']->shouldReceive('isGrantable')
            ->with($irhpApplication)
            ->andReturn(true);

        $irhpApplication->shouldReceive('canBeGranted')
            ->once()
            ->withNoArgs()
            ->andReturn(false);

        $command = m::mock(CommandInterface::class);

        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('This application cannot be granted');

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandCantGrantFromService()
    {
        $irhpApplicationId = 66;
        $irhpApplication = m::mock(IrhpApplication::class);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->mockedSmServices['PermitsGrantabilityChecker']->shouldReceive('isGrantable')
            ->with($irhpApplication)
            ->andReturn(false);

        $command = m::mock(CommandInterface::class);

        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Insufficient permit availability to grant this application');

        $this->sut->handleCommand($command);
    }
}
