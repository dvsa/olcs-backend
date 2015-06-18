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

        $mockFee = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');

        $this->repoMap['Fee']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockFee);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        $mockFee->shouldReceive('serialize')->once()->andReturn(['FEE']);
        $this->assertEquals(['FEE'], $result->serialize());
    }
}
