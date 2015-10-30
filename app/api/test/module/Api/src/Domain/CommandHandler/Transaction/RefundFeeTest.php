<?php

/**
 * Refund Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Transaction;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\RefundFee;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Fee\RefundFee as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Refund Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class RefundFeeTest extends CommandHandlerTestCase
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

        $this->sut = new RefundFee();
        $this->mockRepo('Fee', Repository\Fee::class);

        // $this->mockCpmsService
        //     ->shouldReceive('formatAmount')
        //     ->andReturnUsing(
        //         function ($input) {
        //             return (string)$input;
        //         }
        //     );

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
            TransactionEntity::TYPE_REFUND,
            TransactionEntity::STATUS_COMPLETE,
            FeeEntity::METHOD_REFUND,
        ];

        $this->references = [
            FeeEntity::class => [
                69 => m::mock(FeeEntity::class),
            ],
            FeeTransactionEntity::class => [
                101 => m::mock(FeeTransactionEntity::class),
                102 => m::mock(FeeTransactionEntity::class),
                103 => m::mock(FeeTransactionEntity::class),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $now = new DateTime();

        // set up data
        $feeId = 69;

        $data = [
            'id' => $feeId,
        ];

        $ft1 = $this->mapReference(FeeTransactionEntity::class, 101);
        $ft1
            ->shouldReceive('getTransaction->getReference')
            ->andReturn('payment_ref_1');
        $ft1
            ->shouldReceive('getAmount')
            ->andReturn('10.00');

        $ft2 = $this->mapReference(FeeTransactionEntity::class, 102);
        $ft2
            ->shouldReceive('getTransaction->getReference')
            ->andReturn('payment_ref_2');
        $ft2
            ->shouldReceive('getAmount')
            ->andReturn('5.00');

        $ft3 = $this->mapReference(FeeTransactionEntity::class, 103);
        $ft3
            ->shouldReceive('getTransaction->getReference')
            ->andReturn('payment_ref_3');
        $ft3
            ->shouldReceive('getAmount')
            ->andReturn('1.00');

        $fee = $this->mapReference(FeeEntity::class, $feeId);
        $fee->setFeeTransactions(new ArrayCollection([$ft1, $ft2, $ft3]));
        $fee->shouldReceive('getFeeTransactionsForRefund')
            ->andReturn(new ArrayCollection([$ft1, $ft2, $ft3]));

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($fee);

        $this->mockCpmsService
            ->shouldReceive('batchRefund')
            ->once()
            ->with($fee)
            ->andReturn(
                [
                    'receipt_references' => [
                        'payment_ref_1' => 'refund_ref_1',
                        'payment_ref_2' => 'refund_ref_2',
                        // 'payment_ref_3 NOT refunded
                    ],
                    'code' => CpmsHelper::RESPONSE_SUCCESS,
                    'message' => 'ok',
                ]
            );

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee);

        $this->expectedSideEffect(
            CancelFeeCmd::class,
            ['id' => $feeId],
            (new Result())->addMessage('fee cancelled')
        );

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'transaction' => null, // not bothered what this is
            ],
            'messages' => [
                'Refund transaction created',
                'fee cancelled',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(5, $fee->getFeeTransactions()->count()); // payment_ref_3 not refunded

        $this->assertSame($ft1, $fee->getFeeTransactions()->get(3)->getReversedFeeTransaction());
        $this->assertEquals('-10.00', $fee->getFeeTransactions()->get(3)->getAmount());

        $this->assertSame($ft2, $fee->getFeeTransactions()->get(4)->getReversedFeeTransaction());
        $this->assertEquals('-5.00', $fee->getFeeTransactions()->get(4)->getAmount());

        $transaction = $fee->getFeeTransactions()->get(3)->getTransaction();
        $this->assertSame($transaction, $fee->getFeeTransactions()->get(4)->getTransaction());

        $this->assertSame($this->mapRefData(TransactionEntity::TYPE_REFUND), $transaction->getType());
        $this->assertSame($this->mapRefData(TransactionEntity::STATUS_COMPLETE), $transaction->getStatus());
        $this->assertSame($this->mapRefData(FeeEntity::METHOD_REFUND), $transaction->getPaymentMethod());
        $this->assertEquals(
            'Non over payment refund refund_ref_1, refund_ref_2',
            $transaction->getComment()
        );
        $this->assertEquals($now, $transaction->getCompletedDate());
        $this->assertEquals('bob', $transaction->getProcessedByUser()->getLoginId());
        $this->assertNull($transaction->getReference());
    }

    public function testHandleCommandCpmsResponseException()
    {
        $feeId = 69;

        $command = Cmd::create(['id' => $feeId]);

        $fee = $this->mapReference(FeeEntity::class, $feeId);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($fee);

        $this->mockCpmsService
            ->shouldReceive('batchRefund')
            ->once()
            ->with($fee)
            ->andThrow(new \Dvsa\Olcs\Api\Service\CpmsResponseException('ohnoes'));

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);

        $this->sut->handleCommand($command);
    }
}
