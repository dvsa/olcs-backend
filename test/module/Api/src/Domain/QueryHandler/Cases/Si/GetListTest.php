<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Si\GetList as QueryHandler;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Doctrine\ORM\Query;
use Mockery as m;

/**
 * GetListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp()
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
            ->shouldReceive('fetchList')->with($query, Query::HYDRATE_OBJECT)->once()->andReturn([$mockResult]);

        $expected = [
            'results' => [
                ['foo' => 'bar']
            ],
            'count' => 1,
        ];

        $this->assertSame($expected, $this->sut->handleQuery($query));
    }
}
