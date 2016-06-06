<?php

/**
 * History Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Processing;

use Dvsa\Olcs\Api\Domain\QueryHandler\Processing\History;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\Processing\History as Qry;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;

/**
 * History Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class HistoryTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new History();
        $this->mockRepo('EventHistory', Repository\EventHistory::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $data = [
            'licence' => 1
        ];

        $query = Qry::create($data);

        $mockEventHistory = m::mock(BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->andReturn(['foo' => 'bar'])
            ->once()
            ->getMock();

        $this->repoMap['EventHistory']
            ->shouldReceive('disableSoftDeleteable')
            ->once()
            ->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn([$mockEventHistory])
            ->once()
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(5)
            ->once();

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [['foo' => 'bar']],
            'count' => 5
        ];

        $this->assertEquals($expected, $result);
    }
}
