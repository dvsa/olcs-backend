<?php

namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as Entity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction;
use Dvsa\Olcs\Api\Entity\Fee\Transaction;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Mockery as m;

/**
 * Fee Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class FeeEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->sut = $this->instantiate($this->entityClass);
    }

    /**
     * @param array $feeTransactions
     * @param boolean $expected
     *
     * @dataProvider outstandingPaymentProvider
     */
    public function testHadOutstandingPayment($feeTransactions, $expected)
    {
        $this->sut->setFeeTransactions($feeTransactions);

        $this->assertEquals($expected, $this->sut->hasOutstandingPayment());
    }

    public function outstandingPaymentProvider()
    {
        return [
            'no fee payments' => [
                [],
                false,
            ],
            'one outstanding' => [
                [
                    m::mock()
                        ->shouldReceive('getTransaction')
                        ->andReturn(
                            m::mock()
                                ->shouldReceive('isOutstanding')
                                ->andReturn(true)
                                ->getMock()
                        )
                        ->getMock()
                ],
                true,
            ]
        ];
    }

    /**
     * @param string $accrualRuleId,
     * @param Licence $licence
     * @param DateTime $expected
     *
     * @dataProvider ruleStartDateProvider
     */
    public function testGetRuleStartDate($accrualRuleId, $licence, $expected)
    {
        $feeType = m::mock()
            ->shouldReceive('getAccrualRule')
            ->andReturn((new RefData())->setId($accrualRuleId))
            ->getMock();

        $this->sut->setFeeType($feeType);
        if (!is_null($licence)) {
            $this->sut->setLicence($licence);
        }

        $this->assertEquals($expected, $this->sut->getRuleStartDate());
    }

    public function ruleStartDateProvider()
    {
        $now = new DateTime();

        return [
            'immediate' => [
                Entity::ACCRUAL_RULE_IMMEDIATE,
                null,
                $now,
            ],
            'licence start' => [
                Entity::ACCRUAL_RULE_LICENCE_START,
                m::mock()
                    ->shouldReceive('getInForceDate')
                    ->andReturn('2015-04-03')
                    ->getMock(),
                new DateTime('2015-04-03'),
            ],
            'licence start date missing' => [
                Entity::ACCRUAL_RULE_LICENCE_START,
                m::mock()
                    ->shouldReceive('getInForceDate')
                    ->andReturn(null)
                    ->getMock(),
                null,
            ],
            'continuation' => [
                Entity::ACCRUAL_RULE_CONTINUATION,
                m::mock()
                    ->shouldReceive('getExpiryDate')
                    ->andReturn('2015-04-03')
                    ->getMock(),
                new DateTime('2015-04-04'),
            ],
            'continuation date missing' => [
                Entity::ACCRUAL_RULE_CONTINUATION,
                m::mock()
                    ->shouldReceive('getExpiryDate')
                    ->andReturn(null)
                    ->getMock(),
                null,
            ],
            'invalid' => [
                'foo',
                null,
                null,
            ],
        ];
    }

    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider allowEditProvider
     */
    public function testAllowEdit($status, $expected)
    {
        $feeStatus = m::mock(RefData::class)->makePartial();
        $feeStatus->setId($status);
        $this->sut->setFeeStatus($feeStatus);

        $this->assertEquals($expected, $this->sut->allowEdit());
    }

    public function allowEditProvider()
    {
        return [
            [Entity::STATUS_PAID, false],
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_OUTSTANDING, true],
            ['invalid', true],
        ];
    }

    public function testCompatibilityGetMethods()
    {
        $this->assertNull($this->sut->getReceivedAmount());
        $this->assertNull($this->sut->getReceiptNo());
        $this->assertNull($this->sut->getReceivedDate());
        $this->assertNull($this->sut->getPaymentMethod());
        $this->assertNull($this->sut->getProcessedBy());
        $this->assertNull($this->sut->getPayer());
        $this->assertNull($this->sut->getSlipNo());
        $this->assertNull($this->sut->getChequePoNumber());
        $this->assertNull($this->sut->getWaiveReason());

        $ft1 = $this->getStubFeeTransaction('1234.56', '2015-09-01', '2015-09-02 12:34:56');
        $ft2 = $this->getStubFeeTransaction('1234.56', '2015-08-01', '2015-09-02 12:34:56');
        $ft3 = $this->getStubFeeTransaction('1234.56', '2015-09-01', '2015-09-02 12:34:55');
        $ft4 = $this->getStubFeeTransaction(
            '234.56',
            '2015-09-03',
            '2015-09-03 12:34:55',
            Transaction::STATUS_OUTSTANDING
        );

        $transaction = $ft1->getTransaction();

        $paymentMethod = m::mock(RefData::class);
        $transaction->setPaymentMethod($paymentMethod);

        $user = m::mock()
            ->shouldReceive('getLoginId')
            ->andReturn('bob')
            ->getMock();
        $transaction->setProcessedByUser($user);

        $transaction->setPayerName('payer');

        $transaction->setPayingInSlipNumber('12345');

        $transaction->setChequePoNumber('23456');

        $transaction->setComment('reason');

        $transaction->setReference('OLCS-1234');

        $this->sut->getFeeTransactions()->add($ft1);
        $this->sut->getFeeTransactions()->add($ft2);
        $this->sut->getFeeTransactions()->add($ft3);
        $this->sut->getFeeTransactions()->add($ft4);

        $this->assertEquals('1234.56', $this->sut->getReceivedAmount());
        $this->assertEquals('2015-09-01', $this->sut->getReceivedDate()->format('Y-m-d'));
        $this->assertEquals($paymentMethod, $this->sut->getPaymentMethod());
        $this->assertEquals('bob', $this->sut->getProcessedBy());
        $this->assertEquals('payer', $this->sut->getPayer());
        $this->assertEquals('12345', $this->sut->getSlipNo());
        $this->assertEquals('23456', $this->sut->getChequePoNumber());
        $this->assertEquals('reason', $this->sut->getWaiveReason());
        $this->assertEquals('OLCS-1234', $this->sut->getReceiptNo());
    }

    private function getStubFeeTransaction(
        $amount,
        $completedDate,
        $createdOn,
        $statusId = Transaction::STATUS_COMPLETE
    ) {
        $transaction = new Transaction();
        $feeTransaction = new FeeTransaction();
        $feeTransaction->setTransaction($transaction);
        $feeTransaction->setAmount($amount);
        $completed = new \DateTime($completedDate);
        $transaction->setCompletedDate($completed);
        $created = new \DateTime($createdOn);
        $transaction->setCreatedOn($created);
        $status = (new RefData())->setId($statusId);
        $transaction->setStatus($status);

        return $feeTransaction;
    }

    /**
     * @dataProvider outstandingWaiveTransactionProvider
     */
    public function testGetOutstandingWaiveTransaction(array $feeTransactions, $expected)
    {
        $this->sut->setFeeTransactions(new ArrayCollection($feeTransactions));

        $this->assertEquals($expected, $this->sut->getOutstandingWaiveTransaction());
    }

    public function outstandingWaiveTransactionProvider()
    {
        $transaction1 = m::mock(Transaction::class);
        $transaction1->shouldReceive('getStatus->getId')
            ->andReturn(Transaction::STATUS_CANCELLED);
        $transaction1->shouldReceive('getType->getId')
            ->andReturn(Transaction::TYPE_WAIVE);

        $transaction2 = m::mock(Transaction::class);
        $transaction2->shouldReceive('getStatus->getId')
            ->andReturn(Transaction::STATUS_OUTSTANDING);
        $transaction2->shouldReceive('getType->getId')
            ->andReturn(Transaction::TYPE_WAIVE);

        $feeTransaction1 = m::mock(FeeTransaction::class)
            ->shouldReceive('getTransaction')
            ->andReturn($transaction1)
            ->getMock();
        $feeTransaction2 = m::mock(FeeTransaction::class)
            ->shouldReceive('getTransaction')
            ->andReturn($transaction2)
            ->getMock();

        return [
            'none' => [
                [],
                null,
            ],
            'valid' => [
                [$feeTransaction1, $feeTransaction2],
                $transaction2,
            ],
        ];
    }

    /**
     * @dataProvider outstandingAmountProvider
     */
    public function testGetOutstandingAmount($feeAmount, $feeTransactions, $expected)
    {
        $this->sut->setAmount($feeAmount);
        $this->sut->setFeeTransactions($feeTransactions);
        $this->assertEquals($expected, $this->sut->getOutstandingAmount());
    }

    public function outstandingAmountProvider()
    {
        return [
            'no transactions' => [
                '1234.56',
                new ArrayCollection(),
                '1234.56',
            ],
            'one complete transaction' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('1234.56', '2015-09-01', '2015-09-02'),
                    ]
                ),
                '0.00',
            ],
            'one pending transaction' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction(
                            '1234.56',
                            '2015-09-01',
                            '2015-09-02 12:34:56',
                            Transaction::STATUS_OUTSTANDING
                        ),
                    ]
                ),
                '1234.56',
            ],
            'two complete one refund one pending' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('1000', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction('300', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction('-100', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction(
                            '34.56',
                            '2015-09-01',
                            '2015-09-02',
                            Transaction::STATUS_OUTSTANDING
                        ),
                    ]
                ),
                '34.56',
            ],
            'one overpayment' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('2000', '2015-09-01', '2015-09-02'),
                    ]
                ),
                '-765.44',
            ],
        ];
    }
}
