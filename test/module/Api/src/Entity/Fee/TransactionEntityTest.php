<?php

namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Mockery as m;

/**
 * Transaction Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class TransactionEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetCollections()
    {
        $sut = $this->instantiate($this->entityClass);

        $feeTransactions = $sut->getFeeTransactions();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $feeTransactions);
    }

    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider isOutstandingProvider
     */
    public function testIsOutstanding($status, $expected)
    {
        $sut = new $this->entityClass;

        $status = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->once()
            ->andReturn($status)
            ->getMock();

        $sut->setStatus($status);

        $this->assertEquals($expected, $sut->isOutstanding());
    }

    /**
     * @return array
     */
    public function isOutstandingProvider()
    {
        return [
            [Entity::STATUS_OUTSTANDING, true],
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_FAILED, false],
            [Entity::STATUS_PAID, false],
            ['invalid', false],
        ];
    }


    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider isPaidProvider
     */
    public function testIsPaid($status, $expected)
    {
        $sut = new $this->entityClass;

        $status = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->once()
            ->andReturn($status)
            ->getMock();

        $sut->setStatus($status);

        $this->assertEquals($expected, $sut->isPaid());
    }

    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider isPaidProvider
     */
    public function testIsComplete($status, $expected)
    {
        $sut = new $this->entityClass;

        $status = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->once()
            ->andReturn($status)
            ->getMock();

        $sut->setStatus($status);

        $this->assertEquals($expected, $sut->isComplete());
    }

    /**
     * @return array
     */
    public function isPaidProvider()
    {
        return [
            [Entity::STATUS_OUTSTANDING, false],
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_FAILED, false],
            [Entity::STATUS_PAID, true],
            [Entity::STATUS_COMPLETE, true],
            ['invalid', false],
        ];
    }

    /**
     * implicitly tests getTotalAmount() as well
     */
    public function testGetCalculatedBundleValues()
    {
        $sut = $this->instantiate($this->entityClass);

        $ft1 = m::mock(FeeTransaction::class)
            ->shouldReceive('getAmount')
            ->andReturn('12.34')
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(false)
            ->getMock();
        $ft2 = m::mock(FeeTransaction::class)
            ->shouldReceive('getAmount')
            ->andReturn('23.45')
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(false)
            ->getMock();

        $feeTransactions = new ArrayCollection([$ft1, $ft2]);
        $sut->setFeeTransactions($feeTransactions);

        $sut->setType(new RefData(Entity::TYPE_PAYMENT));
        $sut->setPaymentMethod(new RefData(Fee::METHOD_CASH));
        $sut->setStatus(new RefData(Entity::STATUS_COMPLETE));

        $this->assertEquals(
            [
                'amount' => '35.79',
                'displayReversalOption' => true,
                'canReverse' => true,
            ],
            $sut->getCalculatedBundleValues()
        );
    }

    /**
     * @param RefData $type transaction type
     * @param RefData $paymentMethod payment method
     * @dataProvider displayReversalOptionProvider
     */
    public function testDisplayReversalOption($type, $paymentMethod, $status, $expected)
    {
        $sut = $this->instantiate($this->entityClass);

        $sut->setType($type);
        $sut->setPaymentMethod($paymentMethod);
        $sut->setStatus($status);

        $this->assertSame($expected, $sut->displayReversalOption());
    }

    public function displayReversalOptionProvider()
    {
        return [
            'cheque payment' => [
                new RefData(Entity::TYPE_PAYMENT),
                new RefData(Fee::METHOD_CHEQUE),
                new RefData(Entity::STATUS_COMPLETE),
                true,
            ],
            'digital card payment' => [
                new RefData(Entity::TYPE_PAYMENT),
                new RefData(Fee::METHOD_CARD_ONLINE),
                new RefData(Entity::STATUS_COMPLETE),
                true,
            ],
            'assisted digital card payment' => [
                new RefData(Entity::TYPE_PAYMENT),
                new RefData(Fee::METHOD_CARD_OFFLINE),
                new RefData(Entity::STATUS_COMPLETE),
                true,
            ],
            'cash payment' => [
                new RefData(Entity::TYPE_PAYMENT),
                new RefData(Fee::METHOD_CASH),
                new RefData(Entity::STATUS_COMPLETE),
                true,
            ],
            'PO payment' => [
                new RefData(Entity::TYPE_PAYMENT),
                new RefData(Fee::METHOD_POSTAL_ORDER),
                new RefData(Entity::STATUS_COMPLETE),
                true,
            ],
            'waive' => [
                new RefData(Entity::TYPE_WAIVE),
                null,
                null,
                false,
            ],
            'refund' => [
                new RefData(Entity::TYPE_REFUND),
                null,
                null,
                false,
            ],
            'reversal' => [
                new RefData(Entity::TYPE_REVERSAL),
                null,
                null,
                false,
            ],
            'failed card payment' => [
                new RefData(Entity::TYPE_PAYMENT),
                new RefData(Fee::METHOD_CARD_ONLINE),
                new RefData(Entity::STATUS_FAILED),
                false,
            ],
        ];
    }

    /**
     * @param array $feeTransactions
     * @dataProvider canReverseProvider
     */
    public function testCanReverse($feeTransactions, $status, $expected)
    {
        $sut = $this->instantiate($this->entityClass);
        $sut->setType(new RefData(Entity::TYPE_PAYMENT));
        $sut->setPaymentMethod(new RefData(Fee::METHOD_CHEQUE));
        $sut->setStatus($status);

        $sut->setFeeTransactions(new ArrayCollection($feeTransactions));

        $this->assertSame($expected, $sut->canReverse());
    }

    public function canReverseProvider()
    {
        return [
            'no fee transactions' => [
                [],
                new RefData(Entity::STATUS_COMPLETE),
                true,
            ],
            'one refunded fee transaction' => [
                [
                    m::mock(FeeTransaction::class)
                        ->shouldReceive('isRefundedOrReversed')
                        ->andReturn(true)
                        ->getMock(),
                ],
                new RefData(Entity::STATUS_COMPLETE),
                false,
            ],
            'one other fee transaction' => [
                [
                    m::mock(FeeTransaction::class)
                        ->shouldReceive('isRefundedOrReversed')
                        ->andReturn(false)
                        ->getMock(),
                ],
                new RefData(Entity::STATUS_COMPLETE),
                true,
            ],
            'mix of fee transactions' => [
                [
                    m::mock(FeeTransaction::class)
                        ->shouldReceive('isRefundedOrReversed')
                        ->andReturn(false)
                        ->getMock(),
                    m::mock(FeeTransaction::class)
                        ->shouldReceive('isRefundedOrReversed')
                        ->andReturn(true)
                        ->getMock(),
                ],
                new RefData(Entity::STATUS_COMPLETE),
                false,
            ]
        ];
    }
}
