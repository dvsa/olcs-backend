<?php

/**
 * Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Payment;

use Dvsa\Olcs\Api\Domain\QueryHandler\Payment\Payment as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Payment as PaymentRepo;
use Dvsa\Olcs\Transfer\Query\Payment\Payment as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
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
        $this->sut = new QueryHandler();
        $this->mockRepo('Payment', PaymentRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 69]);

        $mockPayment = m::mock();

        $this->repoMap['Payment']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockPayment);

        $result = $this->sut->handleQuery($query);

        $this->assertSame($mockPayment, $result);
    }
}
