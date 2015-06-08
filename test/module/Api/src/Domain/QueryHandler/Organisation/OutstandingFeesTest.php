<?php

/**
 * Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\OutstandingFees;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Transfer\Query\Organisation\OutstandingFees as Qry;
use Mockery as m;

/**
 * Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class OutstandingFeesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new OutstandingFees();
        $this->mockRepo('Organisation', OrganisationRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $organisationId = 69;

        $query = Qry::create(['id' => $organisationId]);

        $mockOrganisation = m::mock()
            ->shouldReceive('getId')
            ->andReturn($organisationId)
            ->shouldReceive('jsonSerialize')
            ->andReturn(
                [
                    'id' => $organisationId,
                    'name' => 'My Org',
                ]
            )
            ->getMock();

        $fees = [
            $this->getMockFee(98, 198),
            $this->getMockFee(99, 199),
        ];

        $this->repoMap['Organisation']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockOrganisation);

        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByOrganisationId')
            ->once()
            ->with($organisationId)
            ->andReturn($fees);

        $expected = [
            'id' => $organisationId,
            'name' => 'My Org',
            'outstandingFees' => [
                [
                    'id' => 98,
                    'feePayments' => [
                        [
                            'payment' => [
                                'id' => 198,
                            ],
                        ]
                    ],
                ],
                [
                    'id' => 99,
                    'feePayments' => [
                        [
                            'payment' => [
                                'id' => 199,
                            ],
                        ]
                    ],
                ],
            ],
        ];

        $result = $this->sut->handleQuery($query);

        $this->assertEquals($expected, $result);
    }

    private function getMockFee($feeId, $paymentId)
    {
        $mockFee = m::mock();
        $mockFeePayment = m::mock();
        $mockPayment = m::mock();

        $mockFee
            ->shouldReceive('getFeePayments')
            ->andReturn([$mockFeePayment])
            ->shouldReceive('jsonSerialize')
            ->andReturn(['id' => $feeId]);

        $mockFeePayment
            ->shouldReceive('getPayment')
            ->andReturn($mockPayment)
            ->shouldReceive('jsonSerialize')
            ->andReturn([]);

        $mockPayment
            ->shouldReceive('jsonSerialize')
            ->andReturn(['id' => $paymentId]);

        return $mockFee;
    }
}
