<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Si\SiList as QueryHandler;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Doctrine\ORM\Query;
use Mockery as m;

/**
 * SiListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class SiListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('SeriousInfringement', \Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);
        $mockResult = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class);
        $mockResult->shouldReceive('serialize')->with(
            ['siCategoryType']
        )->once()->andReturn(['foo' => 'bar']);

        $this->repoMap['SeriousInfringement']
            ->shouldReceive('fetchList')->with($query, Query::HYDRATE_OBJECT)->once()->andReturn([$mockResult])
            ->shouldReceive('fetchCount')->with($query)->once()->andReturn(1);

        $expected = [
            'result' => [
                ['foo' => 'bar']
            ],
            'count' => 1,
        ];

        $this->assertSame($expected, $this->sut->handleQuery($query));
    }
}
