<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Opposition;

use Dvsa\Olcs\Api\Domain\QueryHandler\Opposition\GetList as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Opposition as Repo;
use Dvsa\Olcs\Transfer\Query\Opposition\GetList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

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
        $this->mockRepo('Opposition', Repo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['QUERY']);

        $mock = \Mockery::mock();
        $mock->shouldReceive('serialize')->with(
            [
                'application',
                'case',
                'grounds',
                'opposer' => [
                    'contactDetails' => [
                        'person'
                    ]
                ]
            ]
        )->once()->andReturn('RESULT');

        $this->repoMap['Opposition']->shouldReceive('fetchList')->with($query, \Doctrine\ORM\Query::HYDRATE_OBJECT)
            ->andReturn([$mock]);
        $this->repoMap['Opposition']->shouldReceive('fetchCount')->with($query)->andReturn('COUNT');

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['RESULT'], $result['result']);
        $this->assertSame('COUNT', $result['count']);
    }
}
