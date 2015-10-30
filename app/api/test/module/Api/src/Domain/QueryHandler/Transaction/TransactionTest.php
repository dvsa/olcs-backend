<?php

/**
 * Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Transaction;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\QueryHandler\Transaction\Transaction as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Transaction as PaymentRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction;
use Dvsa\Olcs\Transfer\Query\Transaction\Transaction as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TransactionTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Transaction', PaymentRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 69]);

        $mockPayment = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');

        $ft1 = m::mock(FeeTransaction::class)
            ->shouldReceive('getFee')
            ->andReturn($this->getMockFee('1', '12.34'))
            ->shouldReceive('getAmount')
            ->andReturn('2.34')
            ->getMock();
        $ft2 = m::mock(FeeTransaction::class)
            ->shouldReceive('getFee')
            ->andReturn($this->getMockFee('2', '23.45'))
            ->shouldReceive('getAmount')
            ->andReturn('3.45')
            ->getMock();

        $feeTransactions = new ArrayCollection([$ft1, $ft2]);

        $mockPayment->shouldReceive('getFeeTransactions')->andReturn($feeTransactions);

        $this->repoMap['Transaction']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockPayment);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        $mockPayment
            ->shouldReceive('serialize')
            ->andReturn(
                [
                    'id' => 99,
                    'foo' => 'bar',
                ]
            );

        $this->assertEquals(
            [
                'id' => 99,
                'foo' => 'bar',
                'fees' => [
                    '1' => [
                        'id' => '1',
                        'amount' => '12.34',
                        'allocatedAmount' => '2.34',
                    ],
                    '2' => [
                        'id' => '2',
                        'amount' => '23.45',
                        'allocatedAmount' => '3.45',
                    ]
                ],
            ],
            $result->serialize()
        );

    }

    public function testHandleQueryRefundMultiplePayments()
    {
        $query = Qry::create(['id' => 69]);

        $mockTransaction = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');

        $mockFee = $this->getMockFee('1', '12.34');

        $ft1 = m::mock(FeeTransaction::class)
            ->shouldReceive('getFee')
            ->andReturn($mockFee)
            ->shouldReceive('getAmount')
            ->andReturn('-2.34')
            ->getMock();
        $ft2 = m::mock(FeeTransaction::class)
            ->shouldReceive('getFee')
            ->andReturn($mockFee)
            ->shouldReceive('getAmount')
            ->andReturn('-10.00')
            ->getMock();

        $feeTransactions = new ArrayCollection([$ft1, $ft2]);

        $mockTransaction->shouldReceive('getFeeTransactions')->andReturn($feeTransactions);

        $this->repoMap['Transaction']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockTransaction);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        $mockTransaction
            ->shouldReceive('serialize')
            ->andReturn(
                [
                    'id' => 99,
                    'foo' => 'bar',
                ]
            );

        $this->assertEquals(
            [
                'id' => 99,
                'foo' => 'bar',
                'fees' => [
                    '1' => [
                        'id' => '1',
                        'amount' => '12.34',
                        'allocatedAmount' => '-12.34',
                    ],
                ],
            ],
            $result->serialize()
        );

    }

    private function getMockFee($id, $amount)
    {

        $fee = m::mock(Fee::class)
            ->shouldReceive('getAmount')
            ->andReturn($amount)
            ->shouldReceive('serialize')
            ->andReturn(
                [
                    'id' => $id,
                    'amount' => $amount,
                ]
            )
            ->getMock();

        return $fee;
    }
}
