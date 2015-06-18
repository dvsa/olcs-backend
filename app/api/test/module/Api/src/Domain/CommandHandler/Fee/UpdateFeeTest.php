<?php

/**
 * Update Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\UpdateFee;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Transfer\Command\Fee\UpdateFee as UpdateFeeCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Update Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class UpdateFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateFee();
        $this->mockRepo('Fee', FeeRepo::class);

        $this->refData = [
            FeeEntity::STATUS_PAID
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $feeId = 69;

        $command = UpdateFeeCmd::create(
            [
                'id' => $feeId,
                'version' => 1,
                'status' => FeeEntity::STATUS_WAIVED,
            ]
        );

        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee
            ->setId($feeId)
            ->setWaiveReason('foo');

        $this->repoMap['Fee']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->once()
            ->andReturn($fee);

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->with($fee)
            ->once();

        $result1 = new Result();
        $expectedData = ['id' => $feeId];
        $this->expectedSideEffect(PayFeeCmd::class, $expectedData, $result1);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);

        $this->assertEquals($feeId, $result->getId('fee'));
        $this->assertEquals('Fee updated', $result->getMessages()[0]);
    }
}
