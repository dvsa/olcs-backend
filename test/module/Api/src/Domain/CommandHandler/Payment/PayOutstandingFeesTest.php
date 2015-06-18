<?php

/**
 * Pay Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Payment\PayOutstandingFees;
use Dvsa\Olcs\Api\Domain\Command\Payment\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeePayment as FeePaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\CpmsHelperService as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Payment\PayOutstandingFees as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Pay Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PayOutstandingFeesTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
        ];

        $this->sut = new PayOutstandingFees();
        $this->mockRepo('Fee', '\Dvsa\Olcs\Api\Domain\Repository\Fee');
        $this->mockRepo('Payment', '\Dvsa\Olcs\Api\Domain\Repository\Payment');

        $this->refData = [
            PaymentEntity::STATUS_OUTSTANDING,
            PaymentEntity::STATUS_PAID,
            PaymentEntity::STATUS_FAILED,
            FeeEntity::METHOD_CARD_ONLINE,
            FeeEntity::METHOD_CASH,
            FeeEntity::METHOD_CHEQUE,
            FeeEntity::METHOD_POSTAL_ORDER,
            FeeEntity::STATUS_PAID,
        ];

        $this->mockCpmsService
            ->shouldReceive('formatAmount')
            ->andReturnUsing(
                function ($input) {
                    return (string)$input;
                }
            );

        parent::setUp();
    }

    public function testHandleCommandWithOrgId()
    {
        // set up data
        $organisationId = 69;
        $feeIds = [99, 100, 101];
        $cpmsRedirectUrl = 'https://olcs-selfserve/foo';

        $paymentId = 999; // payment to be created

        $fees = [
            $this->getStubFee(99, 99.99),
            $this->getStubFee(101, 99.99),
        ];

        $data = [
            'feeIds' => $feeIds,
            'organisationId' => $organisationId,
            'cpmsRedirectUrl' => $cpmsRedirectUrl,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
        ];

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByOrganisationId')
            ->once()
            ->with($organisationId)
            ->andReturn($fees);

        $this->mockCpmsService
            ->shouldReceive('initiateCardRequest')
            ->once()
            ->with($organisationId, $cpmsRedirectUrl, $fees);

        /** @var PaymentEntity $savedPayment */
        $savedPayment = null;
        $this->repoMap['Payment']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PaymentEntity::class))
            ->andReturnUsing(
                function (PaymentEntity $payment) use (&$savedPayment, $paymentId) {
                    $payment->setId($paymentId);
                    $savedPayment = $payment;
                }
            );

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'payment' => $paymentId,
            ],
            'messages' => [
                'Payment record created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(PaymentEntity::STATUS_OUTSTANDING, $savedPayment->getStatus()->getId());
    }

    public function testHandleCommandNoOp()
    {
        // set up data
        $organisationId = 69;
        $feeIds = [99, 100, 101];
        $cpmsRedirectUrl = 'https://olcs-selfserve/foo';

        $fees = [];

        $data = [
            'feeIds' => $feeIds,
            'organisationId' => $organisationId,
            'cpmsRedirectUrl' => $cpmsRedirectUrl,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
        ];

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByOrganisationId')
            ->once()
            ->with($organisationId)
            ->andReturn($fees);

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'No fees to pay',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testResolvePaidFees()
    {
        $result = new Result();

        // set up fee with outstanding payment that was paid
        $paymentId = 222;
        $payment = new PaymentEntity();
        $payment
            ->setStatus($this->refData[PaymentEntity::STATUS_OUTSTANDING])
            ->setId($paymentId);
        $fp = new FeePaymentEntity();
        $fp->setPayment($payment);
        $fee1 = $this->getStubFee(99, 150.00);
        $fee1
            ->setPaymentMethod($this->refData[FeeEntity::METHOD_CARD_ONLINE])
            ->getFeePayments()->add($fp);

        $fees = [$fee1];

        $resolveResult = new Result();
        $resolveResult->addId('payment', $paymentId);
        $this->expectedSideEffect(
            ResolvePaymentCommand::class,
            [
                'id' => $paymentId,
                'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
            ],
            $resolveResult
        );

        $updatedPayment = new PaymentEntity();
        $updatedPayment
            ->setId($paymentId)
            ->setStatus($this->refData[PaymentEntity::STATUS_PAID]);
        $this->repoMap['Payment']
            ->shouldReceive('fetchById')
            ->once()
            ->with($paymentId)
            ->andReturn($updatedPayment);

        $this->sut->resolvePaidFees($fees, $result);
    }

    public function testResolvePaidFeesOutstandingPaymentUnpaid()
    {
        $result = new Result();

        // set up fee with outstanding payment that was paid
        $paymentId = 222;
        $payment = new PaymentEntity();
        $payment
            ->setStatus($this->refData[PaymentEntity::STATUS_OUTSTANDING])
            ->setId($paymentId);
        $fp = new FeePaymentEntity();
        $fp->setPayment($payment);
        $fee1 = $this->getStubFee(99, 150.00);
        $fee1
            ->setPaymentMethod($this->refData[FeeEntity::METHOD_CARD_ONLINE])
            ->getFeePayments()->add($fp);

        $fees = [$fee1];

        $resolveResult = new Result();
        $resolveResult->addId('payment', $paymentId);
        $this->expectedSideEffect(
            ResolvePaymentCommand::class,
            [
                'id' => $paymentId,
                'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
            ],
            $resolveResult
        );

        $updatedPayment = new PaymentEntity();
        $updatedPayment
            ->setId($paymentId)
            ->setStatus($this->refData[PaymentEntity::STATUS_FAILED]);
        $this->repoMap['Payment']
            ->shouldReceive('fetchById')
            ->once()
            ->with($paymentId)
            ->andReturn($updatedPayment);

        $this->sut->resolvePaidFees($fees, $result);
    }

    public function testHandleCommandWithFeeIds()
    {
        // set up data
        $organisationId = 77;
        $feeIds = [99, 100, 101];
        $cpmsRedirectUrl = 'https://olcs-selfserve/foo';

        $paymentId = 999; // payment to be created

        $fee1 = $this->getStubFee(99, 99.99);
        $fee2 = $this->getStubFee(101, 99.99);
        $fees = [$fee1, $fee2];

        $data = [
            'feeIds' => $feeIds,
            'cpmsRedirectUrl' => $cpmsRedirectUrl,
            'paymentMethod' => FeeEntity::METHOD_CARD_OFFLINE,
        ];

        $command = Cmd::create($data);

        // mocks
        $licence = m::mock();
        $organisation = m::mock();
        $licence
            ->shouldReceive('getOrganisation')
            ->andReturn($organisation)
            ->getMock();
        $organisation
            ->shouldReceive('getId')
            ->andReturn($organisationId);
        $fee1->setLicence($licence);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByIds')
            ->once()
            ->with($feeIds)
            ->andReturn($fees);

        $this->mockCpmsService
            ->shouldReceive('initiateCardRequest')
            ->once()
            ->with($organisationId, $cpmsRedirectUrl, $fees);

        /** @var PaymentEntity $savedPayment */
        $savedPayment = null;
        $this->repoMap['Payment']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PaymentEntity::class))
            ->andReturnUsing(
                function (PaymentEntity $payment) use (&$savedPayment, $paymentId) {
                    $payment->setId($paymentId);
                    $savedPayment = $payment;
                }
            );

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'payment' => $paymentId,
            ],
            'messages' => [
                'Payment record created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(PaymentEntity::STATUS_OUTSTANDING, $savedPayment->getStatus()->getId());
    }

    public function testHandleCommandCashPayment()
    {
        // set up data
        $feeIds = [99];
        $fee1 = $this->getStubFee(99, 99.99);
        $fees = [$fee1];

        $data = [
            'feeIds' => $feeIds,
            'paymentMethod' => FeeEntity::METHOD_CASH,
            'receiptDate' => '2015-06-17',
            'payer' => 'Dan',
            'slipNo' => '12345',
            'received' => '99.99',
        ];

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByIds')
            ->once()
            ->with($feeIds)
            ->andReturn($fees);

        $this->mockCpmsService
            ->shouldReceive('recordCashPayment')
            ->once()
            ->with($fees, 'Miscellaneous', '99.99', '2015-06-17', 'Dan', '12345')
            ->andReturn(
                [
                    'code' => CpmsHelper::RESPONSE_SUCCESS,
                    'receipt_reference' => 'OLCS-1234-CASH',
                ]
            );

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee1);

        $updateData = ['id' => 99];
        $result2 = new Result();
        $this->expectedSideEffect(PayFeeCmd::class, $updateData, $result2);

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Fee(s) updated as Paid by cash',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(FeeEntity::STATUS_PAID, $fee1->getFeeStatus()->getId());
        $this->assertEquals('2015-06-17', $fee1->getReceivedDate()->format('Y-m-d'));
        $this->assertEquals('OLCS-1234-CASH', $fee1->getReceiptNo());
        $this->assertEquals(FeeEntity::METHOD_CASH, $fee1->getPaymentMethod()->getId());
        $this->assertEquals('Dan', $fee1->getPayerName());
        $this->assertEquals('12345', $fee1->getPayingInSlipNumber());
        $this->assertEquals('99.99', $fee1->getReceivedAmount());
    }

    public function testHandleCommandChequePayment()
    {
        // set up data
        $feeIds = [99];
        $fee1 = $this->getStubFee(99, 99.99);
        $fees = [$fee1];

        $data = [
            'feeIds' => $feeIds,
            'paymentMethod' => FeeEntity::METHOD_CHEQUE,
            'receiptDate' => '2015-06-17',
            'payer' => 'Dan',
            'slipNo' => '12345',
            'received' => '99.99',
            'chequeNo' => '23456',
            'chequeDate' => '2015-06-10',
        ];

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByIds')
            ->once()
            ->with($feeIds)
            ->andReturn($fees);

        $this->mockCpmsService
            ->shouldReceive('recordChequePayment')
            ->once()
            ->with($fees, 'Miscellaneous', '99.99', '2015-06-17', 'Dan', '12345', '23456', '2015-06-10')
            ->andReturn(
                [
                    'code' => CpmsHelper::RESPONSE_SUCCESS,
                    'receipt_reference' => 'OLCS-1234-CHEQUE',
                ]
            );

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee1);

        $updateData = ['id' => 99];
        $result2 = new Result();
        $this->expectedSideEffect(PayFeeCmd::class, $updateData, $result2);

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Fee(s) updated as Paid by cheque',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(FeeEntity::STATUS_PAID, $fee1->getFeeStatus()->getId());
        $this->assertEquals('2015-06-17', $fee1->getReceivedDate()->format('Y-m-d'));
        $this->assertEquals('OLCS-1234-CHEQUE', $fee1->getReceiptNo());
        $this->assertEquals(FeeEntity::METHOD_CHEQUE, $fee1->getPaymentMethod()->getId());
        $this->assertEquals('Dan', $fee1->getPayerName());
        $this->assertEquals('12345', $fee1->getPayingInSlipNumber());
        $this->assertEquals('99.99', $fee1->getReceivedAmount());
        $this->assertEquals('23456', $fee1->getChequePoNumber());
        $this->assertEquals('2015-06-10', $fee1->getChequePoDate()->format('Y-m-d'));
    }

    public function testHandleCommandPoPayment()
    {
        // set up data
        $feeIds = [99];
        $fee1 = $this->getStubFee(99, 99.99);
        $fees = [$fee1];

        $data = [
            'feeIds' => $feeIds,
            'paymentMethod' => FeeEntity::METHOD_POSTAL_ORDER,
            'receiptDate' => '2015-06-17',
            'payer' => 'Dan',
            'slipNo' => '12345',
            'received' => '99.99',
            'poNo' => '23456',
        ];

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByIds')
            ->once()
            ->with($feeIds)
            ->andReturn($fees);

        $this->mockCpmsService
            ->shouldReceive('recordPostalOrderPayment')
            ->once()
            ->with($fees, 'Miscellaneous', '99.99', '2015-06-17', 'Dan', '12345', '23456')
            ->andReturn(
                [
                    'code' => CpmsHelper::RESPONSE_SUCCESS,
                    'receipt_reference' => 'OLCS-1234-PO',
                ]
            );

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee1);

        $updateData = ['id' => 99];
        $result2 = new Result();
        $this->expectedSideEffect(PayFeeCmd::class, $updateData, $result2);

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Fee(s) updated as Paid by postal order',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(FeeEntity::STATUS_PAID, $fee1->getFeeStatus()->getId());
        $this->assertEquals('2015-06-17', $fee1->getReceivedDate()->format('Y-m-d'));
        $this->assertEquals('OLCS-1234-PO', $fee1->getReceiptNo());
        $this->assertEquals(FeeEntity::METHOD_POSTAL_ORDER, $fee1->getPaymentMethod()->getId());
        $this->assertEquals('Dan', $fee1->getPayerName());
        $this->assertEquals('12345', $fee1->getPayingInSlipNumber());
        $this->assertEquals('99.99', $fee1->getReceivedAmount());
        $this->assertEquals('23456', $fee1->getChequePoNumber());
    }

    public function testHandleCommandAmountMismatch()
    {
        // set up data
        $feeIds = [99];
        $fee1 = $this->getStubFee(99, 99.99);
        $fees = [$fee1];

        $data = [
            'feeIds' => $feeIds,
            'paymentMethod' => FeeEntity::METHOD_CASH,
            'receiptDate' => '2015-06-17',
            'payer' => 'Dan',
            'slipNo' => '12345',
            'received' => '98.99', // 1 quid short :(
        ];

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByIds')
            ->once()
            ->with($feeIds)
            ->andReturn($fees);

        $this->mockCpmsService
            ->shouldReceive('recordCashPayment')
            ->never();

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    /**
     * Helper function to generate a stub fee entity
     *
     * @param int $id
     * @param string $amount
     * @return FeeEntity
     */
    private function getStubFee($id, $amount)
    {
        $status = new RefData();
        $feeType = new FeeTypeEntity();

        $fee = new FeeEntity($feeType, $amount, $status);
        $fee->setId($id);

        return $fee;
    }
}
