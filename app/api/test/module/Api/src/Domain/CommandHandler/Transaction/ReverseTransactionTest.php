<?php

/**
 * Reverse Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Transaction;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Transaction\ReverseTransaction;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Transaction\ReverseTransaction as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Reverse Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ReverseTransactionTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
            AuthorizationService::class => m::mock(AuthorizationService::class)->makePartial(),
            'Config' => [],
        ];

        $this->sut = new ReverseTransaction();
        $this->mockRepo('Fee', Repository\Fee::class);
        $this->mockRepo('Transaction', Repository\Transaction::class);

        /** @var UserEntity $mockUser */
        $mockUser = m::mock(UserEntity::class)
            ->shouldReceive('getLoginId')
            ->andReturn('bob')
            ->getMock();

        $this->mockedSmServices[AuthorizationService::class]
            ->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            // TransactionEntity::TYPE_REFUND,
            TransactionEntity::STATUS_COMPLETE,
            FeeEntity::STATUS_OUTSTANDING => m::mock(RefData::class)
                ->makePartial()
                ->shouldReceive('getDescription')
                ->andReturn('Outstanding')
                ->getMock(),
            FeeEntity::METHOD_REVERSAL,
            FeeEntity::METHOD_CHEQUE,
            FeeEntity::METHOD_CARD_ONLINE,
            FeeEntity::METHOD_CARD_OFFLINE,
        ];

        $this->references = [
            TransactionEntity::class => [
                123 => m::mock(TransactionEntity::class),
            ],
            FeeEntity::class => [
                69 => m::mock(FeeEntity::class),
            ],
            FeeTransactionEntity::class => [
                101 => m::mock(FeeTransactionEntity::class),
            ],
        ];

        parent::initReferences();
    }

    /**
     * @param string $paymentMethod
     * @param string $expectedHelperMethod
     * @dataProvider handleCommandProvider
     */
    public function testHandleCommand($paymentMethod, $expectedHelperMethod)
    {
        $now = new DateTime();
        $transactionId = 123;
        $transactionReference = 'MY-REFERENCE';

        $data = [
            'id' => $transactionId,
            'reason' => 'bounced cheque',
        ];
        $command = Cmd::create($data);

        $transaction = $this->mapReference(TransactionEntity::class, $transactionId);
        $fee = $this->mapReference(FeeEntity::class, 69);

        $transaction
            ->shouldReceive('getPaymentMethod')
            ->andReturn($this->mapRefData($paymentMethod));
        $transaction
            ->shouldReceive('getFeeTransactions->first->getFee')
            ->andReturn($fee);
        $transaction
            ->shouldReceive('getReference')
            ->andReturn($transactionReference);
        $transaction
            ->shouldReceive('isComplete')
            ->andReturn(true)
            ->shouldReceive('canReverse')
            ->andReturn(true);

        $fee
            ->shouldReceive('isBalancingFee')
            ->andReturn(false);

        $ft1 = $this->mapReference(FeeTransactionEntity::class, 101);
        $ft1
            ->shouldReceive('getFee')
            ->andReturn($fee)
            ->shouldReceive('getAmount')
            ->andReturn('10.00');
        $transaction
            ->shouldReceive('getFeeTransactionsForReversal')
            ->once()
            ->andReturn([$ft1]);

        $this->repoMap['Transaction']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($transaction);

        $this->mockCpmsService
            ->shouldReceive($expectedHelperMethod)
            ->once()
            ->with($transactionReference, [$fee])
            ->andReturn(
                [
                    'receipt_reference' => 'REFUND_REF_1',
                    'code' => CpmsHelper::PAYMENT_PAYMENT_CHARGED_BACK,
                    'message' => 'ok',
                ]
            );

        $savedTransaction = null;
        $this->repoMap['Transaction']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(TransactionEntity::class))
            ->andReturnUsing(
                function ($txn) use (&$savedTransaction) {
                    $savedTransaction = $txn;
                    $savedTransaction->setId(999);
                    return true;
                }
            );

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee)
            ->andReturnUsing(
                function ($fee) {
                    $this->assertEquals($this->mapRefData(FeeEntity::STATUS_OUTSTANDING), $fee->getFeeStatus());
                    return;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'transaction' => 999,
            ],
            'messages' => [
                "Transaction 123 reversed using [$expectedHelperMethod]",
                'Transaction record created',
                'Fee 69 reset to Outstanding',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->mapRefData(TransactionEntity::TYPE_REVERSAL), $savedTransaction->getType());
        $this->assertSame($this->mapRefData(TransactionEntity::STATUS_COMPLETE), $savedTransaction->getStatus());
        $this->assertSame($this->mapRefData(FeeEntity::METHOD_REVERSAL), $savedTransaction->getPaymentMethod());
        $this->assertEquals('bounced cheque', $savedTransaction->getComment());
        $this->assertEquals($now, $savedTransaction->getCompletedDate());
        $this->assertEquals('bob', $savedTransaction->getProcessedByUser()->getLoginId());
        $this->assertEquals('REFUND_REF_1', $savedTransaction->getReference());
    }

    public function handleCommandProvider()
    {
        return [
            'cheque' => [FeeEntity::METHOD_CHEQUE, 'reverseChequePayment'],
            'digital card' => [FeeEntity::METHOD_CARD_ONLINE, 'chargeBackCardPayment'],
            'assisted digital card' => [FeeEntity::METHOD_CARD_OFFLINE, 'chargeBackCardPayment'],
        ];
    }

    public function testHandleCommandCpmsResponseException()
    {
        $transactionId = 123;

        $command = Cmd::create(['id' => $transactionId, 'reason' => 'foo']);

        $transaction = $this->mapReference(TransactionEntity::class, $transactionId);
        $fee = $this->mapReference(FeeEntity::class, 69);

        $transaction
            ->shouldReceive('getPaymentMethod')
            ->andReturn($this->mapRefData(FeeEntity::METHOD_CHEQUE));
        $transaction
            ->shouldReceive('getFeeTransactions->first->getFee')
            ->andReturn($fee);
        $transaction
            ->shouldReceive('getReference')
            ->andReturn('MY-REFERENCE');
        $transaction
            ->shouldReceive('isComplete')
            ->andReturn(true)
            ->shouldReceive('canReverse')
            ->andReturn(true);
        $this->repoMap['Transaction']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($transaction);

        $this->mockCpmsService
            ->shouldReceive('reverseChequePayment')
            ->once()
            ->with('MY-REFERENCE', [$fee])
            ->andThrow(new \Dvsa\Olcs\Api\Service\CpmsResponseException('ohnoes'));

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);

        $this->sut->handleCommand($command);
    }

    public function testValidateIncompleteTransaction()
    {
        $transaction = m::mock(TransactionEntity::class)
            ->shouldReceive('isComplete')
            ->andReturn(false)
            ->getMock();

        $this->setExpectedException(ValidationException::class);

        $this->sut->validate($transaction);
    }

    public function testValidateIrreversibleTransaction()
    {
        $transaction = m::mock(TransactionEntity::class)
            ->shouldReceive('isComplete')
            ->andReturn(true)
            ->shouldReceive('canReverse')
            ->andReturn(false)
            ->getMock();

        $this->setExpectedException(ValidationException::class);

        $this->sut->validate($transaction);
    }
}
