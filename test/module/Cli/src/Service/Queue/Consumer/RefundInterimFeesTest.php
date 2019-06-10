<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Command\Fee\UpdateFeeStatus;
use Dvsa\Olcs\Api\Domain\Command\Queue\Complete;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\RefundInterimFees;
use Dvsa\Olcs\Transfer\Command\Fee\RefundFee;
use Mockery as m;
use OlcsTest\Bootstrap;

class RefundInterimFeesTest extends AbstractConsumerTestCase
{
    /**
     * @var RefundInterimFees
     */
    protected $sut;

    /**
     * @var QueueEntity
     */
    private $item;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->chm = m::mock();
        $this->sm->setService('CommandHandlerManager', $this->chm);

        $this->qhm = m::mock();
        $this->sm->setService('QueryHandlerManager', $this->qhm);

        $this->item = new QueueEntity();
        $this->item->setId(1);
        $this->item->setEntityId(17);
    }

    public function testGetCommandData()
    {
        $this->makeSut(true);
        $this->assertSame(
            [
                'id' => $this->item->getEntityId()
            ],
            $this->sut->getCommandData($this->item)
        );
    }

    public function testProcessMessageCanRefund()
    {
        $this->makeSut(true);

        $this->expectCommand(
            RefundFee::class,
            [
                'id' => $this->item->getEntityId(),
                'customerReference' => null,
                'customerName' => null,
                'address' => null
            ],
            new Result()
        );

        $this->expectCommand(
            UpdateFeeStatus::class,
            [
                'id' => $this->item->getEntityId(),
                'status' => FeeEntity::STATUS_REFUNDED
            ],
            new Result(),
            false
        );

        $this->expectCommand(
            Complete::class,
            ['item' => $this->item],
            new Result(),
            false
        );


        $result = $this->sut->processMessage($this->item);

        $this->assertEquals(
            'Successfully processed message: ' . $this->item->getId() . ' ',
            $result
        );
    }

    public function testProcessMessageCanNotRefund()
    {
        $this->makeSut(false);

        $this->expectCommand(
            Failed::class,
            [
                'item' => $this->item,
                'lastError' => 'Fee cannot be refunded',
            ],
            new Result(),
            false
        );

        $this->expectCommand(
            UpdateFeeStatus::class,
            [
                'id' => $this->item->getEntityId(),
                'status' => FeeEntity::STATUS_REFUND_FAILED
            ],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($this->item);

        $this->assertEquals(
            'Failed to process message: ' . $this->item->getId() . '  Fee cannot be refunded',
            $result
        );
    }

    protected function makeSut(bool $canRefund)
    {
        $fee = m::mock(FeeEntity::class);
        $fee->shouldReceive('canRefund')
            ->andReturn($canRefund)
            ->getMock();

        $feeRepo = m::mock(FeeRepo::class);
        $feeRepo->shouldReceive('fetchById')
            ->andReturn($fee);

        $this->sut = new RefundInterimFees($feeRepo);
        $this->sut->setServiceLocator($this->sm);
    }
}
