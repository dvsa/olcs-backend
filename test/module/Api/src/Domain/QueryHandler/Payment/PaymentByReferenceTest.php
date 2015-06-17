<?php

/**
 * Payment by reference Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Payment;

use Dvsa\Olcs\Api\Domain\QueryHandler\Payment\PaymentByReference as QueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\Payment as PaymentRepo;
use Dvsa\Olcs\Transfer\Query\Payment\PaymentByReference as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Payment by reference Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PaymentByReferenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Payment', PaymentRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $paymentRef = 'OLCS-1234-ABCD';

        $query = Qry::create(['reference' => $paymentRef]);

        $mockPayment = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface')
            ->shouldReceive('serialize')
            ->getMock();

        $this->repoMap['Payment']
            ->shouldReceive('fetchByReference')
            ->with($paymentRef)
            ->once()
            ->andReturn($mockPayment);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);
    }
}
