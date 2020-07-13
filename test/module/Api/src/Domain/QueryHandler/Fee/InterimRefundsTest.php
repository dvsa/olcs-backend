<?php

/**
 * Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\Fee\InterimRefunds;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\Fee;
use Dvsa\Olcs\Transfer\Query\Fee\InterimRefunds as InterimRefundsQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * FeeType Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class InterimRefundsTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new InterimRefunds();
        $this->mockRepo('Fee', Fee::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = InterimRefundsQuery::create([
            'id' => 69,
            'startDate' => '2019-06-01',
            'endDate' => '2019-06-31',
            'sort' => 'id',
            'order' => 'DESC',
            'trafficArea' => [
                'a',
                'b'
            ]
        ]);

        $expected = [
            m::mock(\Dvsa\Olcs\Api\Entity\Fee\Fee::class)
                ->shouldReceive('serialize')->once()->andReturn('foo')->getMock()
        ];

        $this->repoMap['Fee']
            ->shouldReceive('fetchInterimRefunds')
            ->with(
                $query->getStartDate(),
                $query->getEndDate(),
                $query->getSort(),
                $query->getOrder(),
                $query->getTrafficAreas()
            )
            ->once()
            ->andReturn($expected);

        $this->assertEquals([
            'count' => 1,
            'results' => ['foo']
        ], $this->sut->handleQuery($query));
    }
}
