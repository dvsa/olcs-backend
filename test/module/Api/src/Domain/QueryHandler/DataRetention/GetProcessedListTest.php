<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\DataRetention;

use Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention\GetProcessedList;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\DataRetention\GetProcessedList as Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Mockery as m;

/**
 * Class GetProcessedListTest
 */
class GetProcessedListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GetProcessedList();
        $this->mockRepo('DataRetention', Repository\DataRetention::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['dataRetentionRuleId' => 9999, 'startDate' => '2017-05-23', 'endDate' => '2017-09-01']);

        $this->repoMap['DataRetention']->shouldReceive('fetchAllProcessedForRule')
            ->with(9999, m::type(\DateTime::class), m::type(\DateTime::class))
            ->once()
            ->andReturn(['RESULTS']);

        /** @var Result $actual */
        $actual = $this->sut->handleQuery($query);

        $this->assertSame(['count' => 1, 'result' => ['RESULTS']], $actual);
    }
}
