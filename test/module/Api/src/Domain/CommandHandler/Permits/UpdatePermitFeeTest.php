<?php

/**
 * CancelFeeTestt
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCommand;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Permits\UpdatePermitFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdatePermitFee as UpdatePermitFeeHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\CancelFee;

use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Transfer\Command\Task\CloseTasks as CloseTasksCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * CancelFeeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdatePermitFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdatePermitFeeHandler();
        $this->mockRepo('Fee', Fee::class);
        $this->mockRepo('FeeType', FeeType::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $fees = [];

        $data = [
            'ecmtPermitApplicationId' => 1,
            'licenceId' => 7,
            'permitsRequired' => 10,
            'permitType' => EcmtPermitApplication::PERMIT_TYPE,
            'receivedDate' => '2018-09-01'
        ];

        $command = UpdatePermitFee::create($data);
        $feeType = m::mock(FeeType::class);

        $this->repoMap['Fee']->shouldReceive('fetchFeeByEcmtPermitApplicationId')->with($command->getEcmtPermitApplicationId())->andReturn($fees);
        $this->repoMap['FeeType']->shouldReceive('getSpecificDateEcmtPermit')
            ->with(FeeType::FEE_TYPE_ECMT_APP_PRODUCT_REF, $command->getReceivedDate())
            ->andReturn($feeType);
        $feeType->shouldReceive('getId')->andReturn(40075);
        $feeType->shouldReceive('getFixedValue')->andReturn(10);
        $feeType->shouldReceive('getDescription')->andReturn('descr');

        $taskResult = new Result();

        $this->expectedSideEffect(
            CreateFee::class,
            [ 'licence' => $command->getLicenceId(),
                'ecmtPermitApplication' => $command->getEcmtPermitApplicationId(),
                'invoicedDate' => date('Y-m-d'),
                'feeType' => $feeType->getId(),
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'amount' => $feeType->getFixedValue(),
                'quantity' => $command->getPermitsRequired()],
            $taskResult
        );


        $result = $this->sut->handleCommand($command);
    }
}
