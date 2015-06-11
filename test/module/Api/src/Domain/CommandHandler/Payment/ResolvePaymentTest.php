<?php

/**
 * Resolve Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Payment;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Payment\ResolvePayment;
use Dvsa\Olcs\Api\Domain\Command\Payment\ResolvePayment as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Payment as PaymentRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeePayment as FeePaymentEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\CpmsHelperService as CpmsHelper;

/**
 * Resolve Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ResolvePaymentTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
        ];

        $this->sut = new ResolvePayment();
        $this->mockRepo('Payment', PaymentRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            PaymentEntity::STATUS_PAID => m::mock(RefData::class)
                ->shouldReceive('getDescription')
                ->andReturn('PAYMENT PAID')
                ->getMock(),
            FeeEntity::STATUS_PAID => m::mock(RefData::class)
                ->shouldReceive('getDescription')
                ->andReturn('FEE PAID')
                ->getMock(),
            FeeEntity::METHOD_CARD_ONLINE,
        ];

        $this->references = [
            FeePaymentEntity::class => [
                11 => m::mock(FeePaymentEntity::class)->makePartial(),
            ],
            FeeEntity::class => [
                22 => m::mock(FeeEntity::class)->makePartial(),
            ],
        ];

        $this->references[FeePaymentEntity::class][11]
            ->setFee($this->references[FeeEntity::class][22]);

        parent::initReferences();
    }

    public function testHandleCommandSuccess()
    {
        // set up data
        $paymentId = 69;
        $guid = 'OLCS-1234-ABCDE';
        $amount = '1234.56';

        $data = [
            'id' => $paymentId,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
        ];

        $fee = $this->references[FeeEntity::class][22];
        $fee->setAmount($amount);

        $payment = m::mock(PaymentEntity::class)->makePartial();
        $payment->setId($paymentId);
        $payment->setGuid($guid);
        $payment->setFeePayments($this->references[FeePaymentEntity::class]);

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Payment']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($payment);

        $this->mockCpmsService
            ->shouldReceive('getPaymentStatus')
            ->once()
            ->with($guid)
            ->andReturn(CpmsHelper::PAYMENT_SUCCESS);

        $this->repoMap['Payment']
            ->shouldReceive('save')
            ->once()
            ->with($payment);

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee);

        // assertions
        $result = $this->sut->handleCommand($command);

        $this->assertEquals('FEE PAID', $fee->getFeeStatus()->getDescription());
        $this->assertEquals($guid, $fee->getReceiptNo());
        $this->assertEquals(FeeEntity::METHOD_CARD_ONLINE, $fee->getPaymentMethod()->getId());
        $this->assertEquals($amount, $fee->getReceivedAmount());

        $expected = [
            'id' => [
                'payment' => 69,
            ],
            'messages' => [
                'Payment resolved as PAYMENT PAID'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
