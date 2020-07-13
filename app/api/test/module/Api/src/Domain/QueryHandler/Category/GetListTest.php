<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Category;

use Dvsa\Olcs\Api\Domain\QueryHandler\Category\GetList as QueryHandler;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * GetListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Category', \Dvsa\Olcs\Api\Domain\Repository\Category::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = m::mock(\Dvsa\Olcs\Transfer\Query\QueryInterface::class);
        $mockResult = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class);
        $mockResult->shouldReceive('serialize')->with([])->once()->andReturn(['foo' => 'bar']);

        $this->repoMap['Category']
            ->shouldReceive('fetchList')->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)->once()
            ->andReturn([$mockResult]);

        $expected = [
            'result' => [
                ['foo' => 'bar']
            ],
            'count' => 1,
        ];

        $this->assertSame($expected, $this->sut->handleQuery($query));
    }
}
