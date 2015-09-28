<?php

/**
 * Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\Fee\Fee as QueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Transfer\Query\Fee\Fee as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 69]);

        $mockFee = m::mock(FeeEntity::class);

        $this->repoMap['Fee']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockFee);

        $mockFee
            ->shouldReceive('allowEdit')
            ->once()
            ->andReturn(true)
            ->shouldReceive('getLatestPaymentRef')
            ->once()
            ->andReturn('TestReceiptNo')
            ->shouldReceive('getPaymentMethod')
            ->once()
            ->andReturn('TestPaymentMethod')
            ->shouldReceive('getProcessedBy')
            ->once()
            ->andReturn('TestProcessedBy')
            ->shouldReceive('getPayer')
            ->once()
            ->andReturn('TestPayer')
            ->shouldReceive('getSlipNo')
            ->once()
            ->andReturn('TestSlipNo')
            ->shouldReceive('getChequePoNumber')
            ->once()
            ->andReturn('TestChequePoNumber')
            ->shouldReceive('getWaiveReason')
            ->once()
            ->andReturn('TestWaiveReason')
            ->shouldReceive('getOutstandingAmount')
            ->once()
            ->andReturn('TestOutstanding')
            ->shouldReceive('getOutstandingWaiveTransaction')
            ->andReturn(null);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        $mockFee
            ->shouldReceive('serialize')
            ->once()->andReturn(
                [
                    'id' => 69,
                ]
            );

        $expected = [
            'id' => '69',
            'allowEdit' => true,
            'receiptNo' => 'TestReceiptNo',
            'paymentMethod' => 'TestPaymentMethod',
            'processedBy' => 'TestProcessedBy',
            'payer' => 'TestPayer',
            'slipNo' => 'TestSlipNo',
            'chequePoNumber' => 'TestChequePoNumber',
            'waiveReason' => 'TestWaiveReason',
            'outstanding' => 'TestOutstanding',
            'hasOutstandingWaiveTransaction' => false,
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}
