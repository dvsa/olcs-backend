<?php


/**
 * Pay Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Transaction;

use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Transaction\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Transaction\PayOutstandingFees;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeePaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as PaymentEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as CpmsHelper;
use Dvsa\Olcs\Api\Service\FeesHelperService as FeesHelper;
use Dvsa\Olcs\Transfer\Command\Transaction\PayOutstandingFees as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Pay Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PayOutstandingFeesTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    protected $mockFeesHelperService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockFeesHelperService = m::mock(FeesHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
            'FeesHelperService' => $this->mockFeesHelperService,
            AuthorizationService::class => m::mock(AuthorizationService::class)->makePartial(),
            'Config' => [],
        ];

        $this->sut = new PayOutstandingFees();
        $this->mockRepo('Fee', Repository\Fee::class);
        $this->mockRepo('Transaction', Repository\Transaction::class);
        $this->mockRepo('Application', Repository\Application::class);

        $this->mockCpmsService
            ->shouldReceive('formatAmount')
            ->andReturnUsing(
                function ($input) {
                    return (string)$input;
                }
            );

        /** @var UserEntity $mockUser */
        $mockUser = m::mock(UserEntity::class)
            ->shouldReceive('getLoginId')
            ->andReturn('bob')
            ->getMock();

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            PaymentEntity::STATUS_OUTSTANDING,
            PaymentEntity::STATUS_PAID,
            PaymentEntity::STATUS_FAILED,
            PaymentEntity::TYPE_PAYMENT,
            FeeEntity::METHOD_CARD_ONLINE,
            FeeEntity::METHOD_CASH => m::mock(RefData::class)->makePartial()->setDescription('Cash'),
            FeeEntity::METHOD_CHEQUE => m::mock(RefData::class)->makePartial()->setDescription('Cheque'),
            FeeEntity::METHOD_POSTAL_ORDER => m::mock(RefData::class)->makePartial()->setDescription('Postal Order'),
            FeeEntity::STATUS_PAID,
            LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
            LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
        ];

        $this->references = [
            OrganisationEntity::class => [
                77 => m::mock(OrganisationEntity::class),
            ],
            LicenceEntity::class => [
                7 => m::mock(LicenceEntity::class),
            ],
            ApplicationEntity::class => [
                69 => m::mock(ApplicationEntity::class)
            ]
        ];

        parent::initReferences();
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
            ->with($cpmsRedirectUrl, $fees);

        /** @var PaymentEntity $savedPayment */
        $savedPayment = null;
        $this->repoMap['Transaction']
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
                'transaction' => $paymentId,
            ],
            'messages' => [
                'Transaction record created',
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
            ->setStatus($this->mapRefData(PaymentEntity::STATUS_OUTSTANDING))
            ->setId($paymentId)
            ->setPaymentMethod($this->mapRefData(FeeEntity::METHOD_CARD_ONLINE));
        $fp = new FeePaymentEntity();
        $fp->setTransaction($payment);
        $fee1 = $this->getStubFee(99, 150.00);
        $fee1->getFeeTransactions()->add($fp);

        $fees = [$fee1];

        $resolveResult = new Result();
        $resolveResult->addId('transaction', $paymentId);
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
            ->setStatus($this->mapRefData(PaymentEntity::STATUS_PAID));
        $this->repoMap['Transaction']
            ->shouldReceive('fetchById')
            ->once()
            ->with($paymentId)
            ->andReturn($updatedPayment);

        $this->sut->resolvePaidFees($fees, $result);
    }

    public function testResolvePaidFeesOutstandingPaymentUnpaid()
    {
        $result = new Result();

        // set up fee with outstanding payment that was not paid
        $paymentId = 222;
        $payment = new PaymentEntity();
        $payment
            ->setStatus($this->mapRefData(PaymentEntity::STATUS_OUTSTANDING))
            ->setId($paymentId)
            ->setPaymentMethod($this->mapRefData(FeeEntity::METHOD_CARD_ONLINE));
        $fp = new FeePaymentEntity();
        $fp->setTransaction($payment);
        $fee1 = $this->getStubFee(99, 150.00);
        $fee1->getFeeTransactions()->add($fp);

        $fees = [$fee1];

        $resolveResult = new Result();
        $resolveResult->addId('transaction', $paymentId);
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
            ->setStatus($this->mapRefData(PaymentEntity::STATUS_FAILED));
        $this->repoMap['Transaction']
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
            ->with($cpmsRedirectUrl, $fees);

        /** @var PaymentEntity $savedPayment */
        $savedPayment = null;
        $this->repoMap['Transaction']
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
                'transaction' => $paymentId,
            ],
            'messages' => [
                'Transaction record created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(PaymentEntity::STATUS_OUTSTANDING, $savedPayment->getStatus()->getId());
    }

    public function testHandleCommandWithApplicationId()
    {
        // set up data
        $cpmsRedirectUrl = 'https://olcs-selfserve/foo';
        $applicationId = 69;
        $organisationId = 77;
        $licenceId = 7;

        $paymentId = 999; // payment to be created

        $applicationFee = $this->getStubFee(99, 99.99);
        $interimFee = $this->getStubFee(101, 99.99);
        $fees = [$applicationFee, $interimFee];

        $data = [
            'applicationId' => $applicationId,
            'cpmsRedirectUrl' => $cpmsRedirectUrl,
            'paymentMethod' => FeeEntity::METHOD_CARD_OFFLINE,
        ];

        $command = Cmd::create($data);

        // mocks/set up references
        $organisation = $this->mapReference(OrganisationEntity::class, $organisationId);
        $licence = $this->mapReference(LicenceEntity::class, $licenceId);
        $licence->setOrganisation($organisation);
        $application = $this->mapReference(ApplicationEntity::class, $applicationId);
        $application->setLicence($licence);
        $applicationFee->setLicence($licence);

        // expectations
        $this->mockFeesHelperService
            ->shouldReceive('getOutstandingFeesForApplication')
            ->once()
            ->with($applicationId)
            ->andReturn($fees);

        $this->mockCpmsService
            ->shouldReceive('initiateCardRequest')
            ->once()
            ->with($cpmsRedirectUrl, $fees);

        /** @var PaymentEntity $savedPayment */
        $savedPayment = null;
        $this->repoMap['Transaction']
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
                'transaction' => $paymentId,
            ],
            'messages' => [
                'Transaction record created',
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
        $transactionId = 69;
        $feeTransactionId = 123;

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
            ->with($fees, '99.99', '2015-06-17', 'Dan', '12345')
            ->andReturn(
                [
                    'code' => CpmsHelper::RESPONSE_SUCCESS,
                    'receipt_reference' => 'OLCS-1234-CASH',
                ]
            );

        $this->mockFeesHelperService
            ->shouldReceive('getMinPaymentForFees')
            ->with($fees)
            ->andReturn(0.01);

        $this->mockFeesHelperService
            ->shouldReceive('allocatePayments')
            ->with('99.99', $fees)
            ->andReturn(
                [
                    99 => '99.99',
                ]
            );

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee1);

        $savedTransaction = null;
        $this->repoMap['Transaction']
            ->shouldReceive('save')
            ->andReturnUsing(
                function ($transaction) use (&$savedTransaction, $transactionId, $feeTransactionId) {
                    $savedTransaction = $transaction;
                    $savedTransaction->setId($transactionId);
                    $savedTransaction->getFeeTransactions()->forAll(
                        function ($key, $ft) use ($feeTransactionId) {
                            unset($key); // unused
                            $ft->setId($feeTransactionId);
                        }
                    );
                }
            );

        $updateData = ['id' => 99];
        $result2 = new Result();
        $this->expectedSideEffect(PayFeeCmd::class, $updateData, $result2);

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'transaction' => $transactionId,
                'feeTransaction' => [$feeTransactionId],
            ],
            'messages' => [
                'Fee ID 99 updated as paid by Cash',
                'Transaction record created: OLCS-1234-CASH',
                'FeeTransaction record(s) created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(FeeEntity::STATUS_PAID, $fee1->getFeeStatus()->getId());
        $this->assertEquals('2015-06-17', $savedTransaction->getCompletedDate()->format('Y-m-d'));
        $this->assertEquals('OLCS-1234-CASH', $savedTransaction->getReference());
        $this->assertEquals(FeeEntity::METHOD_CASH, $savedTransaction->getPaymentMethod()->getId());
        $this->assertEquals('Dan', $savedTransaction->getPayerName());
        $this->assertEquals('12345', $savedTransaction->getPayingInSlipNumber());
        $this->assertEquals('bob', $savedTransaction->getProcessedByUser()->getLoginId());
        $this->assertEquals('99.99', $savedTransaction->getFeeTransactions()->first()->getAmount());
        $this->assertEquals(PaymentEntity::STATUS_PAID, $savedTransaction->getStatus()->getId());
        $this->assertEquals(PaymentEntity::TYPE_PAYMENT, $savedTransaction->getType()->getId());
    }

    public function testHandleCommandChequePayment()
    {
        // set up data
        $feeIds = [99];
        $fee1 = $this->getStubFee(99, 99.99);
        $fees = [$fee1];
        $transactionId = 69;
        $feeTransactionId = 123;

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
            ->with($fees, '99.99', '2015-06-17', 'Dan', '12345', '23456', '2015-06-10')
            ->andReturn(
                [
                    'code' => CpmsHelper::RESPONSE_SUCCESS,
                    'receipt_reference' => 'OLCS-1234-CHEQUE',
                ]
            );

        $this->mockFeesHelperService
            ->shouldReceive('getMinPaymentForFees')
            ->with($fees)
            ->andReturn(0.01);

        $this->mockFeesHelperService
            ->shouldReceive('allocatePayments')
            ->with('99.99', $fees)
            ->andReturn(
                [
                    99 => '99.99',
                ]
            );

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee1);

        $savedTransaction = null;
        $this->repoMap['Transaction']
            ->shouldReceive('save')
            ->andReturnUsing(
                function ($transaction) use (&$savedTransaction, $transactionId, $feeTransactionId) {
                    $savedTransaction = $transaction;
                    $savedTransaction->setId($transactionId);
                    $savedTransaction->getFeeTransactions()->forAll(
                        function ($key, $ft) use ($feeTransactionId) {
                            unset($key);
                            $ft->setId($feeTransactionId);
                        }
                    );
                }
            );

        $updateData = ['id' => 99];
        $result2 = new Result();
        $this->expectedSideEffect(PayFeeCmd::class, $updateData, $result2);

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'transaction' => $transactionId,
                'feeTransaction' => [$feeTransactionId],
            ],
            'messages' => [
                'Fee ID 99 updated as paid by Cheque',
                'Transaction record created: OLCS-1234-CHEQUE',
                'FeeTransaction record(s) created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(FeeEntity::STATUS_PAID, $fee1->getFeeStatus()->getId());
        $this->assertEquals('2015-06-17', $savedTransaction->getCompletedDate()->format('Y-m-d'));
        $this->assertEquals('OLCS-1234-CHEQUE', $savedTransaction->getReference());
        $this->assertEquals(FeeEntity::METHOD_CHEQUE, $savedTransaction->getPaymentMethod()->getId());
        $this->assertEquals('Dan', $savedTransaction->getPayerName());
        $this->assertEquals('12345', $savedTransaction->getPayingInSlipNumber());
        $this->assertEquals('bob', $savedTransaction->getProcessedByUser()->getLoginId());
        $this->assertEquals('99.99', $savedTransaction->getFeeTransactions()->first()->getAmount());
        $this->assertEquals(PaymentEntity::STATUS_PAID, $savedTransaction->getStatus()->getId());
        $this->assertEquals(PaymentEntity::TYPE_PAYMENT, $savedTransaction->getType()->getId());
        $this->assertEquals('23456', $savedTransaction->getChequePoNumber());
        $this->assertEquals('2015-06-10', $savedTransaction->getChequePoDate()->format('Y-m-d'));
    }

    public function testHandleCommandPoPayment()
    {
        // set up data
        $feeIds = [99];
        $fee1 = $this->getStubFee(99, 99.99);
        $fees = [$fee1];
        $transactionId = 69;
        $feeTransactionId = 123;

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
            ->with($fees, '99.99', '2015-06-17', 'Dan', '12345', '23456')
            ->andReturn(
                [
                    'code' => CpmsHelper::RESPONSE_SUCCESS,
                    'receipt_reference' => 'OLCS-1234-PO',
                ]
            );

        $this->mockFeesHelperService
            ->shouldReceive('getMinPaymentForFees')
            ->with($fees)
            ->andReturn(0.01);

        $this->mockFeesHelperService
            ->shouldReceive('allocatePayments')
            ->with('99.99', $fees)
            ->andReturn(
                [
                    99 => '99.99',
                ]
            );

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee1);

        $savedTransaction = null;
        $this->repoMap['Transaction']
            ->shouldReceive('save')
            ->andReturnUsing(
                function ($transaction) use (&$savedTransaction, $transactionId, $feeTransactionId) {
                    $savedTransaction = $transaction;
                    $savedTransaction->setId($transactionId);
                    $savedTransaction->getFeeTransactions()->forAll(
                        function ($key, $ft) use ($feeTransactionId) {
                            unset($key);
                            $ft->setId($feeTransactionId);
                        }
                    );
                }
            );

        $updateData = ['id' => 99];
        $result2 = new Result();
        $this->expectedSideEffect(PayFeeCmd::class, $updateData, $result2);

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'transaction' => $transactionId,
                'feeTransaction' => [$feeTransactionId],
            ],
            'messages' => [
                'Fee ID 99 updated as paid by Postal Order',
                'Transaction record created: OLCS-1234-PO',
                'FeeTransaction record(s) created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(FeeEntity::STATUS_PAID, $fee1->getFeeStatus()->getId());
        $this->assertEquals('2015-06-17', $savedTransaction->getCompletedDate()->format('Y-m-d'));
        $this->assertEquals('OLCS-1234-PO', $savedTransaction->getReference());
        $this->assertEquals(FeeEntity::METHOD_POSTAL_ORDER, $savedTransaction->getPaymentMethod()->getId());
        $this->assertEquals('Dan', $savedTransaction->getPayerName());
        $this->assertEquals('12345', $savedTransaction->getPayingInSlipNumber());
        $this->assertEquals('bob', $savedTransaction->getProcessedByUser()->getLoginId());
        $this->assertEquals('99.99', $savedTransaction->getFeeTransactions()->first()->getAmount());
        $this->assertEquals(PaymentEntity::STATUS_PAID, $savedTransaction->getStatus()->getId());
        $this->assertEquals(PaymentEntity::TYPE_PAYMENT, $savedTransaction->getType()->getId());
        $this->assertEquals('23456', $savedTransaction->getChequePoNumber());
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

        $this->mockFeesHelperService
            ->shouldReceive('getMinPaymentForFees')
            ->with($fees)
            ->andReturn(99);

        // $this->setExpectedException(ValidationException::class, "The amount must be at least ");
        // try/catch as we can't assert the message otherwise
        try {
            $this->sut->handleCommand($command);
        } catch (ValidationException $e) {
            $messages = $e->getMessages();
            $this->assertTrue(strpos(reset($messages), 'Amount must be at least 99.00') !== false);
        }
    }

    public function testHandleCommandCpmsResponseException()
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
            ->andThrow(new \Dvsa\Olcs\Api\Service\CpmsResponseException('ohnoes'));

        $this->mockFeesHelperService
            ->shouldReceive('getMinPaymentForFees')
            ->with($fees)
            ->andReturn(0.01);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);

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
