<?php

/**
 * Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\Payment\Payment;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Payment as PaymentRepo;
use Dvsa\Olcs\Transfer\Query\Payment\Payment as Qry;
use Mockery as m;

/**
 * Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PaymentTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Payment();
        $this->mockRepo('Payment', PaymentRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $paymentRef = 'OLCS-1234-ABCD';
        $paymentId = 99;

        $query = Qry::create(['reference' => $paymentRef]);

        $mockPayment = m::mock()
            ->shouldReceive('jsonSerialize')
            ->andReturn(
                [
                    'id' => $paymentId,
                    'guid' => $paymentRef,
                ]
            )
            ->getMock();

        $mockPayment
            ->shouldReceive('getFeePayments')
            ->andReturn(
                [
                    $this->getMockFeePayment(98),
                    $this->getMockFeePayment(99),
                ]
            );

        $this->repoMap['Payment']
            ->shouldReceive('fetchByReference')
            ->with($paymentRef)
            ->once()
            ->andReturn(
                [
                    'count' => 1,
                    'result' => [
                        $mockPayment
                    ]
                ]
            );

        $expected = [
            'count' => 1,
            'result' => [
                [
                    'id' => $paymentId,
                    'guid' => $paymentRef,
                    'feePayments' => [
                        [
                            'fee' => [
                                'id' => 98,
                            ]
                        ],
                        [
                            'fee' => [
                                'id' => 99,
                            ]
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->sut->handleQuery($query);

        $this->assertEquals($expected, $result);
    }

    private function getMockFeePayment($feeId)
    {
        $mockFee = m::mock();
        $mockFeePayment = m::mock();

        $mockFee
            ->shouldReceive('jsonSerialize')
            ->andReturn(['id' => $feeId]);

        $mockFeePayment
            ->shouldReceive('getFee')
            ->andReturn($mockFee)
            ->shouldReceive('jsonSerialize')
            ->andReturn([]);

        return $mockFeePayment;
    }
}
